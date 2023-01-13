<?php

use Illuminate\Database\Eloquent\Relations\Relation;
use Spatie\DeletedModels\Models\DeletedModel;
use Spatie\DeletedModels\Tests\TestSupport\Models\TestModel;
use function Spatie\PestPluginTestTime\testTime;

beforeEach(function () {
    testTime()->freeze('2023-01-01 00:00:00');

    $this->model = TestModel::factory()->create([
        'name' => 'John Doe',
    ]);

    Relation::morphMap();

});

it('will copy a deleted model to the deleted models table', function () {
    $this->model->delete();

    expect(DeletedModel::count())->toBe(1);

    $deletedModel = DeletedModel::first();

    expect($deletedModel)
        ->key->toBe(1)
        ->model->toBe(TestModel::class);

    expect($deletedModel->values)
        ->id->toBe(1)
        ->name->toBe('John Doe')
        ->created_at->toBe('2023-01-01T00:00:00.000000Z')
        ->updated_at->toBe('2023-01-01T00:00:00.000000Z');
});

it('can delete a model without keeping it', function() {
    $this->model->deleteWithoutKeeping();

    expect(DeletedModel::count())->toBe(0);
});

it('uses the morph map to determine the model', function() {
    Relation::morphMap([
        'test' => TestModel::class,
    ]);

    $this->model->delete();

    expect(DeletedModel::first()->model)->toBe('test');
});

it('can restore a deleted model', function() {
    $this->model->delete();

    expect(TestModel::count())->toBe(0);

    DeletedModel::first()->restore();

    expect(TestModel::count())->toBe(1);

    $restoredModel = TestModel::first();

    expect($restoredModel)
        ->id->toBe(1)
        ->name->toBe('John Doe')
        ->created_at->format('Y-m-d H:i:s')->toBe('2023-01-01 00:00:00')
        ->updated_at->format('Y-m-d H:i:s')->toBe('2023-01-01 00:00:00');
});

it('can uses the morph map when restoring a model', function() {
    Relation::morphMap([
        'test' => TestModel::class,
    ]);

    $this->model->delete();

    DeletedModel::first()->restore();

    expect(TestModel::first()->name)->toBe('John Doe');
});
