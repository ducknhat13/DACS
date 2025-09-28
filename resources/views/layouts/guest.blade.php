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
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-indigo-50 via-white to-cyan-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800">
            <div class="text-center">
                <a href="/" class="inline-flex items-center justify-center rounded-full bg-white/70 dark:bg-gray-800/60 shadow ring-1 ring-black/5 dark:ring-white/10 p-3 backdrop-blur">
                    <x-application-logo class="w-10 h-10 fill-current text-indigo-600 dark:text-indigo-400" />
                </a>
                <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">Chào mừng đến QuickPoll</p>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-6 bg-white/90 dark:bg-gray-800/90 shadow-xl sm:rounded-2xl ring-1 ring-black/5 dark:ring-white/10 backdrop-blur">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
