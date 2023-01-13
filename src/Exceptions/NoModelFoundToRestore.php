<?php

namespace Spatie\DeletedModels\Exceptions;

use Exception;

class NoModelFoundToRestore extends Exception
{
    public static function make(string $modelClass, mixed $key): self
    {
        return new self("Could not find a deleted model to restore for class `{$modelClass}` with key `{$key}`.");
    }
}
