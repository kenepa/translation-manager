<?php

namespace Kenepa\TranslationManager;

use Exception;
use Illuminate\Support\Facades\Validator;
use Kenepa\TranslationManager\Commands\SynchronizeTranslationsCommand;
use Kenepa\TranslationManager\Resources\LanguageLineResource;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class TranslationManagerProvider extends PackageServiceProvider
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
     * Configure the Translation Manager package.
     */
    public function configurePackage(Package $package): void
    {
        $package->name('translation-manager')
            ->hasCommand(SynchronizeTranslationsCommand::class)
            ->hasViews()
            ->hasConfigFile()
            ->hasRoute('web')
            ->hasTranslations();
    }

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function packageBooted()
    {
        parent::packageBooted();

        $this->verifyConfig();
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'translation-manager');
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
        $packageConfig = config('translation-manager');
        $appConfig = config('app');

        $packageValidator = Validator::make($packageConfig, [
            'available_locales' => ['required', 'array', 'min:1'],
            'disable_key_and_group_editing' => ['required', 'boolean'],
            'language_switcher' => ['required', 'boolean'],
            'navigation_group' => ['nullable', 'string'],
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
}
