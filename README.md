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
| <= 3.x         | 2.x   | \> 8.0      |
| 4.x            | 3.x             | \> 8.1      |

```bash
composer require kenepa/translation-manager
```

You can run the following command to publish the configuration file:
```bash
php artisan vendor:publish --tag=translation-manager-config
```

This package uses `spatie/laravel-translation-loader`, publish their migration file using:
```bash
php artisan vendor:publish --provider="Spatie\TranslationLoader\TranslationServiceProvider" --tag="migrations"
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

In order to compile the package views correctly, we need to [create a custom Filament theme](https://filamentphp.com/docs/3.x/panels/themes#creating-a-custom-theme) **first**, and then add the following path to its content:

```js
// Located at: /resources/css/filament/admin/tailwind.config.js

import preset from '../../../../vendor/filament/filament/tailwind.config.preset';

export default {
    presets: [preset],
    content: [
        // other content...
        './vendor/kenepa/translation-manager/resources/**/*.blade.php',
    ],
};
```


## Register the plugin with a panel

```php
use Kenepa\ResourceLock\ResourceLockPlugin;
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


## Authorization

By default, the translation manager cannot be used by anyone. You need to define the following gate in your `AppServiceProvider` boot method:

```php
Gate::define('use-translation-manager', function (?User $user) {
    // Your authorization logic
    return $user !== null && $user->hasRole('admin');
});
```


## Configuration
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

## Usage

Once installed, the Translation Manager can be accessed via the Filament sidebar menu. Simply click on the "Translation Manager" link to access the translation management screen.


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
