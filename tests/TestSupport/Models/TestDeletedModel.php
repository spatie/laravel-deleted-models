<?php

namespace Spatie\DeletedModels\Tests\TestSupport\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\DeletedModels\Models\DeletedModel;

class TestDeletedModel extends DeletedModel
{
    use HasFactory;
}
