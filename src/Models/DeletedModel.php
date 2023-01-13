<?php

namespace Spatie\DeletedModels\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Spatie\DeletedModels\Exceptions\CouldNotRestoreModel;

class DeletedModel extends Model
{
    public $casts = [
        'values' => 'array',
    ];

    public $guarded = [];

    public function restore(): ?Model
    {
        $modelClass = $this->getModelClass();

        try {
            $restoredModel = $this->makeRestoredModel($modelClass);

            $this->persistRestoredModel($restoredModel);
        } catch (Exception $exception) {
            $this->handleExceptionDuringRestore($exception);
        }


        $this->deleteDeletedModel();

        return $restoredModel;
    }

    /**
     * @return class-string<Model>
     */
    protected function getModelClass(): string
    {
        return Relation::getMorphedModel($this->model) ?? $this->model;

    }

    /**
     * @param class-string<Model> $modelClass
     *
     * @return Model
     */
    protected function makeRestoredModel(string $modelClass): mixed
    {
        $model = (new $modelClass)->fill($this->values);

        return $model;
    }

    protected function persistRestoredModel(Model $model): void
    {
        $model->save();
    }

    protected function deleteDeletedModel(): void
    {
        $this->delete();
    }

    protected function handleExceptionDuringRestore(Exception $exception)
    {
        throw CouldNotRestoreModel::make($this, $exception);
    }
}
