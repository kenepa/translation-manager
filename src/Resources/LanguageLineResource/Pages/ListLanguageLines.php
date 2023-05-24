<?php

namespace Kenepa\TranslationManager\Resources\LanguageLineResource\Pages;

use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Kenepa\TranslationManager\Actions\SynchronizeAction;
use Kenepa\TranslationManager\Resources\LanguageLineResource;

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
                ->label(__('translation-manager::translations.quick-translate'))
                ->url(LanguageLineResource::getUrl('quick-translate')),

            SynchronizeAction::make('synchronize')
                ->action('synchronize'),
        ];
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return config('translation-manager.list_records_per_page', []);
    }
}
