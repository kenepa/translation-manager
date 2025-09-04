<?php

use Illuminate\Support\Facades\Route;
use Kenepa\TranslationManager\TranslationManagerPlugin;

if (config('translation-manager.language_switcher')) {
    $availableCodes = collect(TranslationManagerPlugin::get()->getAvailableLocales())
        ->pluck('code')
        ->toArray();

    Route::group(['middleware' => ['web']], function () use ($availableCodes) {
        Route::get('select-language/{code}', function ($code) {
            request()->session()->put('language', $code);

            return redirect()->back();
        })->whereIn('code', $availableCodes)
            ->name('translation-manager.switch');
    });
}
