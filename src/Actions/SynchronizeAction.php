<?php

namespace musa11971\FilamentTranslationManager\Actions;

use Filament\Pages\Actions\Action;
use Filament\Pages\Page;
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
     *
     * @param  Page  $page The page to display notifications on.
     */
    public static function run(Page $page): void
    {
        // Create non-existing groups and keys
        $scanner = new TranslationScanner;
        $groupsAndKeys = $scanner->start();

        // Find and delete old LanguageLines that no longer exist in the translation files
        $deletedCount = LanguageLine::whereNotIn('group', array_column($groupsAndKeys, 'group'))
            ->orWhereNotIn('key', array_column($groupsAndKeys, 'key'))
            ->delete();

        // Create new LanguageLines for the groups and keys that don't exist yet
        foreach ($groupsAndKeys as $groupAndKey) {
            LanguageLine::updateOrCreate($groupAndKey);
        }

        $page->notify('success', __('filament-translation-manager::translations.synchronization-success', ['count' => count($groupsAndKeys)]));

        if ($deletedCount > 0) {
            $page->notify('success', __('filament-translation-manager::translations.synchronization-deleted', ['count' => $deletedCount]));
        }
    }
}
