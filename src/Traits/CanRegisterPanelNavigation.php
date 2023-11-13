<?php

namespace Kenepa\TranslationManager\Traits;

use Illuminate\Support\Facades\Route;

trait CanRegisterPanelNavigation
{
    static function shouldRegisterOnPanel() : bool
    {
        if(empty(config('translation-manager.dont_register_navigation_on_panel_ids'))) {
            return true;
        }

        $routeName = Route::getCurrentRoute()?->getName();
        foreach (config('translation-manager.dont_register_navigation_on_panel_ids') as $panelName) {
            if(str_starts_with($routeName, 'filament.' . $panelName)) {
                return false;
            }
        }
        return true;
    }
}
