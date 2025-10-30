<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'QuickPoll') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('Logo.png') }}">
        <meta property="og:title" content="{{ $ogTitle ?? config('app.name', 'QuickPoll') }}" />
        <meta property="og:description" content="{{ $ogDescription ?? 'Tạo và chia sẻ khảo sát nhanh chóng' }}" />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="{{ url()->current() }}" />
        @isset($ogImage)
            <meta property="og:image" content="{{ $ogImage }}" />
        @endisset
        @stack('head')

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0" />
    </head>
    <body class="font-sans antialiased bg-surface-100 dark:bg-surface-900 text-surface-900 dark:text-surface-100">
        <div class="min-h-screen bg-surface-100 dark:bg-surface-900 flex flex-col">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header>
                    <div class="max-w-7xl mx-auto mt-10 mb-10 px-4 sm:px-6 lg:px-8 text-center">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="flex-1">
                {{ $slot }}
            </main>
            
            @include('layouts.footer')
            
            <!-- Toast Notifications -->
            <x-toast />
        </div>
    </body>
</html>
