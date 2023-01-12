<?php

namespace Spatie\DeletedModels;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class DeletedModelsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-deleted-models')
            ->hasConfigFile()
            ->hasMigration('create_deleted_models_table');
    }
}
