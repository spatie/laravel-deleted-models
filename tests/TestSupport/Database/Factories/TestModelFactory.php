<?php

namespace Spatie\DeletedRecordsKeeper\Tests\TestSupport\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\DeletedRecordsKeeper\Tests\TestSupport\Models\TestModel;

class TestModelFactory extends Factory
{
    protected $model = TestModel::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
        ];
    }
}
