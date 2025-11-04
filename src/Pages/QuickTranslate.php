<?php

namespace Kenepa\TranslationManager\Pages;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Kenepa\TranslationManager\Resources\LanguageLineResource;
use Kenepa\TranslationManager\Traits\CanRegisterPanelNavigation;
use Kenepa\TranslationManager\TranslationManagerPlugin;
use Spatie\TranslationLoader\LanguageLine;

class QuickTranslate extends Page implements HasForms
{
    use CanRegisterPanelNavigation, InteractsWithForms;

    protected static string $resource = LanguageLineResource::class;

    public $selectedLocale = null;

    public $offset = 0;

    public $record;

    public $totalLanguageLines;

    public $enteredTranslation;

    protected string $view = 'translation-manager::quick-translate';

    /**
     * @param  array<string, mixed>  $parameters
     */
    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return static::shouldRegisterOnPanel() ? TranslationManagerPlugin::get()->shouldRegisterQuickTranslateNavigation() : false;
    }

    public static function getNavigationGroup(): ?string
    {
        return TranslationManagerPlugin::get()->getNavigationGroup();
    }

    public static function getNavigationLabel(): string
    {
        return trans_choice('translation-manager::translations.quick-translate', 1);
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-bolt';
    }

    public function mount(): void
    {
        abort_unless(static::shouldRegisterOnPanel(), 403);
    }

    /**
     * Returns an array containing two forms for quick translation of content.
     */
    public function selectForm(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('selectedLocale')
                ->options(collect(TranslationManagerPlugin::get()->getAvailableLocales())->pluck('code', 'code'))
                ->label(__('translation-manager::translations.quick-translate-select-locale'))
                ->reactive()
                ->afterStateUpdated(function ($state) {
                    $this->offset = 0;
                    $this->next();
                }),
        ]);
    }

    /**
     * Returns an array containing two forms for quick translation of content.
     */
    public function enterForm(Schema $schema): Schema
    {
        return $schema->components([
            Textarea::make('enteredTranslation')
                ->label(__('translation-manager::translations.quick-translate-enter', ['lang' => $this->selectedLocale]))
                ->required(),
        ]);
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
     * Navigate to the next record.
     */
    public function next(): void
    {
        $this->record = LanguageLine::whereNull('text->' . $this->selectedLocale)
            ->first();

        $this->totalLanguageLines = LanguageLine::whereNull('text->' . $this->selectedLocale)->count();
    }

    public function skip(): void
    {
        $this->offset++;
        $this->record = LanguageLine::whereNull('text->' . $this->selectedLocale)
            ->offset($this->offset)
            ->first();
    }

    /**
     * Returns the title of the page.
     */
    public function getTitle(): string
    {
        return __('translation-manager::translations.quick-translate');
    }
}
