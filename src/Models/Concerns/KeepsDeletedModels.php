<?php

namespace Spatie\DeletedRecordsKeeper\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Spatie\DeletedRecordsKeeper\Models\DeletedModel;

/** @mixin \Illuminate\Database\Eloquent\Model */
trait KeepsDeletedModels
{
    public static function bootKeepsDeletedModels(): void
    {
        static::deleted(function(Model $model) {
            DeletedModel::create([
                'key' => $model->getKey(),
                'model' => $model->getMorphClass(),
                'values' => $model->prepareForKeeping(),
            ]);
        });
    }

    public function prepareForKeeping(): array
    {
        return $this->toArray();
    }
}
