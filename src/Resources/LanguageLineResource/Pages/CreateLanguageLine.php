<?php

namespace musa11971\FilamentTranslationManager\Resources\LanguageLineResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use musa11971\FilamentTranslationManager\Resources\LanguageLineResource;

class CreateLanguageLine extends CreateRecord
{
    protected static string $resource = LanguageLineResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['text'] = [];

        foreach ($data['translations'] as $translation) {
            $data['text'][$translation['language']] = $translation['text'];
        }

        unset($data['translations']);

        return $data;
    }
}
