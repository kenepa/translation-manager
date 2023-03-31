<?php

namespace musa11971\FilamentTranslationManager\Resources\LanguageLineResource\Pages;

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
            SynchronizeAction::make('synchronize')
                ->action('synchronize'),
        ];
    }
}
