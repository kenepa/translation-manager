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
        $command?->loudInfo('synchronize function called');

        $result = [];

        // Create non-existing groups and keys
        $scanner = new TranslationScanner($command);
        $groupsAndKeys = $scanner->start();

        $result['total_count'] = count($groupsAndKeys);

        // Find and delete old LanguageLines that no longer exist in the translation files
        $result['deleted_count'] = LanguageLine::whereNotIn('group', array_column($groupsAndKeys, 'group'))
            ->orWhereNotIn('key', array_column($groupsAndKeys, 'key'))
            ->delete();

        $command?->loudInfo('deleted old languagelines');

        $command?->loudInfo('found ' . $result['total_count'] . ' total language lines');
        $command?->loudInfo('deleted ' . $result['deleted_count'] . ' unused ones');

        // Create new LanguageLines for the groups and keys that don't exist yet
        foreach ($groupsAndKeys as $groupAndKey) {
            $command?->loudInfo('checking existence: ' . $groupAndKey['group'] . '.' . $groupAndKey['group']);

            $exists = LanguageLine::where('group', $groupAndKey['group'])
                ->where('key', $groupAndKey['key'])
                ->exists();

            if (! $exists) {
                LanguageLine::create($groupAndKey);

                $command?->loudInfo('exists? no (created new)');
            } else {
                $command?->loudInfo('exists? yes');
            }
        }

        $command?->loudInfo('finished synchronize function');

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
