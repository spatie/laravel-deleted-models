<?php

namespace Spatie\DeletedModels\Models;

use Exception;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Spatie\DeletedModels\Events\DeletedModelRestored;
use Spatie\DeletedModels\Events\RestoringDeletedModel;
use Spatie\DeletedModels\Exceptions\CouldNotRestoreModel;

class DeletedModel extends Model
{
    use MassPrunable;

    public $casts = [
        'values' => 'array',
    ];

    public $guarded = [];

    public function restore(): ?Model
    {
        event(new RestoringDeletedModel($this));

        $modelClass = $this->getModelClass();

        try {
            $restoredModel = $this->makeRestoredModel($modelClass);

            $this->beforeSavingRestoredModel();

            $this->persistRestoredModel($restoredModel);

            $this->afterSavingRestoredModel();
        } catch (Exception $exception) {
            $this->handleExceptionDuringRestore($exception);
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

    /**
     * @param  class-string<Model>  $modelClass
     *
     * @return Model
     */
    protected function makeRestoredModel(string $modelClass): mixed
    {
        $model = (new $modelClass)->fill($this->values);

        return $model;
    }

    public function beforeSavingRestoredModel(): void
    {

    }

    protected function persistRestoredModel(Model $model): void
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

    protected function massPrunable()
    {
        return static::where('created_at', '<=', config('deleted-models.prune_after_days'));
    }
}
