<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale', 'vi');
        if (!in_array($locale, ['vi','en'])) {
            $locale = 'vi';
        }
        app()->setLocale($locale);
        return $next($request);
    }
}


