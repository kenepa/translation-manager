<?php

namespace Kenepa\TranslationManager\Traits;

use Filament\Facades\Filament;
use Kenepa\TranslationManager\TranslationManagerPlugin;

trait CanRegisterPanelNavigation
{
    public static function shouldRegisterOnPanel(): bool
    {
        $dontRegisterIds = TranslationManagerPlugin::get()->getDontRegisterNavigationOnPanelIds();

        if (empty($dontRegisterIds)) {
            return true;
        }

        if (in_array(
            Filament::getCurrentPanel()->getId(),
            $dontRegisterIds
        )) {
            return false;
        }

        return true;
    }
}
