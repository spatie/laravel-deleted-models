<?php

namespace Spatie\DeletedModels\Exceptions;

use Exception;
use Spatie\DeletedModels\Models\DeletedModel;

class CouldNotRestoreModel extends Exception
{
    public static function make(DeletedModel $model, Exception $exception)
    {
        return new self("Could not restore deleted model id `{$model->id}` because: {$exception->getMessage()}", previous: $exception);
    }
}
