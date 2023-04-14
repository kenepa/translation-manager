<?php

namespace musa11971\FilamentTranslationManager\Actions;

use Filament\Pages\Actions\Action;
use Filament\Pages\Page;
use musa11971\FilamentTranslationManager\Commands\SynchronizeTranslationsCommand;
use musa11971\FilamentTranslationManager\Helpers\TranslationScanner;
use Spatie\TranslationLoader\LanguageLine;

class SynchronizeAction extends Action
{
    public static function make(?string $name = null): static
    {
        return parent::make($name)
            ->label(__('filament-translation-manager::translations.synchronize'))
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
            $command?->loudInfo('updateOrCreate: ' . $groupAndKey['group'] . '.' . $groupAndKey['group']);

            LanguageLine::updateOrCreate($groupAndKey);

            $command?->loudInfo('done');
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

        $page->notify('success', __('filament-translation-manager::translations.synchronization-success', ['count' => $result['total_count']]));

        if ($result['deleted_count'] > 0) {
            $page->notify('success', __('filament-translation-manager::translations.synchronization-deleted', ['count' => $result['deleted_count']]));
        }
    }
}
