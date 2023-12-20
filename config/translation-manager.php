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

    /*
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
    | 'navigation_group' is the group in which the translation manager is displayed.
    | For instance, it could be set to 'Admin'.
    |
    | 'navigation_group_translation_key' is used for a translation key.
    | For example, it could be set to 'navigation.manageSettings'.
    | If this key is filled, it will override navigation_group settings.
    | Set it to null if you do not want to use it.
    |
    */

    'navigation_group_translation_key' => null,

    'navigation_group' => null,

    /*
    |--------------------------------------------------------------------------
    | Navigation Icon
    |--------------------------------------------------------------------------
    |
    | The navigation icon to use. Set `false` to disable the icon
    | or specify a custom icon
    |
    */

    'navigation_icon' => 'heroicon-o-globe-alt',

    /*
    |--------------------------------------------------------------------------
    | Quick-Translate Navigation Registration
    |--------------------------------------------------------------------------
    |
    | Whether to register the quick-translate page in navigation.
    |
    */

    'quick_translate_navigation_registration' => true,

    /*
    |--------------------------------------------------------------------------
    | Don't Register Navigation On Panels
    |--------------------------------------------------------------------------
    |
    | Array of panel id's which not to register navigation on.
    | i.e. => ['guest', 'team1']
    |
    */

    'dont_register_navigation_on_panel_ids' => [],

    /*
    |--------------------------------------------------------------------------
    | Flags or Initials
    |--------------------------------------------------------------------------
    |
    | Control whether to express locales using international flags, or through locale initial letters if disabled in the language switcher.
    |
    */

    'show_flags' => true,

    /*
   |--------------------------------------------------------------------------
   | Prepend directory path to group name
   |--------------------------------------------------------------------------
   |
   | Control whether to append the directory path to the group name.
   | ex. If the translation file for settings is in directory lang/en/settings/user.php
   | The group name will be settings/user
   |
   */

    'prepend_directory_path_to_group_name' => false,
];
