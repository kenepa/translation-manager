<?php

use Illuminate\Support\Facades\Route;

if (config('filament-translation-manager.language_switcher')) {
    $availableCodes = collect(config('filament-translation-manager.available_locales'))
        ->pluck('code')
        ->toArray();

    Route::group(['middleware' => ['web']], function () use ($availableCodes) {
        Route::get('select-language/{code}', function ($code) {
            request()->session()->put('language', $code);

            return redirect()->back();
        })->whereIn('code', $availableCodes)
            ->name('filament-translation-manager.switch');
    });
}
