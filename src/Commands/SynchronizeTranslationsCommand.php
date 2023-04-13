<?php

namespace musa11971\FilamentTranslationManager\Commands;

use Illuminate\Console\Command;
use musa11971\FilamentTranslationManager\Actions\SynchronizeAction;

class SynchronizeTranslationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:synchronize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize all application translations';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $startTime = microtime(true);
        $this->info('Synchronization busy...');

        $result = SynchronizeAction::synchronize($this);

        $elapsedSecs = round(microtime(true) - $startTime, 2);
        $this->info('Synchronization success! (' . $elapsedSecs . 's)');
        $this->info('  - total: ' . $result['total_count']);
        $this->info('  - deleted: ' . $result['deleted_count']);
    }
}
