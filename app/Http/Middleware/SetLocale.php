<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

/**
 * SetLocale Middleware - Set application locale (ngôn ngữ) cho mỗi request
 * 
 * Middleware này được chạy trước mỗi request để set locale cho application
 * 
 * Priority order:
 * 1. User locale (nếu đã login và có locale trong database)
 * 2. Session locale (nếu có trong session)
 * 3. Default: 'vi' (tiếng Việt)
 * 
 * Locale được dùng để:
 * - Hiển thị translations từ resources/lang/{locale}/messages.php
 * - Email notifications theo ngôn ngữ user
 * - Date/time formatting theo locale
 * 
 * @author QuickPoll Team
 */
class SetLocale
{
    /**
     * Handle an incoming request.
     * 
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        /**
         * Xác định locale với priority:
         * 1. User locale (nếu logged in và có locale)
         * 2. Session locale (nếu có)
         * 3. Default: 'vi'
         */
        $locale = 'vi';
        if (Auth::check() && Auth::user()->locale) {
            $locale = Auth::user()->locale; // Ưu tiên: User locale từ database
        } else if (session('locale')) {
            $locale = session('locale'); // Fallback: Session locale
        }
        
        // Set locale cho Laravel application (dùng cho __() helper)
        app()->setLocale($locale);
        return $next($request);
    }
}


