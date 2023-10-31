<?php

namespace Kenepa\TranslationManager\Resources\LanguageLineResource\Pages;

use Filament\Actions\Action;
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

    public function getTranslationPreview($record, $maxLength = null)
    {
        $transParameter = "{$record->group}.{$record->key}";
        $translated = trans($transParameter);

        if ($maxLength) {
            $translated = (strlen($translated) > $maxLength) ? substr($translated, 0, $maxLength) . '...' : $translated;
        }

        return $translated;
    }

    protected function getActions(): array
    {
        return [
            Action::make('quick-translate')
                ->icon('heroicon-o-bolt')
                ->label(__('translation-manager::translations.quick-translate'))
                ->url(LanguageLineResource::getUrl('quick-translate')),

            SynchronizeAction::make('synchronize')
                ->action('synchronize')->tooltip('oke'),
        ];
    }
}
