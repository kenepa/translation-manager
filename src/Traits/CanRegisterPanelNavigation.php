<?php

namespace Kenepa\TranslationManager\Traits;

use Filament\Facades\Filament;

trait CanRegisterPanelNavigation
{
    public static function shouldRegisterOnPanel(): bool
    {
        if (empty(config('translation-manager.dont_register_navigation_on_panel_ids'))) {
            return true;
        }

        foreach (config('translation-manager.dont_register_navigation_on_panel_ids') as $panelName) {
            if (Filament::getCurrentPanel()->getId() === $panelName) {
                return false;
            }
        }

        return true;
    }
}
