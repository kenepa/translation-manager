<?php

namespace musa11971\FilamentTranslationManager\Resources\LanguageLineResource\Pages;

use Filament\Resources\Pages\EditRecord;
use musa11971\FilamentTranslationManager\Resources\LanguageLineResource;

class EditLanguageLine extends EditRecord
{
    protected static string $resource = LanguageLineResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        foreach ($data['text'] as $locale => $translation) {
            $data['translations'][] = [
                'language' => $locale,
                'text' => $translation,
            ];
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['text'] = [];

        foreach ($data['translations'] as $translation) {
            $data['text'][$translation['language']] = $translation['text'];
        }

        unset($data['translations']);

        return $data;
    }

    protected function beforeSave(): void
    {
        $this->record->flushGroupCache();
    }

    protected function getActions(): array
    {
        return [];
    }
}
