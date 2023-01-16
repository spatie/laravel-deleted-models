<?php

namespace Spatie\DeletedModels\Tests\TestSupport\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\DeletedModels\Tests\TestSupport\Models\TestDeletedModel;
use Spatie\DeletedModels\Tests\TestSupport\Models\TestModel;

class TestDeletedModelFactory extends Factory
{
    public $model = TestDeletedModel::class;

    public function definition()
    {
        return [
            'key' => TestModel::factory(),
            'model' => 'TestModel',
            'values' => [],
        ];
    }
}
