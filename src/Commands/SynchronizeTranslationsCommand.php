<?php

namespace Kenepa\TranslationManager\Commands;

use Illuminate\Console\Command;
use Kenepa\TranslationManager\Actions\SynchronizeAction;

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

    public function components()
    {
        return $this->components;
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $startTime = microtime(true);
        $this->components->info('Starting synchronization.');

        $result = SynchronizeAction::synchronize($this);
        $this->newLine();

        $this->components->bulletList([
            'synced translations: ' . $result['total_count'],
            'purged translations: ' . $result['deleted_count'],
        ]);
        $this->newLine();

        $runTime = number_format((microtime(true) - $startTime) * 1000, 0);
        $this->components->info('Synchronization success! (' . $runTime . 'ms)');
    }
}
