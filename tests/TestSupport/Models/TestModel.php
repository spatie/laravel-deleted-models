<?php

namespace Spatie\DeletedRecordsKeeper\Tests\TestSupport\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\DeletedRecordsKeeper\Models\Concerns\KeepsDeletedModels;

class TestModel extends Model
{
    use KeepsDeletedModels;
    use HasFactory;
}
