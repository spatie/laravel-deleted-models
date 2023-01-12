<?php

namespace Spatie\DeletedRecordsKeeper\Commands;

use Illuminate\Console\Command;

class DeletedRecordsKeeperCommand extends Command
{
    public $signature = 'laravel-deleted-records-keeper';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
