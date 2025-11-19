{{--
    Layout: guest.blade.php
    - Layout cho khách/unauthenticated: khung đơn giản cho login/register/landing.
    - Gồm header đơn giản (logo), slot nội dung trang con.
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'QuickPoll') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('Logo.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-[color:var(--on-surface)] antialiased bg-[var(--surface-variant)]">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div class="text-center">
                <a href="/" class="inline-flex items-center justify-center rounded-full bg-[var(--surface)]/70 shadow ring-1 ring-[color:var(--outline)] p-3 backdrop-blur">
                    <x-application-logo class="w-10 h-10 fill-current text-indigo-600 dark:text-indigo-400" />
                </a>
                <p class="mt-3 text-sm text-[color:var(--on-surface-variant)]">Chào mừng đến QuickPoll</p>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-6 bg-[var(--surface)]/90 shadow-xl sm:rounded-2xl ring-1 ring-[color:var(--outline)] backdrop-blur">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
