<?php

namespace Kenepa\TranslationManager\Actions;

use Filament\Pages\Actions\Action;
use Filament\Pages\Page;
use Kenepa\TranslationManager\Commands\SynchronizeTranslationsCommand;
use Kenepa\TranslationManager\Helpers\TranslationScanner;
use Spatie\TranslationLoader\LanguageLine;

class SynchronizeAction extends Action
{
    public static function make(?string $name = null): static
    {
        return parent::make($name)
            ->label(__('translation-manager::translations.synchronize'))
            ->icon('heroicon-o-chevron-right');
    }

    /**
     * Runs the synchronization process for the translations.
     */
    public static function synchronize(SynchronizeTranslationsCommand $command = null): array
    {
        // Extract all translation groups, keys and text
        $groupsAndKeys = TranslationScanner::scan();

        $result = [];
        $result['total_count'] = 0;

        // Find and delete old LanguageLines that no longer exist in the translation files
        $result['deleted_count'] = LanguageLine::query()
            ->whereNotIn('group', array_column($groupsAndKeys, 'group'))
            ->orWhereNotIn('key', array_column($groupsAndKeys, 'key'))
            ->delete();

        // Create new LanguageLines for the groups and keys that don't exist yet
        foreach ($groupsAndKeys as $groupAndKey) {
            $startTime = microtime(true);

            $existingItem = LanguageLine::where('group', $groupAndKey['group'])
                ->where('key', $groupAndKey['key'])
                ->first();

            if (! $existingItem) {
                LanguageLine::create([
                    'group' => $groupAndKey['group'],
                    'key' => $groupAndKey['key'],
                    'text' => $groupAndKey['text'],
                ]);

                $result['total_count'] += 1;

                $runTime = number_format((microtime(true) - $startTime) * 1000, 2);
                $command?->components()->twoColumnDetail($groupAndKey['group'] . '.' . $groupAndKey['key'], "<fg=gray>{$runTime} ms</> <fg=green;options=bold>DONE</>");
            }
        }

        return $result;
    }

    /**
     * Runs the synchronization process for a page.
     *
     * @param  Page  $page The page to display notifications on.
     */
    public static function run(Page $page): void
    {
        $result = static::synchronize();

        $page->notify('success', __('translation-manager::translations.synchronization-success', ['count' => $result['total_count']]));

        if ($result['deleted_count'] > 0) {
            $page->notify('success', __('translation-manager::translations.synchronization-deleted', ['count' => $result['deleted_count']]));
        }
    }
}
