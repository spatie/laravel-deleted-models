<?php

namespace Spatie\DeletedRecordsKeeper;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\DeletedRecordsKeeper\Commands\DeletedRecordsKeeperCommand;

class DeletedRecordsKeeperServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-deleted-records-keeper')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-deleted-records-keeper_table')
            ->hasCommand(DeletedRecordsKeeperCommand::class);
    }
}
