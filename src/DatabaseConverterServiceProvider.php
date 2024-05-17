<?php

declare(strict_types=1);

namespace RichanFongdasen\DatabaseConverter;

use RichanFongdasen\DatabaseConverter\Commands\DatabaseConverterCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class DatabaseConverterServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('database-converter-laravel')
            ->hasConfigFile()
            ->hasCommand(DatabaseConverterCommand::class);
    }
}
