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
    protected $signature = 'translations:synchronize {--l|loud}';

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
        $this->loudInfo('  - total: ' . $result['total_count']);
        $this->loudInfo('  - deleted: ' . $result['deleted_count']);
    }

    /**
     * Prints the info message if loud option is enabled.
     *
     * @return void
     */
    public function loudInfo($message)
    {
        if (! $this->option('loud')) {
            return;
        }

        $prefix = '[' . now()->format('H:i:s') . '] ! ';
        $this->info($prefix . $message);
    }
}
