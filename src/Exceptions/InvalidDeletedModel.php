<?php

namespace Spatie\DeletedModels\Exceptions;

use Exception;

class InvalidDeletedModel extends Exception
{
    public static function create(string $model): self
    {
        return new self("The model `{$model}` is invalid. A valid model must extend the model \Spatie\DeletedModels\Models\DeletedModel.");
    }
}
