<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Обработка входящего запроса для установки языка интерфейса.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session()->has('app_locale')) {
            $locale = session('app_locale');
            if (in_array($locale, ['ru', 'ka', 'en'])) {
                App::setLocale($locale);
            }
        }

        return $next($request);
    }
}