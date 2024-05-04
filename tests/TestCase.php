<?php

namespace RichanFongdasen\DatabaseConverter\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use RichanFongdasen\DatabaseConverter\DatabaseConverterServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'RichanFongdasen\\DatabaseConverter\\Tests\\Fixtures\\Factories\\' . class_basename($modelName) . 'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            DatabaseConverterServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        config()->set('database.connections.source', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
        ]);
        config()->set('database.connections.target', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
        ]);

        /*
        $migration = include __DIR__.'/../database/migrations/create_database-converter-laravel_table.php.stub';
        $migration->up();
        */
    }
}
