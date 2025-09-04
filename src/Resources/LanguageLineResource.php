<?php

namespace Kenepa\TranslationManager\Resources;

use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Kenepa\TranslationManager\Filters\NotTranslatedFilter;
use Kenepa\TranslationManager\Pages\QuickTranslate;
use Kenepa\TranslationManager\Resources\LanguageLineResource\Pages\EditLanguageLine;
use Kenepa\TranslationManager\Resources\LanguageLineResource\Pages\ListLanguageLines;
use Kenepa\TranslationManager\TranslationManagerPlugin;
use Kenepa\TranslationManager\Traits\CanRegisterPanelNavigation;
use Spatie\TranslationLoader\LanguageLine;

class LanguageLineResource extends Resource
{
    use CanRegisterPanelNavigation;
    protected static ?string $model = LanguageLine::class;
    protected static string|null|BackedEnum $navigationIcon = 'heroicon-o-globe-alt';
    protected static ?string $slug = 'translation-manager';

    /**
     * @param  array<string, mixed>  $parameters
     */
    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return static::shouldRegisterOnPanel();
    }

    public static function getLabel(): ?string
    {
        return trans_choice('translation-manager::translations.translation-label', 1);
    }

    public static function getPluralLabel(): ?string
    {
        return trans_choice('translation-manager::translations.translation-label', 2);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('group')
                    ->prefixIcon('heroicon-o-tag')
                    ->disabled(TranslationManagerPlugin::get()->shouldDisableKeyAndGroupEditing())
                    ->label(__('translation-manager::translations.group'))
                    ->required(),

                TextInput::make('key')
                    ->prefixIcon('heroicon-o-key')
                    ->disabled(TranslationManagerPlugin::get()->shouldDisableKeyAndGroupEditing())
                    ->label(__('translation-manager::translations.key'))
                    ->required(),

                ViewField::make('preview')
                    ->view('translation-manager::preview-translation')
                    ->disabled()
                    ->columnSpan(2),

                Section::make(__('translation-manager::translations.translations-header'))
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('translations')->schema([
                            Select::make('language')
                                ->prefixIcon('heroicon-o-language')
                                ->label(__('translation-manager::translations.translation-language'))
                                ->options(collect(TranslationManagerPlugin::get()->getAvailableLocales())->pluck('name', 'code'))
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                ->columnSpanFull()
                                ->required(),

                            Textarea::make('text')
                                ->label(__('translation-manager::translations.translation-text'))
                                ->columnSpanFull()
                                ->required(),
                        ])
                            ->columns(2)
                            ->addActionLabel(__('translation-manager::translations.add-translation-button'))
                            ->hiddenLabel()
                            ->defaultItems(0)
                            ->reorderable(false)
                            ->grid([
                                'default' => 1,
                                'sm' => 1,
                                'md' => 2,
                                'xl' => 3,
                                '2xl' => 4,
                            ])
                            ->columnSpan(2)
                            ->maxItems(count(TranslationManagerPlugin::get()->getAvailableLocales())),
                    ]),
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
            ->bulkActions([])
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(25);
    }

    public static function getColumns(): array
    {

        $columns = [
            TextColumn::make('group_and_key')
                ->label(__('translation-manager::translations.group') . ' & ' . __('translation-manager::translations.key'))
                ->searchable(query: function (Builder $query, string $search): Builder {
                    return $query
                        ->where('group', 'like', "%{$search}%")
                        ->orWhere('key', 'like', "%{$search}%");
                })
                ->getStateUsing(function (Model $record) {
                    return $record->group . '.' . $record->key;
                }),

            ViewColumn::make('preview')
                ->view('translation-manager::preview-column')
                ->searchable(query: function (Builder $query, string $search): Builder {
                    return $query
                        ->where('text', 'like', "%{$search}%");
                })
                ->label(__('translation-manager::translations.preview-in-your-lang', ['lang' => app()->getLocale()]))
                ->sortable(false),
        ];

        foreach (TranslationManagerPlugin::get()->getAvailableLocales() as $locale) {
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

    public static function canViewAny(): bool
    {
        return static::shouldRegisterOnPanel() ? Gate::allows('use-translation-manager') : false;
    }

    public static function canEdit(Model $record): bool
    {
        return static::shouldRegisterOnPanel() ? Gate::allows('use-translation-manager') : false;
    }

    public static function getNavigationLabel(): string
    {
        return __('translation-manager::translations.translation-navigation-label');
    }

    public static function getNavigationIcon(): ?string
    {
        return TranslationManagerPlugin::get()->getNavigationIcon();
    }

    public static function getNavigationGroup(): ?string
    {
        return TranslationManagerPlugin::get()->getNavigationGroup();
    }

    public static function getCluster(): ?string
    {
        return TranslationManagerPlugin::get()->getCluster();
    }
}
