# Translation Manager

<a href="https://github.com/kenepa/translation-manager">
<img class="filament-hidden" style="width: 100%; max-width: 100%;" alt="filament-translation-manager-art" src="https://raw.githubusercontent.com/kenepa/Kenepa/main/art/TranslationManager/filament-translation-manager-banner.png" >
</a>

[![Latest Version on Packagist](https://img.shields.io/packagist/v/kenepa/translation-manager.svg?style=flat-square)](https://packagist.org/packages/kenepa/translation-manager)
[![Total Downloads](https://img.shields.io/packagist/dt/kenepa/translation-manager.svg?style=flat-square)](https://packagist.org/packages/kenepa/translation-manager)

Introducing our Filament translation management tool, which allows you to easily manage, preview, and sync translations with your language files all within your Filament admin dashboard. Say goodbye to relying on developers to edit language files and streamline your localization workflow today.

<a href="https://github.com/kenepa/translation-manager">
<img style="width: 100%; max-width: 100%;" alt="filament-translation-manager-art" src="https://raw.githubusercontent.com/kenepa/Kenepa/main/art/TranslationManager/translation-manager-promo.png" >
</a>

## Installation

You can install the package via composer:

Install via Composer.

| Plugin Version | Filament Version | PHP Version |
|----------------|-----------------|-------------|
| <= 3.x         | 2.x             | \> 8.0      |
| 4.x            | 3.x             | \> 8.1      |
| 5.x            | 4.x or 5.x      | \> 8.2      |

```bash
composer require kenepa/translation-manager
```

This package uses `spatie/laravel-translation-loader`, publish their migration file using:
```bash
php artisan vendor:publish --provider="Spatie\TranslationLoader\TranslationServiceProvider" --tag="translation-loader-migrations"
php artisan migrate
```

You have to update the migration file to the following:
```php
Schema::create('language_lines', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->string('group')->index();
    $table->string('key')->index();
    $table->json('text')->default(new \Illuminate\Database\Query\Expression('(JSON_ARRAY())'));
    $table->timestamps();
});
```

Finally, run the migration.

### Custom Theme Required

In order to compile the package views correctly, we need to [create a custom Filament theme](https://filamentphp.com/docs/3.x/panels/themes#creating-a-custom-theme) **first**, and then add the following path to its content. In the `theme.css` file of the theme, add the following line:

```css
@source '../../../../vendor/kenepa/translation-manager/resources/**/*.blade.php';
```


## Register the plugin with a panel

```php
use Kenepa\TranslationManager\TranslationManagerPlugin;
use Filament\Panel;
 
class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            // ...
            ->plugin(TranslationManagerPlugin::make());
    }
}
```

## Configuration

**From version 5.x onwards, the main configuration is done through the plugin class.** The traditional config file is still supported for  compatibility, but all new configurations should be done through the plugin.

### Plugin Configuration

Configure the plugin using fluent method chaining:

```php
use Kenepa\TranslationManager\TranslationManagerPlugin;

TranslationManagerPlugin::make()
    ->availableLocales([
        ['code' => 'en', 'name' => 'English', 'flag' => 'gb'],
        ['code' => 'nl', 'name' => 'Nederlands', 'flag' => 'nl'],
        ['code' => 'fr', 'name' => 'Français', 'flag' => 'fr'],
        ['code' => 'de', 'name' => 'Deutsch', 'flag' => 'de'],
    ])
    ->languageSwitcher(true)
    ->languageSwitcherRenderHook('panels::user-menu.before')
    ->navigationGroup('Settings')
    ->navigationIcon('heroicon-o-globe-alt')
    ->showFlags(true)
    ->disableKeyAndGroupEditing(false)
    ->quickTranslateNavigationRegistration(true)
    ->dontRegisterNavigationOnPanelIds(['guest'])
    ->prependDirectoryPathToGroupName(false)
```

#### Available Configuration Methods

- `availableLocales(array $locales)` - Set available application locales
- `disableKeyAndGroupEditing(bool $disable = true)` - Control key/group editing
- `languageSwitcher(bool $enable = true)` - Enable/disable language switcher
- `languageSwitcherRenderHook(string $hook)` - Set render hook for language switcher
- `navigationGroupTranslationKey(?string $key)` - Set navigation group translation key
- `navigationGroup(?string $group)` - Set navigation group
- `cluster(?string $cluster)` - Set cluster
- `navigationIcon(mixed $icon)` - Set navigation icon (supports `false` to disable)
- `quickTranslateNavigationRegistration(bool $register = true)` - Control quick translate navigation
- `dontRegisterNavigationOnPanelIds(array $panelIds)` - Exclude panels from navigation
- `showFlags(bool $show = true)` - Show flags in language switcher
- `prependDirectoryPathToGroupName(bool $prepend = true)` - Control group naming

### Config File

You can run the following command to publish the configuration file:
```bash
php artisan vendor:publish --tag=translation-manager-config
```


## Authorization

By default, the translation manager cannot be used by anyone. You need to define the following gate in your `AppServiceProvider` boot method:

```php
// app/Providers/AppServiceProvider.php

use Illuminate\Support\Facades\Gate;

/**
 * Bootstrap any application services.
 */
public function boot(): void
{   
    Gate::define('use-translation-manager', function (?User $user) {
        // Your authorization logic
        return $user !== null && $user->hasRole('admin');
    });
}
```
If you want to learn more about gates, [check out the official documentation](https://laravel.com/docs/master/authorization#gates).

### Legacy Configuration Examples

#### `available_locales`
Determines which locales your application supports. For example:
```php
'available_locales' => [
    ['code' => 'en', 'name' => 'English', 'flag' => 'gb'],
    ['code' => 'nl', 'name' => 'Nederlands', 'flag' => 'nl'],
    ['code' => 'de', 'name' => 'Deutsch', 'flag' => 'de']
]
```

#### `language_switcher`
Enable or disable the language switcher feature. This allows users to switch their language - disable if you have your own implementation.  
![Language Switcher](https://raw.githubusercontent.com/kenepa/translation-manager/4.x/.github/language-switcher.png)

#### `dont_register_navigation_on_panel_ids`
Disable registering the translation manager navigation on certain panel IDs. The following will disable the translation navigation for the guest panel but still allow guest panel users to change the language.
```php
    'dont_register_navigation_on_panel_ids' => [
        'guest'
    ],
```

#### Adding to cluster
Example of adding the translation manager to a cluster:
```php
// config/translation-manager.php
[
  // ...Other config   
 'cluster' => \App\Filament\Clusters\Products::class,
]
```

## Usage

Once installed, the Translation Manager can be accessed via the Filament sidebar menu. Simply click on the "Translation Manager" link to access the translation management screen.


## Upgrade Guide

### Upgrading to Filament 5.x (Livewire 4)

Filament 5 introduces **Livewire 4 + Tailwind 4** support. There are **no API breaking changes** — your plugin configuration and panel setup remain identical.

#### Prerequisites
- **PHP**: 8.2+
- **Laravel**: 11.28+
- **Filament**: Upgrade to Filament 5.x
- **Livewire**: Upgrade to Livewire 4.x

#### Step 1: Upgrade Filament and Livewire

```bash
composer require livewire/livewire:"^4.0" filament/filament:"^5.0" -W
```

#### Step 2: Run the Filament upgrade script

```bash
composer require filament/upgrade:"^5.0" -W --dev
vendor/bin/filament-v5
```

#### Step 3: Rebuild your theme assets

Filament 5 requires **Tailwind CSS v4**. Rebuild your custom theme after upgrading:

```bash
npm install && npm run build
```

> No changes are required to your `TranslationManagerPlugin` configuration or any Blade views — everything works as-is with Filament 5.

---

### Upgrading from 4.x to 5.x

Version 5.x introduces **Filament v4 support** and a **new plugin-based configuration system**. Follow these steps to upgrade:

#### Prerequisites
- **PHP**: Upgrade to PHP 8.2+
- **Filament**: Upgrade to Filament 4.x

#### Step 1: Theme Configuration (Required)

**Breaking Change**: Filament v4 requires a different approach for including package assets.

**Remove from `tailwind.config.js` (if present):**
```js
// Remove this from your tailwind.config.js content array:
'./vendor/kenepa/translation-manager/resources/**/*.blade.php'
```

**Add to your custom theme CSS file:**

1. Create a custom theme if you don't have one ([Filament v4 theme docs](https://filamentphp.com/docs/4.x/panels/themes#creating-a-custom-theme))
2. Add this line to your theme's CSS file:

```css
@source '../../../../vendor/kenepa/translation-manager/resources/**/*.blade.php';
```

#### Step 2: Migrate Configuration (Recommended)

Migrate your config file settings to the plugin configuration:

```php
// In your AdminPanelProvider.php
use Kenepa\TranslationManager\TranslationManagerPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugin(
            TranslationManagerPlugin::make()
                ->availableLocales([
                    ['code' => 'en', 'name' => 'English', 'flag' => 'gb'],
                    ['code' => 'nl', 'name' => 'Nederlands', 'flag' => 'nl'],
                    ['code' => 'fr', 'name' => 'Français', 'flag' => 'fr'],
                ])
                ->languageSwitcher(true)
                ->languageSwitcherRenderHook('panels::user-menu.before')
                ->navigationGroup('Settings')
                ->navigationIcon('heroicon-o-globe-alt')
                ->showFlags(true)
                ->disableKeyAndGroupEditing(false)
                ->quickTranslateNavigationRegistration(true)
                ->dontRegisterNavigationOnPanelIds(['guest'])
        );
}
```


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
