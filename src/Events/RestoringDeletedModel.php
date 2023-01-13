<?php

namespace Spatie\DeletedModels\Events;

use Spatie\DeletedModels\Models\DeletedModel;

class RestoringDeletedModel
{
    public function __construct(public DeletedModel $model)
    {
    }
}
