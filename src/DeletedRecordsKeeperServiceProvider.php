<?php

namespace Spatie\DeletedRecordsKeeper;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class DeletedRecordsKeeperServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-deleted-records-keeper')
            ->hasConfigFile()
            ->hasMigration('create_deleted_models_table');
    }
}
