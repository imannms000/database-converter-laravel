<?php

use Illuminate\Support\Facades\Artisan;
use RichanFongdasen\DatabaseConverter\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

function migrateDatabase(string $connection): void
{
    Artisan::call('migrate:fresh', [
        '--database' => $connection,
        '--path'     => __DIR__ . '/Fixtures/Migrations',
        '--realpath' => 'true',
    ]);
}
