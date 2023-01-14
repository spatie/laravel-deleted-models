# Automatically copy deleted records to a separate table

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-deleted-models.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-deleted-models)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/spatie/laravel-deleted-models/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/spatie/laravel-deleted-models/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/spatie/laravel-deleted-models/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/spatie/laravel-deleted-models/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-deleted-models.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-deleted-models)

When deleting an Eloquent model, this package will copy the attributes of that model to a new table called `deleted_models`. You can view this as a sort of "Recycle bin for models".

```php
$blogPost = BlogPost::find(5); // an Eloquent model

$blogPost->delete(); // values will be copied to the `deleted_models` table.
```

To restore a previous model you can call `restore` and pass the id.

```php
$blogPost = Blogpost::restore(5); // $blogPost will be restored and returned
```

This way of preserving information when deleting can be seen as an alternative to soft deletes. You can read more on the trade-offs [in this blog post](https://brandur.org/fragments/deleted-record-insert) and [this one](https://brandur.org/soft-deletion).

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/laravel-deleted-models.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/laravel-deleted-models)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require spatie/laravel-deleted-models
```

To create the `deleted_models` table, you can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="deleted-models-migrations"
php artisan migrate
```

Optionally, you can publish the config file with:

```bash
php artisan vendor:publish --tag="deleted-models-config"
```

This is the contents of the published config file:

```php
return [
    /*
     * The model uses to store deleted models.
     */
    'model' => Spatie\DeletedModels\Models\DeletedModel::class,

    /*
     * After this amount of days, the records in `deleted_models` will be deleted
     *
     * This functionality uses Laravel's native pruning feature.
     */
    'prune_after_days' => 365,
];
```

The pruning of the `deleted_models` table depends on Laravel's native pruning feature. Don't forget to schedule the `model:prune` as instructed [in Laravel's docs](https://laravel.com/docs/9.x/eloquent#pruning-models).

```php
$schedule->command('model:prune', [
    '--model' => [\Spatie\DeletedModels\Models\DeletedModel::class],
])->daily();
```

## Usage

You should add the `KeepsDeletedModels` trait to all models whose attributes should be written to `deleted_models` whenever the model is deleted.

```php
use Illuminate\Database\Eloquent\Model;
use Spatie\DeletedModels\Models\Concerns\KeepsDeletedModels;

class BlogPost extends Model
{
    use KeepsDeletedModels;
}
```

With this in place, the attributes will be written to `deleted_models` when the model is deleted.

```php
$blogPost = BlogPost::find(5);

$blogPost->delete(); // values will be copied to the `deleted_models` table.
```

To restore a previous model you can call `restore` and pass the id.

```php
$blogPost = Blogpost::restore(5); // $blogPost will be restored and returned
```

### Restoring without saving

To restore in memory, without actually saving it, you can call `makeRestored`.
Keep in mind that calling this method will also remove the record in the `deleted_models_table`.

```php
// $blogPost will be return, but it is not saved in the db yet
$blogPost = Blogpost::makeRestored($id); 

$blogPost->save();
```

### Restoring without emitting events

By default, the `Spatie\DeletedModels\Events\RestoringDeletedModelEvent` and `Spatie\DeletedModels\Events\DeletedModelEvent` will be dispatched when calling `restore` on a model.

If you don't want these events to be dispatched, call `restoreQuietly`

```php
BlogPost::restoreQuietly(); // no events will be dispatched
```

### Customizing the restore process

If you need to run some logic to before and after restoring a model, you can implement `beforeRestoringModel` and `afterRestoringModel` on your model.

```php
use Illuminate\Database\Eloquent\Model;
use Spatie\DeletedModels\Models\Concerns\KeepsDeletedModels;

class BlogPost extends Model
{
    use KeepsDeletedModels;
    
    public static function beforeRestoringModel(DeletedModel $deletedModel): void
    {
        // this will be executed right before restoring a model
    }

    public static function afterRestoringModel(
        Model $restoredMode,
        DeletedModel $deletedModel
    ): void
    {
        // this will be executed right after restoring a model
    }
}
```

To determine which attributes and values should be kept in `deleted_models`, you can implement `attributesToKeep`

```php
use Illuminate\Database\Eloquent\Model;
use Spatie\DeletedModels\Models\Concerns\KeepsDeletedModels;

class BlogPost extends Model
{
    use KeepsDeletedModels;
    
    public function attributesToKeep(): array
    {
        // here you can customize which values should be kept. This is
        // the default implementation.
        
        return $this->toArray();
    }
}
```

### Pruning deleted models

After a while, the `deleted_models` table can become large. The `DeletedModel` implements [Laravel's native `MassPrunable` trait](https://laravel.com/docs/9.x/eloquent#pruning-models).

You can configure the number of days records in the `deleted_models` will be pruned in the `prune_after_days` key of the `deleted-models.php` config file. By default, all deleted models will be kept for 365 days.

Don't forget to schedule the `model:prune` as instructed [in Laravel's docs](https://laravel.com/docs/9.x/eloquent#pruning-models).

### Low-level customization of the delete and restoration process

The `DeletedModel` model implements most logic to keep and restore deleted models. You can modify any of the behaviour by creating a class that extends `Spatie\DeletedModels\Models\DeletedModel`. You should put the class name of your extended class in the `model` key of the `deleted-models.php` config file.

With this in place you can override any of the methods in `DeletedModel`.

```php
use Spatie\DeletedModels\Models\DeletedModel;

class CustomDeletedModel extends DeletedModel
{
    protected function makeRestoredModel(string $modelClass): mixed
    {
        // add custom logic
    
        return parent::makeRestoredModel($modelClass)
    }
}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
