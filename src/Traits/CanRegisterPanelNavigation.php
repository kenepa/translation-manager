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

        if (in_array(
            Filament::getCurrentPanel()->getId(),
            config('translation-manager.dont_register_navigation_on_panel_ids')
        )) {
            return false;
        }

        return true;
    }
}
