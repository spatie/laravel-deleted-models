<?php

namespace Spatie\DeletedModels\Tests\TestSupport\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\DeletedModels\Models\Concerns\KeepsDeletedModels;

class TestModel extends Model
{
    public $guarded = [];

    public $hidden = [
        'secret',
    ];

    public $table = 'test_models';

    use KeepsDeletedModels;
    use HasFactory;
}
