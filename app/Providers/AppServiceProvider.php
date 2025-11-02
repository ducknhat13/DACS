<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS cho tất cả URLs khi không phải local environment
        // Render và các proxy servers sẽ forward HTTPS requests
        // Kiểm tra cả production env và X-Forwarded-Proto header
        $isProduction = app()->environment('production');
        $hasHttpsHeader = request()->header('X-Forwarded-Proto') === 'https';
        $hasHttpsInUrl = str_starts_with(config('app.url', ''), 'https://');
        
        // Force HTTPS nếu một trong các điều kiện sau đúng:
        // 1. Production environment
        // 2. Có X-Forwarded-Proto: https header (Render sẽ set)
        // 3. APP_URL đã có https://
        if ($isProduction || $hasHttpsHeader || $hasHttpsInUrl) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
