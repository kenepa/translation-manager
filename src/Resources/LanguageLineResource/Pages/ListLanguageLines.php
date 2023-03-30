<?php

namespace musa11971\FilamentTranslationManager\Resources\LanguageLineResource\Pages;

use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use musa11971\FilamentTranslationManager\Helpers\TranslationScanner;
use musa11971\FilamentTranslationManager\Resources\LanguageLineResource;
use Spatie\TranslationLoader\LanguageLine;

class ListLanguageLines extends ListRecords
{
    protected static string $resource = LanguageLineResource::class;

    public function synchronize(): void
    {
        // Create non-existing groups and keys
        $scanner = new TranslationScanner;
        $groupsAndKeys = $scanner->start();

        foreach ($groupsAndKeys as $groupAndKey) {
            LanguageLine::firstOrCreate($groupAndKey);
        }

        $this->notify('success', __('filament-translation-manager::translations.synchronization-success', ['count' => LanguageLine::count()]));
    }

    protected function getActions(): array
    {
        return [
            Action::make('synchronize')
                ->action('synchronize')
                ->icon('heroicon-o-chevron-right')
                ->label(__('filament-translation-manager::translations.synchronize')),
        ];
    }
}
