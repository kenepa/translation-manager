<?php

namespace musa11971\FilamentTranslationManager\Resources;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use musa11971\FilamentTranslationManager\Filters\NotTranslatedFilter;
use musa11971\FilamentTranslationManager\Resources\LanguageLineResource\Pages\EditLanguageLine;
use musa11971\FilamentTranslationManager\Resources\LanguageLineResource\Pages\ListLanguageLines;
use Spatie\TranslationLoader\LanguageLine;

class LanguageLineResource extends Resource
{
    protected static ?string $model = LanguageLine::class;
    protected static ?string $navigationIcon = 'heroicon-o-globe';
    protected static ?string $slug = 'translation-manager';

    public static function getLabel(): ?string
    {
        return trans_choice('filament-translation-manager::translations.translation-label', 1);
    }

    public static function getPluralLabel(): ?string
    {
        return trans_choice('filament-translation-manager::translations.translation-label', 2);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('group')
                    ->prefixIcon('heroicon-o-tag')
                    ->disabled(config('filament-translation-manager.disable_key_and_group_editing'))
                    ->label(__('filament-translation-manager::translations.group'))
                    ->required(),

                TextInput::make('key')
                    ->prefixIcon('heroicon-o-key')
                    ->disabled(config('filament-translation-manager.disable_key_and_group_editing'))
                    ->label(__('filament-translation-manager::translations.key'))
                    ->required(),

                ViewField::make('preview')
                    ->view('filament-translation-manager::preview-translation')
                    ->columnSpan(2),

                Repeater::make('translations')->schema([
                    Select::make('language')
                        ->prefixIcon('heroicon-o-translate')
                        ->label(__('filament-translation-manager::translations.translation-language'))
                        ->options(collect(config('filament-translation-manager.available_locales'))->pluck('code', 'code'))
                        ->required(),

                    Textarea::make('text')
                        ->label(__('filament-translation-manager::translations.translation-text'))
                        ->required(),
                ])->columns(2)
                    ->createItemButtonLabel(__('filament-translation-manager::translations.add-translation-button'))
                    ->disableLabel()
                    ->defaultItems(0)
                    ->disableItemMovement()
                    ->grid(2)
                    ->columnSpan(2)
                    ->maxItems(count(config('filament-translation-manager.available_locales'))),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(static::getColumns())
            ->filters([NotTranslatedFilter::make()])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getColumns(): array
    {
        $columns = [
            TextColumn::make('group')
                ->label(__('filament-translation-manager::translations.group'))
                ->searchable(),

            TextColumn::make('key')
                ->label(__('filament-translation-manager::translations.key'))
                ->searchable(),

            TextColumn::make('preview')
                ->searchable(query: function (Builder $query, string $search): Builder {
                    return $query
                        ->where('text', 'like', "%{$search}%");
                })
                ->label(__('filament-translation-manager::translations.preview-in-your-lang', ['lang' => app()->getLocale()]))
                ->icon('heroicon-o-translate')
                ->size('sm')
                ->sortable(false)
                ->formatStateUsing(fn ($record): string => static::getTranslationPreview($record, 50)),
        ];

        foreach (config('filament-translation-manager.available_locales') as $locale) {
            $localeCode = $locale['code'];

            if ($localeCode == config('app.fallback_locale')) {
                continue;
            }

            $columns[] = IconColumn::make($localeCode)
                ->label(strtoupper($localeCode))
                ->searchable(false)
                ->sortable(false)
                ->getStateUsing(function (LanguageLine $record) use ($localeCode) {
                    return in_array($localeCode, array_keys($record->text));
                })
                ->boolean();
        }

        return $columns;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLanguageLines::route('/'),
            'edit' => EditLanguageLine::route('/{record}/edit'),
        ];
    }

    public static function getTranslationPreview($record, $maxLength = null)
    {
        $transParameter = "{$record->group}.{$record->key}";
        $translated = trans($transParameter);

        if ($maxLength) {
            $translated = (strlen($translated) > $maxLength) ? substr($translated, 0, $maxLength) . '...' : $translated;
        }

        return $translated;
    }

    public static function canViewAny(): bool
    {
        return Gate::allows('use-translation-manager');
    }

    public static function canEdit(Model $record): bool
    {
        return Gate::allows('use-translation-manager');
    }

    protected static function getNavigationLabel(): string
    {
        return __('filament-translation-manager::translations.translation-navigation-label');
    }

    protected static function getNavigationGroup(): ?string
    {
        return config('filament-translation-manager.navigation_group');
    }
}
