<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = 'vi';
        if (Auth::check() && Auth::user()->locale) {
            $locale = Auth::user()->locale;
        } else if (session('locale')) {
            $locale = session('locale');
        }
        app()->setLocale($locale);
        return $next($request);
    }
}


