<?php

namespace musa11971\FilamentTranslationManager;

use Filament\PluginServiceProvider;
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
            ->hasTranslations();
    }

    public function boot()
    {
        parent::boot();

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'filament-translation-manager');
    }
}
