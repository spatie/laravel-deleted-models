<?php

namespace Spatie\DeletedModels\Tests\TestSupport\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\DeletedModels\Models\Concerns\KeepsDeletedModels;

class TestModel extends Model
{
    use KeepsDeletedModels;
    use HasFactory;
}
