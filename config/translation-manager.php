<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Available Application Locales
    |--------------------------------------------------------------------------
    |
    | The available application locales that can be used.
    | For flag codes, please refer to https://flagicons.lipis.dev/ (e.g. nl for Netherlands).
    |
    */

    'available_locales' => [
        ['code' => 'en', 'name' => 'English', 'flag' => 'gb'],
        // ['code' => 'nl', 'name' => 'Nederlands', 'flag' => 'nl'] ,
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
    | Language Switcher
    |--------------------------------------------------------------------------
    |
    | Enable the language switcher feature in the Filament top bar.
    |
    */

    'language_switcher' => true,

    /**
    |--------------------------------------------------------------------------
     |
     | Determines the render hook for the language switcher.
     | Available render hooks: https://filamentphp.com/docs/3.x/support/render-hooks#available-render-hooks
     |
     */
    'language_switcher_render_hook' => 'panels::user-menu.before',

    /*
    |--------------------------------------------------------------------------
    | Navigation Group
    |--------------------------------------------------------------------------
    |
    | The navigation group the translation manager is shown in, for example: 'Admin'.
    |
    */

    'navigation_group' => null,

    /*
    |--------------------------------------------------------------------------
    | Flags or Initials
    |--------------------------------------------------------------------------
    |
    | Control whether to express locales using international flags, or through locale initial letters if disabled.
    |
    */

    'show-flags' => true,

];
