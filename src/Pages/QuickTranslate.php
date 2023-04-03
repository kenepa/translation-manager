<?php

namespace musa11971\FilamentTranslationManager\Pages;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use musa11971\FilamentTranslationManager\Resources\LanguageLineResource;
use Spatie\TranslationLoader\LanguageLine;

class QuickTranslate extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament-translation-manager::quick-translate';
    protected static string $resource = LanguageLineResource::class;

    public $selectedLocale = null;
    public $offset = 0;
    public $record;
    public $totalLanguageLines;
    public $enteredTranslation;

    /**
     * Returns an array containing two forms for quick translation of content.
     */
    public function getForms(): array
    {
        return [
            'selectForm' => $this->makeForm()
                ->schema([
                    Select::make('selectedLocale')
                        ->options(collect(config('filament-translation-manager.available_locales'))->pluck('code', 'code'))
                        ->label(__('filament-translation-manager::translations.quick-translate-select-locale'))
                        ->reactive()
                        ->afterStateUpdated(function ($state) {
                            $this->offset = 0;
                            $this->update();
                        }),
                ]),

            'enterForm' => $this->makeForm()
                ->schema([
                    Textarea::make('enteredTranslation')
                        ->label(__('filament-translation-manager::translations.quick-translate-enter', ['lang' => $this->selectedLocale]))
                        ->required(),
                ]),
        ];
    }

    /**
     * Saves the entered translation to the current record for the selected locale and proceeds to the next item.
     */
    public function saveAndContinue(): void
    {
        if (! strlen($this->enteredTranslation)) {
            return;
        }

        // Save the entered translation
        $text = $this->record->text;
        $text[$this->selectedLocale] = $this->enteredTranslation;
        $this->record->text = $text;
        $this->record->save();

        // Clear the input field
        $this->enteredTranslation = '';

        // Go to the next item
        $this->next();
    }

    /**
     * Updates the current record and total number of language lines for the selected locale.
     */
    public function update(): void
    {
        $this->record = LanguageLine::whereNull('text->' . $this->selectedLocale)
            ->offset($this->offset)
            ->first();

        $this->totalLanguageLines = LanguageLine::whereNull('text->' . $this->selectedLocale)->count();
    }

    /**
     * Navigate to the next record.
     */
    public function next(): void
    {
        $this->offset++;
        $this->update();
    }

    /**
     * Returns the title of the page.
     */
    protected function getTitle(): string
    {
        return __('filament-translation-manager::translations.quick-translate');
    }
}
