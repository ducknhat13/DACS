{{--
    Main Application Layout - app.blade.php
    
    Đây là layout chính của ứng dụng, được sử dụng cho tất cả các trang.
    Layout này sử dụng Laravel Blade component system với x-app-layout.
    
    Cấu trúc:
    - <head>: Meta tags, fonts, CSS/JS assets
    - <body>: Navigation, header slot, main content slot, footer, toast notifications
    
    Features:
    - Multi-language support: lang attribute từ app locale
    - CSRF protection: CSRF token trong meta tag
    - SEO: Open Graph meta tags cho social media sharing
    - Dark mode: Hỗ trợ dark mode qua Tailwind classes
    - Material Design 3: Sử dụng Material Design color palette và components
    
    Slots:
    - $header: Optional header content (page title, description)
    - $slot: Main page content (injected từ child views)
    
    Components:
    - layouts.navigation: Top navigation bar
    - layouts.footer: Footer component
    - components.toast: Toast notification system
    
    @author QuickPoll Team
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        {{-- Basic Meta Tags --}}
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        {{-- CSRF Token cho AJAX requests --}}
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- Page Title và Favicon --}}
        <title>{{ config('app.name', 'QuickPoll') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('Logo.png') }}">
        
        {{-- Open Graph Meta Tags cho Social Media Sharing --}}
        <meta property="og:title" content="{{ $ogTitle ?? config('app.name', 'QuickPoll') }}" />
        <meta property="og:description" content="{{ $ogDescription ?? __('messages.create_poll_subtext') }}" />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="{{ url()->current() }}" />
        @isset($ogImage)
            <meta property="og:image" content="{{ $ogImage }}" />
        @endisset
        
        {{-- Stack để các view con có thể inject thêm content vào head --}}
        @stack('head')

        {{-- Fonts --}}
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        {{-- Assets: Vite bundles CSS và JS --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        {{-- FontAwesome Icons --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
        {{-- Material Symbols (Google Icons) --}}
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0" />
    </head>
    <body class="font-sans antialiased bg-surface-100 dark:bg-surface-900 text-surface-900 dark:text-surface-100">
        <div class="min-h-screen bg-surface-100 dark:bg-surface-900 flex flex-col">
            {{-- Top Navigation Bar --}}
            @include('layouts.navigation')

            {{-- Optional Page Header (title, description) --}}
            @isset($header)
                <header>
                    <div class="max-w-7xl mx-auto mt-10 mb-10 px-4 sm:px-6 lg:px-8 text-center">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            {{-- Main Page Content --}}
            <main class="flex-1">
                {{ $slot }}
            </main>
            
            {{-- Footer --}}
            @include('layouts.footer')
            
            {{-- Toast Notification Component --}}
            <x-toast />
        </div>
    </body>
</html>
