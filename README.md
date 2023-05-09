# Translation Manager

<a href="https://github.com/kenepa/translation-manager">
<img style="width: 100%; max-width: 100%;" alt="filament-shield-art" src="https://raw.githubusercontent.com/kenepa/Kenepa/main/art/TranslationManager/filament-translation-manager-banner.png" >
</a>

[![Latest Version on Packagist](https://img.shields.io/packagist/v/kenepa/translation-manager.svg?style=flat-square)](https://packagist.org/packages/kenepa/translation-manager)
[![Total Downloads](https://img.shields.io/packagist/dt/kenepa/translation-manager.svg?style=flat-square)](https://packagist.org/packages/kenepa/translation-manager)

Introducing our Filament translation management tool, which allows you to easily manage, preview, and sync translations with your language files all within your Filament admin dashboard. Say goodbye to relying on developers to edit language files and streamline your localization workflow today.

<a href="https://github.com/kenepa/translation-manager">
<img style="width: 100%; max-width: 100%;" alt="filament-shield-art" src="https://raw.githubusercontent.com/kenepa/Kenepa/main/art/TranslationManager/translation-manager-promo.png" >
</a>

## Installation

You can install the package via composer:

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
    $table->json('text')->default('[]');
    $table->timestamps();
});
```

Finally, run the migration.

## (Optional) Enable the middleware
If you want to make use of the language switcher, you have to enable the middleware.  
First in `app/Http/Kernel.php` under the 'web' middleware group:  
```php
protected $middlewareGroups = [
    'web' => [
        // ... 
        // Add the middleware to the array
        \Kenepa\TranslationManager\Http\Middleware\SetLanguage::class,
    ]
];
```
Secondly in `config/filament.php`:
```php
'middleware' => [
    'auth' => [/* ... */],
    'base' => [
        // ... 
        // Add the middleware to the array
        \Kenepa\TranslationManager\Http\Middleware\SetLanguage::class,
    ]
]
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
    ['code' => 'en', 'name' => 'English', 'emoji' => '🇬🇧'],
    ['code' => 'nl', 'name' => 'Nederlands', 'emoji' => '🇳🇱'],
    ['code' => 'de', 'name' => 'Deutsch', 'emoji' => '🇩🇪']
]
```

#### `language_switcher`
Enable or disable the language switcher feature. This allows users to switch their language - disable if you have your own implementation.  
![Language Switcher](.github/language-switcher.png)

## Usage

Once installed, the Translation Manager can be accessed via the Filament sidebar menu. Simply click on the "Translation Manager" link to access the translation management screen.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.