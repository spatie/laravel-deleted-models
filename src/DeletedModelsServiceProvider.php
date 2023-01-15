<?php

namespace Spatie\DeletedModels;

use Spatie\DeletedModels\Exceptions\InvalidDeletedModel;
use Spatie\DeletedModels\Models\DeletedModel;
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

    public function packageBooted()
    {
        $this->guardAgainstInvalidDeletedModel();
    }

    public function guardAgainstInvalidDeletedModel()
    {
        $modelClassName = config('deleted-models.model');

        if (! is_a($modelClassName, DeletedModel::class, true)) {
            throw InvalidDeletedModel::create($modelClassName);
        }
    }
}
