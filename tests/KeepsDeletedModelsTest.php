<?php

use Spatie\DeletedRecordsKeeper\Models\DeletedModel;
use Spatie\DeletedRecordsKeeper\Tests\TestSupport\Models\TestModel;
use function Spatie\PestPluginTestTime\testTime;

beforeEach(function() {
    testTime()->freeze('2023-01-01 00:00:00');
});

it('will copy a deleted model to the deleted models table', function() {
    $model = TestModel::factory()->create([
        'name' => 'John Doe',
    ]);

    $model->delete();

    expect(DeletedModel::count())->toBe(1);

    $model = DeletedModel::first();

    expect($model)
        ->key->toBe(1)
        ->model->toBe(TestModel::class);

    expect($model->values)
        ->id->toBe(1)
        ->name->toBe('John Doe')
        ->created_at->toBe('2023-01-01T00:00:00.000000Z')
        ->updated_at->toBe('2023-01-01T00:00:00.000000Z');


});
