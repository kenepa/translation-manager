<?php

namespace musa11971\FilamentTranslationManager;

use Filament\Facades\Filament;
use Filament\PluginServiceProvider;
use Illuminate\View\View;
use musa11971\FilamentTranslationManager\Resources\LanguageLineResource;
use Spatie\LaravelPackageTools\Package;

class FilamentTranslationManagerProvider extends PluginServiceProvider
{
    protected array $resources = [
        LanguageLineResource::class,
    ];

    public function configurePackage(Package $package): void
    {
        $package->name('filament-translation-manager')
            ->hasViews()
            ->hasConfigFile()
            ->hasRoute('web')
            ->hasTranslations();
    }

    public function boot()
    {
        parent::boot();

        Filament::serving(function () {
            if (config('filament-translation-manager.language_switcher')) {
                Filament::registerRenderHook(
                    'global-search.end',
                    fn (): View => $this->getLanguageSwitcherView()
                );
            }
        });

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'filament-translation-manager');
    }

    /**
     * Returns a View object that renders the language switcher component.
     *
     * @return \Illuminate\Contracts\View\View The View object that renders the language switcher component.
     */
    private function getLanguageSwitcherView(): View
    {
        $locales = config('filament-translation-manager.available_locales');
        $currentLocale = app()->getLocale();
        $currentLanguage = collect($locales)->firstWhere('code', $currentLocale);

        $currentLanguageEmoji = $currentLanguage ? $currentLanguage['emoji'] : '🌐';
        $otherLanguages = $locales;

        return view('filament-translation-manager::language-switcher', compact('currentLanguageEmoji', 'otherLanguages'));
    }
}
