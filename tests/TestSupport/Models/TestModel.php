<?php

namespace Spatie\DeletedModels\Tests\TestSupport\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\DeletedModels\Models\Concerns\KeepsDeletedModels;

class TestModel extends Model
{
    public $fillable = [
        'name',
        'secret',
    ];

    public $hidden = [
        'secret',
    ];

    public $table = 'test_models';

    use HasFactory;
    use KeepsDeletedModels;

    public function relatedModel()
    {
        return $this->hasOne(RelatedModel::class);
    }
}
