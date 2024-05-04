<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use RichanFongdasen\DatabaseConverter\Tests\Fixtures\Models\Deployment;
use RichanFongdasen\DatabaseConverter\Tests\Fixtures\Models\Environment;
use RichanFongdasen\DatabaseConverter\Tests\Fixtures\Models\Project;

beforeEach(function () {
    migrateDatabase('source');
    migrateDatabase('target');

    config()->set('database.default', 'source');

    Project::factory()->count(5)->create()->each(function (Project $project) {
        Environment::factory()->count(3)->create([
            'project_id' => $project->getKey(),
        ])->each(function (Environment $environment) {
            Deployment::factory()->count(10)->create([
                'environment_id' => $environment->getKey(),
            ]);
        });
    });
});

test('it can convert the database as expected.', function () {
    Artisan::call('db:convert', [
        'sourceConnection' => 'source',
        'targetConnection' => 'target',
    ]);

    collect(
        DB::connection('source')
            ->getSchemaBuilder()
            ->getTableListing()
    )->each(function (string $tableName) {
        expect(DB::connection('target')->getSchemaBuilder()->hasTable($tableName))->toBeTrue()
            ->and(DB::connection('target')->table($tableName)->count())->toBe(DB::connection('source')->table($tableName)->count());

        $sourceRecords = DB::connection('source')->table($tableName)->get();
        $targetRecords = DB::connection('target')->table($tableName)->get();

        $sourceRecords->each(function ($sourceRecord) use ($targetRecords) {
            $targetRecord = $targetRecords->firstWhere('id', $sourceRecord->id);
            expect($targetRecord)->not->toBeNull();
            expect((array) $sourceRecord)->toEqual((array) $targetRecord);
        });
    });
})->group('FeatureTest');
