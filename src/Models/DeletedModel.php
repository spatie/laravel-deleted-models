<?php

namespace Spatie\DeletedModels\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class DeletedModel extends Model
{
    public $casts = [
        'values' => 'array',
    ];

    public $guarded = [];

    public function restore(): ?Model
    {
        $modelClass = $this->getModelClass();

        $restoredModel = $this->makeRestoredModel($modelClass);

        $this->persistRestoredModel($restoredModel);

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
        $model = (new $modelClass)
            ->fill($this->values);
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
}
