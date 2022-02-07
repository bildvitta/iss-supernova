<?php

namespace Bildvitta\IssSupernova\Commands;

use Illuminate\Console\Command;

class IssSupernovaCommand extends Command
{
    public $signature = 'iss-supernova';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
