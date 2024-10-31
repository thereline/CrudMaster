<?php

namespace Thereline\CrudMaster\Commands;

use Illuminate\Console\Command;

class CrudMasterCommand extends Command
{
    public $signature = 'crudmaster';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
