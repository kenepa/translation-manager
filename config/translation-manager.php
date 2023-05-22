<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Available Application Locales
    |--------------------------------------------------------------------------
    |
    | The available application locales that can be used.
    |
    */

    'available_locales' => [
        ['code' => 'en', 'name' => 'English', 'emoji' => '🇬🇧', 'flag' => 'gb'],
        // ['code' => 'nl', 'name' => 'Nederlands', 'emoji' => '🇳🇱', 'flag' => 'nl'] ,
    ],

    /*
    |--------------------------------------------------------------------------
    | Disable key and group editing
    |--------------------------------------------------------------------------
    |
    | Whether editing the key and group values is disabled. By default, this is true
    | because these values are automatically added by the synchronization process.
    |
    */

    'disable_key_and_group_editing' => true,

    /*
    |--------------------------------------------------------------------------
    | Emoji usage
    |--------------------------------------------------------------------------
    |
    | Set this value true to use flag icons instead of emojis.
    | stijnvanouplines/blade-country-flags package is required for this change.
    |
    */

    'use_emoji' => false,

    /*
    |--------------------------------------------------------------------------
    | Language Switcher
    |--------------------------------------------------------------------------
    |
    | Enable the language switcher feature in the Filament top bar.
    |
    */

    'language_switcher' => true,

    /*
    |--------------------------------------------------------------------------
    | Navigation Group
    |--------------------------------------------------------------------------
    |
    | The navigation group the translation manager is shown in, for example: 'Admin'.
    |
    */

    'navigation_group' => null,

];
