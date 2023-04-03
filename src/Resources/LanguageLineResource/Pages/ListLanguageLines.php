<?php

namespace musa11971\FilamentTranslationManager\Resources\LanguageLineResource\Pages;

use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use musa11971\FilamentTranslationManager\Actions\SynchronizeAction;
use musa11971\FilamentTranslationManager\Resources\LanguageLineResource;

class ListLanguageLines extends ListRecords
{
    protected static string $resource = LanguageLineResource::class;

    public function synchronize(): void
    {
        SynchronizeAction::run($this);
    }

    protected function getActions(): array
    {
        return [
            Action::make('quick-translate')
                ->icon('heroicon-o-chevron-right')
                ->label(__('filament-translation-manager::translations.quick-translate'))
                ->url(LanguageLineResource::getUrl('quick-translate')),

            SynchronizeAction::make('synchronize')
                ->action('synchronize'),
        ];
    }
}
