<?php

namespace Finller\LaravelMedia;

use Finller\LaravelMedia\Commands\LaravelMediaCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelMediaServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-media')
            ->hasConfigFile()
            ->hasMigration('1_create_media_table')
            ->hasMigration('2_add_columns_to_media_table')
            ->hasCommand(LaravelMediaCommand::class);
    }
}
