<?php

namespace Kenepa\TranslationManager\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class SetLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $fallbackLocale = config('app.fallback_locale', 'en');

        if (! $request->hasSession()) {
            $request->session()->put('language', $fallbackLocale);
        }

        App::setLocale($request->session()->get('language', $fallbackLocale));

        return $next($request);
    }
}
