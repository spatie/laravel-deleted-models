<?php

namespace Spatie\DeletedModels\Tests\TestSupport\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\DeletedModels\Tests\TestSupport\Models\RelatedModel;
use Spatie\DeletedModels\Tests\TestSupport\Models\TestModel;

class RelatedModelFactory extends Factory
{
    public $model = RelatedModel::class;

    public function definition()
    {
        return [
            'test_model_id' => TestModel::factory(),
            'name' => $this->faker->name,
        ];
    }
}
