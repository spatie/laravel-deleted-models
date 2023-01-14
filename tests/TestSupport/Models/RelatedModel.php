<?php

namespace Spatie\DeletedModels\Tests\TestSupport\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelatedModel extends Model
{
    use HasFactory;

    public $guarded = [];

    public $table = 'related_models';

    public function testModel()
    {
        return $this->belongsTo(TestModel::class);
    }
}
