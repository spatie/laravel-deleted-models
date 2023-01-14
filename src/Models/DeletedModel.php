<?php

namespace Spatie\DeletedModels\Models;

use Exception;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Spatie\DeletedModels\Events\DeletedModelRestored;
use Spatie\DeletedModels\Events\RestoringDeletedModel;
use Spatie\DeletedModels\Exceptions\CouldNotRestoreModel;

/**
 * @property string $model
 * @property array $values
 */
class DeletedModel extends Model
{
    use MassPrunable;

    public $casts = [
        'values' => 'array',
    ];

    public $guarded = [];

    public function restore(): Model
    {
        event(new RestoringDeletedModel($this));

        try {
            $restoredModel = $this->makeRestoredModel();

            $this->beforeSavingRestoredModel();

            $this->saveRestoredModel($restoredModel);

            $this->afterSavingRestoredModel();
        } catch (Exception $exception) {
            $this->handleExceptionDuringRestore($exception);

            throw $exception;
        }

        $this->deleteDeletedModel();

        event(new DeletedModelRestored($this, $restoredModel));

        return $restoredModel;
    }

    /** @return class-string<Model> */
    protected function getModelClass(): string
    {
        return Relation::getMorphedModel($this->model) ?? $this->model;
    }

    public function makeRestoredModel(): Model
    {
        $modelClass = $this->getModelClass();

        $model = (new $modelClass)->fill($this->values);

        return $model;
    }

    public function beforeSavingRestoredModel(): void
    {
    }

    protected function saveRestoredModel(Model $model): void
    {
        $model->save();
    }

    public function afterSavingRestoredModel(): void
    {
    }

    protected function deleteDeletedModel(): void
    {
        $this->delete();
    }

    protected function handleExceptionDuringRestore(Exception $exception)
    {
        throw CouldNotRestoreModel::make($this, $exception);
    }

    public function value(string $key = null): mixed
    {
        return Arr::get($this->values, $key);
    }

    protected function massPrunable()
    {
        return static::where('created_at', '<=', config('deleted-models.prune_after_days'));
    }
}
