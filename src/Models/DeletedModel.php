<?php

namespace Spatie\DeletedModels\Models;

use Carbon\Carbon;
use Closure;
use Exception;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Spatie\DeletedModels\Events\DeletedModelRestoredEvent;
use Spatie\DeletedModels\Events\RestoringDeletedModelEvent;
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

    public $table = 'deleted_models';

    public function restore(Closure $beforeSaving = null): Model
    {
        DB::beginTransaction();

        event(new RestoringDeletedModelEvent($this));

        try {
            $restoredModel = $this->makeRestoredModel();

            $this->beforeSavingRestoredModel();

            if ($beforeSaving) {
                $beforeSaving($restoredModel, $this);
            }

            $this->saveRestoredModel($restoredModel);

            $this->afterSavingRestoredModel();

            event(new DeletedModelRestoredEvent($this, $restoredModel));

            $this->deleteDeletedModel();

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();

            $this->handleExceptionDuringRestore($exception);
        }

        return $restoredModel;
    }

    public function restoreQuietly(): Model
    {
        return self::withoutEvents(fn () => $this->restore());
    }

    /** @return class-string<Model> */
    protected function getModelClass(): string
    {
        return Relation::getMorphedModel($this->model) ?? $this->model;
    }

    public function makeRestoredModel(): Model
    {
        $modelClass = $this->getModelClass();

        return (new $modelClass)->forceFill($this->values);
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

    protected function prunable()
    {
        $days = config('deleted-models.prune_after_days');

        return static::where('created_at', '<=', Carbon::now()->subDays($days)->format('Y-m-d H:i:s'));
    }
}
