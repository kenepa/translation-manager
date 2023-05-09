<?php

use Illuminate\Support\Facades\Route;

if (config('translation-manager.language_switcher')) {
    $availableCodes = collect(config('translation-manager.available_locales'))
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
