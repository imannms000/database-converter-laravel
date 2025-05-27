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

test('it can ignore specified tables during conversion.', function () {
    config()->set('database-converter-laravel.ignore_tables', ['projects']);

    Artisan::call('db:convert', [
        'sourceConnection' => 'source',
        'targetConnection' => 'target',
    ]);

    expect(DB::connection('target')->table('projects')->count())->toBe(0);

    expect(DB::connection('target')->table('environments')->count())->toBe(DB::connection('source')->table('environments')->count());
    expect(DB::connection('target')->table('deployments')->count())->toBe(DB::connection('source')->table('deployments')->count());
})->group('FeatureTest');

test('it always ignores migrations table regardless of configuration.', function () {
    config()->set('database-converter-laravel.ignore_tables', []);

    if (!DB::connection('source')->getSchemaBuilder()->hasTable('migrations')) {
        DB::connection('source')->statement('CREATE TABLE migrations (id INTEGER PRIMARY KEY, migration VARCHAR(255), batch INTEGER)');
    }

    DB::connection('source')->table('migrations')->insert([
        'migration' => '2023_01_01_000000_create_test_table',
        'batch' => 1
    ]);

    if (!DB::connection('target')->getSchemaBuilder()->hasTable('migrations')) {
        DB::connection('target')->statement('CREATE TABLE migrations (id INTEGER PRIMARY KEY, migration VARCHAR(255), batch INTEGER)');
    }

    $initialCount = DB::connection('target')->table('migrations')->count();

    Artisan::call('db:convert', [
        'sourceConnection' => 'source',
        'targetConnection' => 'target',
    ]);

    expect(DB::connection('target')->table('migrations')->count())->toBe($initialCount);
    expect(DB::connection('source')->table('migrations')->count())->toBeGreaterThan(0);
})->group('FeatureTest');
