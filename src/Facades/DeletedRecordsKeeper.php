<?php

namespace Spatie\DeletedRecordsKeeper\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Spatie\DeletedRecordsKeeper\DeletedRecordsKeeper
 */
class DeletedRecordsKeeper extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Spatie\DeletedRecordsKeeper\DeletedRecordsKeeper::class;
    }
}
