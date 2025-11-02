<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Đảm bảo SetLocale chạy trong nhóm 'web' sau khi session/auth sẵn sàng
        $middleware->appendToGroup('web', \App\Http\Middleware\SetLocale::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Log tất cả exceptions ra stderr để có thể xem trong Render logs
        $exceptions->render(function (\Throwable $e, Request $request) {
            // Log error details
            error_log("Exception: " . get_class($e));
            error_log("Message: " . $e->getMessage());
            error_log("File: " . $e->getFile() . ":" . $e->getLine());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            // Return null để Laravel handle exception như bình thường
            return null;
        });
    })->create();
