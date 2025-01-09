<?php

namespace Spatie\DeletedModels\Events;

use Spatie\DeletedModels\Models\DeletedModel;

class RestoringDeletedModelEvent
{
    public function __construct(public DeletedModel $model) {}
}
