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
        /** @var class-string<Model> $modelClass */
        $modelClass = Relation::getMorphedModel($this->model) ?? $this->model;

        $model = (new $modelClass)
            ->fill($this->values);

        $model->save();

        $this->delete();

        return $model;
    }
}
