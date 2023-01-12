<?php

namespace Spatie\DeletedModels\Models;

use Illuminate\Database\Eloquent\Model;

class DeletedModel extends Model
{
    public $casts = [
        'values' => 'array',
    ];

    public $guarded = [];

    public function restore(): ?Model
    {
    }
}
