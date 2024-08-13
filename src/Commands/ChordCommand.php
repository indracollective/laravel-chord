<?php

namespace LiveSource\Chord\Commands;

use Illuminate\Console\Command;

class ChordCommand extends Command
{
    public $signature = 'chord';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
