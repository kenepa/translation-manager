<?php

namespace Kenepa\TranslationManager\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Kenepa\TranslationManager\Commands\SynchronizeTranslationsCommand;
use Kenepa\TranslationManager\Helpers\TranslationScanner;
use Spatie\TranslationLoader\LanguageLine;

class SynchronizeAction extends Action
{
    public static function make(string $name = null): static
    {
        return parent::make($name)
            ->label(__('translation-manager::translations.synchronize'))
            ->icon('heroicon-o-arrow-path-rounded-square');
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

        /** @var LanguageLine $languageLine */
        $languageLine = config('translation-loader.model', LanguageLine::class);

        // Find and delete old LanguageLines that no longer exist in the translation files
        $result['deleted_count'] = $languageLine::query()
            ->whereNotIn('group', array_column($groupsAndKeys, 'group'))
            ->orWhereNotIn('key', array_column($groupsAndKeys, 'key'))
            ->delete();

        // Create new LanguageLines for the groups and keys that don't exist yet
        foreach ($groupsAndKeys as $groupAndKey) {
            $startTime = microtime(true);

            $existingItem = $languageLine::where('group', $groupAndKey['group'])
                ->where('key', $groupAndKey['key'])
                ->first();

            if (! $existingItem) {
                $languageLine::create([
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
     */
    public static function run(): void
    {
        $result = static::synchronize();

        Notification::make()
            ->title(__('translation-manager::translations.synchronization-success', ['count' => $result['total_count']]))
            ->icon('heroicon-o-check-circle')
            ->iconColor('success')
            ->send();

        if ($result['deleted_count'] > 0) {
            Notification::make()
                ->title(__('translation-manager::translations.synchronization-deleted', ['count' => $result['deleted_count']]))
                ->icon('heroicon-o-trash')
                ->send();
        }
    }
}
