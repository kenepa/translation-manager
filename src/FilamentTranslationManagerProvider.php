<?php

namespace musa11971\FilamentTranslationManager;

use Exception;
use Filament\Facades\Filament;
use Filament\PluginServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use musa11971\FilamentTranslationManager\Resources\LanguageLineResource;
use Spatie\LaravelPackageTools\Package;

class FilamentTranslationManagerProvider extends PluginServiceProvider
{
    /**
     * The resources that the plugin registers.
     *
     * @var array|string[]
     */
    protected array $resources = [
        LanguageLineResource::class,
    ];

    /**
     * Configure the Filament Translation Manager package.
     */
    public function configurePackage(Package $package): void
    {
        $package->name('filament-translation-manager')
            ->hasViews()
            ->hasConfigFile()
            ->hasRoute('web')
            ->hasTranslations();
    }

    /**
     * Boot the service provider.
     *
     * @return FilamentTranslationManagerProvider|void
     */
    public function boot()
    {
        parent::boot();

        $this->verifyConfig();
        $this->registerLanguageSwitcher();
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

    /**
     * Verify that the package and application configuration files have the required values.
     *
     * @return void
     *
     * @throws Exception
     */
    private function verifyConfig()
    {
        $packageConfig = config('filament-translation-manager');
        $appConfig = config('app');

        $packageValidator = Validator::make($packageConfig, [
            'available_locales' => ['required', 'array', 'min:1'],
            'disable_key_and_group_editing' => ['required', 'boolean'],
            'language_switcher' => ['required', 'boolean'],
        ]);

        $appValidator = Validator::make($appConfig, [
            'locale' => ['required', 'string'],
            'fallback_locale' => ['required', 'string'],
        ]);

        if ($packageValidator->fails() || $appValidator->fails()) {
            $messages = $packageValidator->errors()->merge($appValidator->errors());

            throw new Exception('Config file is not valid. ' . $messages->first());
        }
    }

    /**
     * Register the language switcher view, if enabled.
     *
     * @return void
     */
    private function registerLanguageSwitcher()
    {
        Filament::serving(function () {
            if (config('filament-translation-manager.language_switcher')) {
                Filament::registerRenderHook(
                    'global-search.end',
                    fn (): View => $this->getLanguageSwitcherView()
                );
            }
        });
    }
}
