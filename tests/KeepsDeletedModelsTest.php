<?php

use Illuminate\Database\Eloquent\Relations\Relation;
use Spatie\DeletedModels\Exceptions\CouldNotRestoreModel;
use Spatie\DeletedModels\Exceptions\NoModelFoundToRestore;
use Spatie\DeletedModels\Models\DeletedModel;
use Spatie\DeletedModels\Tests\TestSupport\Models\CustomDeletedModel;
use Spatie\DeletedModels\Tests\TestSupport\Models\RelatedModel;
use Spatie\DeletedModels\Tests\TestSupport\Models\TestModel;
use function Spatie\PestPluginTestTime\testTime;

beforeEach(function () {
    testTime()->freeze('2023-01-01 00:00:00');

    Relation::morphMap([], merge: false);

    $this->attributes = [
        'name' => 'John Doe',
        'secret' => 'secret-value',
    ];

    $this->model = TestModel::factory()->create($this->attributes);

    $this->modelId = $this->model->id;
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

it('can delete a model without keeping it', function () {
    $this->model->deleteWithoutKeeping();

    expect(DeletedModel::count())->toBe(0);
});

it('uses the morph map to determine the model', function () {
    Relation::morphMap([
        'test' => TestModel::class,
    ]);

    $this->model->delete();

    expect(DeletedModel::first()->model)->toBe('test');
});

it('can restore a deleted model', function () {
    $this->model->delete();

    expect(TestModel::count())->toBe(0);

    DeletedModel::first()->restore();

    expect(TestModel::count())->toBe(1);

    $restoredModel = TestModel::first();

    expect($restoredModel)
        ->id->toBe(1)
        ->name->toBe('John Doe')
        ->secret->toBe('secret-value')
        ->created_at->format('Y-m-d H:i:s')->toBe('2023-01-01 00:00:00')
        ->updated_at->format('Y-m-d H:i:s')->toBe('2023-01-01 00:00:00');
});

it('can uses the morph map when restoring a model', function () {
    Relation::morphMap([
        'test' => TestModel::class,
    ]);

    $this->model->delete();

    DeletedModel::first()->restore();

    expect(TestModel::first()->name)->toBe('John Doe');
});

it('can be configured to not keep a deleted model', function () {
    $model = new class extends TestModel
    {
        public function shouldKeep(): bool
        {
            return false;
        }
    };

    $model
        ->fill($this->attributes)
        ->save();

    $model->delete();

    expect(DeletedModel::count())->toBe(0);
});

it('can determine the attributes to be stored', function () {
    $model = new class extends TestModel
    {
        public function attributesToKeep(): array
        {
            return [
                'name' => "{$this->name} suffix",
            ];
        }
    };

    $model
        ->fill($this->attributes)
        ->save();

    $model->delete();

    $deletedModel = DeletedModel::first();

    expect($deletedModel->value('name'))->toBe('John Doe suffix');
});

it('will throw an exception when trying to restore a not-existing model', function () {
    TestModel::restore('non-existing');
})->throws(NoModelFoundToRestore::class);

it('will throw an exception when the model cannot be restored', function () {
    $this->model->delete();

    // sneakily change the deleted model so it cannot be restored
    DeletedModel::first()->update(['values' => []]);

    TestModel::restore($this->modelId);
})->throws(CouldNotRestoreModel::class);

it('will save hidden attributes as well', function () {
    $this->model->delete();

    expect(DeletedModel::count())->toBe(1);

    $deletedModel = DeletedModel::first();

    expect($deletedModel->values)
        ->id->toBe(1)
        ->secret->toBe('secret-value');

    // hidden attributes will not appear on the original model instance
    expect($this->model->toArray())->not()->toHaveKey('secret');
});

it('can make a restored model without saving it', function () {
    $this->model->delete();

    $testModel = TestModel::makeRestored($this->modelId);

    expect($testModel)->toBeInstanceOf(TestModel::class);
    expect($testModel->name)->toBe('John Doe');
    expect($testModel->exists)->toBe(false);
});

it('will return null when making a non-existing model', function () {
    $this->model->delete();

    $testModel = TestModel::makeRestored('non-existing-key');

    expect($testModel)->toBeNull();
});

it('will not save relationship objects', function () {
    RelatedModel::factory()->for($this->model)->create();

    $this->model->load('relatedModel');

    $this->model->delete();

    expect(DeletedModel::count())->toBe(1);

    $deletedModel = DeletedModel::first();

    expect($deletedModel->values)->not()->toHaveKey('related_model');
});

it('can use custom deleted model class', function () {
    config(['deleted-models.model' => CustomDeletedModel::class]);

    $this->model->delete();

    $deletedModel = TestModel::deletedModels()
        ->where('key', $this->model->id)
        ->first();

    expect($deletedModel)->toBeInstanceOf(CustomDeletedModel::class);
});
