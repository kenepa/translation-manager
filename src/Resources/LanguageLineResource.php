<?php

namespace Kenepa\TranslationManager\Resources;

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
use Kenepa\TranslationManager\Filters\NotTranslatedFilter;
use Kenepa\TranslationManager\Pages\QuickTranslate;
use Kenepa\TranslationManager\Resources\LanguageLineResource\Pages\EditLanguageLine;
use Kenepa\TranslationManager\Resources\LanguageLineResource\Pages\ListLanguageLines;
use Spatie\TranslationLoader\LanguageLine;

class LanguageLineResource extends Resource
{
    protected static ?string $model = LanguageLine::class;
    protected static ?string $navigationIcon = 'heroicon-o-globe';
    protected static ?string $slug = 'translation-manager';

    public static function getLabel(): ?string
    {
        return trans_choice('translation-manager::translations.translation-label', 1);
    }

    public static function getPluralLabel(): ?string
    {
        return trans_choice('translation-manager::translations.translation-label', 2);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('group')
                    ->prefixIcon('heroicon-o-tag')
                    ->disabled(config('translation-manager.disable_key_and_group_editing'))
                    ->label(__('translation-manager::translations.group'))
                    ->required(),

                TextInput::make('key')
                    ->prefixIcon('heroicon-o-key')
                    ->disabled(config('translation-manager.disable_key_and_group_editing'))
                    ->label(__('translation-manager::translations.key'))
                    ->required(),

                ViewField::make('preview')
                    ->view('translation-manager::preview-translation')
                    ->columnSpan(2),

                Repeater::make('translations')->schema([
                    Select::make('language')
                        ->prefixIcon('heroicon-o-translate')
                        ->label(__('translation-manager::translations.translation-language'))
                        ->options(collect(config('translation-manager.available_locales'))->pluck('code', 'code'))
                        ->required(),

                    Textarea::make('text')
                        ->label(__('translation-manager::translations.translation-text'))
                        ->required(),
                ])->columns(2)
                    ->createItemButtonLabel(__('translation-manager::translations.add-translation-button'))
                    ->disableLabel()
                    ->defaultItems(0)
                    ->disableItemMovement()
                    ->grid(2)
                    ->columnSpan(2)
                    ->maxItems(count(config('translation-manager.available_locales'))),
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
            TextColumn::make('group_and_key')
                ->label(__('translation-manager::translations.group') . ' & ' . __('translation-manager::translations.key'))
                ->searchable(['group', 'key'])
                ->getStateUsing(function (Model $record) {
                    return $record->group . '.' . $record->key;
                }),

            TextColumn::make('preview')
                ->searchable(query: function (Builder $query, string $search): Builder {
                    return $query
                        ->where('text', 'like', "%{$search}%");
                })
                ->label(__('translation-manager::translations.preview-in-your-lang', ['lang' => app()->getLocale()]))
                ->icon('heroicon-o-translate')
                ->size('sm')
                ->sortable(false)
                ->formatStateUsing(fn ($record): string => static::getTranslationPreview($record, 50)),
        ];

        foreach (config('translation-manager.available_locales') as $locale) {
            $localeCode = $locale['code'];

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
            'quick-translate' => QuickTranslate::route('/quick-translate'),
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
        return __('translation-manager::translations.translation-navigation-label');
    }

    protected static function getNavigationGroup(): ?string
    {
        return config('translation-manager.navigation_group');
    }

    public static function getEloquentQuery(): Builder
    {
        if(!is_array(config('translation-manager.hide_translation_groups')) || count(config('translation-manager.hide_translation_groups')) == 0){
          return parent::getEloquentQuery()->whereNotIn('group',config('translation-manager.hide_translation_groups'));
        }
        return parent::getEloquentQuery();
    }
}
