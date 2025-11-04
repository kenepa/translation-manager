<?php

namespace Kenepa\TranslationManager;

use Exception;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\View\View;
use Kenepa\TranslationManager\Http\Middleware\SetLanguage;
use Kenepa\TranslationManager\Pages\QuickTranslate;
use Kenepa\TranslationManager\Resources\LanguageLineResource;

class TranslationManagerPlugin implements Plugin
{
    protected static ?self $instance = null;
    protected ?array $availableLocales = null;

    protected ?bool $disableKeyAndGroupEditing = null;

    protected ?bool $languageSwitcher = null;

    protected ?string $languageSwitcherRenderHook = null;

    protected ?string $navigationGroupTranslationKey = null;

    protected ?string $navigationGroup = null;

    protected ?string $cluster = null;

    protected mixed $navigationIcon = null;

    protected ?bool $quickTranslateNavigationRegistration = null;

    protected ?array $dontRegisterNavigationOnPanelIds = null;

    protected ?bool $showFlags = null;

    protected ?bool $prependDirectoryPathToGroupName = null;

    public static function make(): static
    {
        if (static::$instance === null) {
            static::$instance = app(static::class);
        }

        return static::$instance;
    }

    public static function get(): static
    {
        try {
            /** @var static $plugin */
            $plugin = filament(app(static::class)->getId());

            return $plugin;
        } catch (Exception $e) {
            // Fallback to singleton when no panel context is available
            return static::make();
        }
    }

    public function getId(): string
    {
        return 'translation-manager';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                LanguageLineResource::class,
            ])
            ->pages([
                QuickTranslate::class,
            ]);

        if ($this->shouldEnableLanguageSwitcher()) {
            $panel->renderHook(
                $this->getLanguageSwitcherRenderHook(),
                fn (): View => $this->getLanguageSwitcherView()
            );

            $panel->authMiddleware([
                SetLanguage::class,
            ]);
        }
    }

    public function boot(Panel $panel): void
    {
        //
    }

    // Configuration methods

    public function availableLocales(array $locales): static
    {
        $this->availableLocales = $locales;

        return $this;
    }

    public function getAvailableLocales(): array
    {
        return $this->availableLocales ?? config('translation-manager.available_locales', [
            ['code' => 'en', 'name' => 'English', 'flag' => 'gb'],
        ]);
    }

    public function disableKeyAndGroupEditing(bool $disable = true): static
    {
        $this->disableKeyAndGroupEditing = $disable;

        return $this;
    }

    public function shouldDisableKeyAndGroupEditing(): bool
    {
        return $this->disableKeyAndGroupEditing ?? config('translation-manager.disable_key_and_group_editing', true);
    }

    public function languageSwitcher(bool $enable = true): static
    {
        $this->languageSwitcher = $enable;

        return $this;
    }

    public function shouldEnableLanguageSwitcher(): bool
    {
        return $this->languageSwitcher ?? config('translation-manager.language_switcher', true);
    }

    public function languageSwitcherRenderHook(string $hook): static
    {
        $this->languageSwitcherRenderHook = $hook;

        return $this;
    }

    public function getLanguageSwitcherRenderHook(): string
    {
        return $this->languageSwitcherRenderHook ?? config('translation-manager.language_switcher_render_hook', 'panels::user-menu.before');
    }

    public function navigationGroupTranslationKey(?string $key): static
    {
        $this->navigationGroupTranslationKey = $key;

        return $this;
    }

    public function getNavigationGroupTranslationKey(): ?string
    {
        return $this->navigationGroupTranslationKey ?? config('translation-manager.navigation_group_translation_key');
    }

    public function navigationGroup(?string $group): static
    {
        $this->navigationGroup = $group;

        return $this;
    }

    public function getNavigationGroup(): ?string
    {
        if ($this->getNavigationGroupTranslationKey()) {
            return __($this->getNavigationGroupTranslationKey());
        }

        return $this->navigationGroup ?? config('translation-manager.navigation_group');
    }

    public function cluster(?string $cluster): static
    {
        $this->cluster = $cluster;

        return $this;
    }

    public function getCluster(): ?string
    {
        return $this->cluster ?? config('translation-manager.cluster');
    }

    public function navigationIcon(mixed $icon): static
    {
        $this->navigationIcon = $icon;

        return $this;
    }

    public function getNavigationIcon(): ?string
    {
        if (($this->navigationIcon ?? config('translation-manager.navigation_icon')) === false) {
            return null;
        }

        return $this->navigationIcon ?? config('translation-manager.navigation_icon', 'heroicon-o-globe-alt');
    }

    public function quickTranslateNavigationRegistration(bool $register = true): static
    {
        $this->quickTranslateNavigationRegistration = $register;

        return $this;
    }

    public function shouldRegisterQuickTranslateNavigation(): bool
    {
        return $this->quickTranslateNavigationRegistration ?? config('translation-manager.quick_translate_navigation_registration', true);
    }

    public function dontRegisterNavigationOnPanelIds(array $panelIds): static
    {
        $this->dontRegisterNavigationOnPanelIds = $panelIds;

        return $this;
    }

    public function getDontRegisterNavigationOnPanelIds(): array
    {
        return $this->dontRegisterNavigationOnPanelIds ?? config('translation-manager.dont_register_navigation_on_panel_ids', []);
    }

    public function showFlags(bool $show = true): static
    {
        $this->showFlags = $show;

        return $this;
    }

    public function shouldShowFlags(): bool
    {
        return $this->showFlags ?? config('translation-manager.show_flags', true);
    }

    public function prependDirectoryPathToGroupName(bool $prepend = true): static
    {
        $this->prependDirectoryPathToGroupName = $prepend;

        return $this;
    }

    public function shouldPrependDirectoryPathToGroupName(): bool
    {
        return $this->prependDirectoryPathToGroupName ?? config('translation-manager.prepend_directory_path_to_group_name', false);
    }

    /**
     * Returns a View object that renders the language switcher component.
     *
     * @return \Illuminate\Contracts\View\View The View object that renders the language switcher component.
     */
    private function getLanguageSwitcherView(): View
    {
        $locales = $this->getAvailableLocales();
        $currentLocale = app()->getLocale();
        $currentLanguage = collect($locales)->firstWhere('code', $currentLocale);
        $otherLanguages = $locales;
        $showFlags = $this->shouldShowFlags();

        return view('translation-manager::language-switcher', compact(
            'otherLanguages',
            'currentLanguage',
            'showFlags',
        ));
    }
}
