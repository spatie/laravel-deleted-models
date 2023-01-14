<?php

namespace Spatie\DeletedModels\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\DeletedModels\Exceptions\NoModelFoundToRestore;
use Spatie\DeletedModels\Models\DeletedModel;

/** @mixin \Illuminate\Database\Eloquent\Model */
trait KeepsDeletedModels
{
    protected bool $shouldKeep = true;

    public static function bootKeepsDeletedModels(): void
    {
        static::deleted(function (Model $model) {
            if (! $model->shouldKeep) {
                return;
            }

            if (! $model->shouldKeep()) {
                return;
            }

            /** @var class-string<DeletedModel> $deletedModelClass */
            $deletedModelClass = config('deleted-models.model');

            $deletedModelClass::create([
                'key' => $model->getKey(),
                'model' => $model->getMorphClass(),
                'values' => $model->attributesToKeep(),
            ]);
        });
    }

    public function shouldKeep(): bool
    {
        return true;
    }

    public function attributesToKeep(): array
    {
        $hiddenAttributes = $this->getHidden();

        /*
         * Avoid changing original instance
         */
        $cloned = clone $this;

        $cloned->makeVisible($hiddenAttributes);

        return $cloned->toArray();
    }

    public function deleteWithoutKeeping()
    {
        $this->shouldKeep = false;

        $this->delete();

        return tap($this->delete(), fn () => $this->shouldKeep = true);
    }

    public static function deletedModels(): Builder
    {
        $model = (new self)->getMorphClass();

        return DeletedModel::query()->where('model', $model);
    }

    public static function restore(mixed $key): Model
    {
        $deletedModel = self::findDeletedModelToRestore($key);

        self::beforeRestoringModel($deletedModel);

        $restoredModel = $deletedModel->restore();

        self::afterRestoringModel($restoredModel, $deletedModel);

        return $restoredModel;
    }

    public static function restoreQuietly(mixed $key): Model
    {
        return static::withoutEvents(fn () => static::restore($key));
    }

    public static function makeRestored(mixed $key): ?Model
    {
        $deletedModel = null;

        try {
            $deletedModel = self::findDeletedModelToRestore($key);
        } catch (NoModelFoundToRestore) {
            // do nothing
        }

        if (! $deletedModel) {
            return null;
        }

        self::beforeRestoringModel($deletedModel);

        $restoredModel = $deletedModel->makeRestoredModel();

        self::afterRestoringModel($restoredModel, $deletedModel);

        return $restoredModel;
    }

    public static function beforeRestoringModel(DeletedModel $deletedModel): void
    {
    }

    public static function afterRestoringModel(
        Model $restoredMode,
        DeletedModel $deletedModel
    ): void {
    }

    protected static function findDeletedModelToRestore(mixed $key): DeletedModel
    {
        $deletedModel = static::deletedModels()->where('key', $key)->first();

        if (! $deletedModel) {
            throw NoModelFoundToRestore::make(static::class, $key);
        }

        return $deletedModel;
    }
}
