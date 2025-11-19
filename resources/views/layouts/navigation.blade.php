{{--
    Partial: layouts/navigation
    - Thanh điều hướng chính: logo, menu, user dropdown, dark-mode toggle.
    - Frontend: đảm bảo responsive (mobile menu), highlight link đang active.
--}}

{{-- Nút hamburger mở/đóng menu mobile, cần JS toggle class/ẩn hiện --}}
{{--
    Navigation Layout - navigation.blade.php
    
    Component này render top navigation bar của ứng dụng với Material Design 3 styling.
    
    Features:
    - Logo và navigation links (Dashboard, Create Poll)
    - Quick Access form: Truy cập poll nhanh bằng slug/code
    - User dropdown: Profile, Logout (nếu đã login)
    - Dark mode toggle
    - Mobile responsive: Hamburger menu cho mobile devices
    
    Alpine.js:
    - x-data: State management cho mobile menu và quick access
    - x-show: Toggle mobile menu visibility
    - x-cloak: Hide elements until Alpine.js loads
    
    Quick Access:
    - Input field để nhập poll code/slug
    - Form submit redirect đến /quick-access/{code}
    - JavaScript handler: handleQuickAccess()
    
    @author QuickPoll Team
--}}
<nav x-data="{ open: false, quickAccessOpen: false, quickAccessCode: '' }" class="material-header">
    {{-- Primary Navigation Menu --}}
    <div class="header-container">
        <div class="header-content">
            {{-- Left Section: Logo + Navigation Links --}}
            <div class="header-left">
                {{-- Logo: Link về home page --}}
                <div class="logo-container">
                    <a href="{{ route('home') }}" class="logo-link">
                        <x-application-logo class="logo" />
                    </a>
                </div>

                {{-- Desktop Navigation Links --}}
                <div class="navigation-links">
                    {{-- Dashboard Link --}}
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <i class="fa-solid fa-chart-pie"></i>
                        {{ __('app.dashboard') }}
                    </x-nav-link>
                    {{-- Create Poll Link --}}
                    <x-nav-link :href="route('polls.create')" :active="request()->routeIs('polls.create')">
                        <i class="fa-solid fa-plus-circle"></i>
                        {{ __('messages.create_poll') }}
                    </x-nav-link>
                </div>
            </div>

            {{-- Center Section: Quick Access Form --}}
            <div class="header-center">
                <div class="quick-access-container">
                    {{-- Quick Access: Nhập poll code/slug để truy cập nhanh --}}
                    <form action="#" method="GET" class="quick-access-form">
                        <div class="quick-access-field">
                            <input 
                                type="text" 
                                name="code" 
                                placeholder="{{ __('messages.enter_code') }}" 
                                class="quick-access-input"
                                x-model="quickAccessCode"
                                @focus="quickAccessOpen = true"
                                @blur="setTimeout(() => quickAccessOpen = false, 200)"
                            >
                            <button type="submit" class="quick-access-button">
                                <i class="fa-solid fa-arrow-right"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Right Section: Dark Mode Toggle + User Menu --}}
            <div class="header-right">
                {{-- Dark Mode Toggle Component --}}
                <x-dark-mode-toggle class="mr-4" />
                
                {{-- User Menu (nếu đã login) --}}
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            {{-- Profile Dropdown Trigger Button --}}
                            <button class="profile-dropdown-trigger">
                                <div class="profile-avatar">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                                <div class="profile-info">
                                    <div class="profile-name">{{ Auth::user()->name }}</div>
                                </div>
                                <div class="profile-chevron">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            {{-- Profile Link --}}
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('app.profile') }}
                            </x-dropdown-link>

                            {{-- Logout Form --}}
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('app.logout') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    {{-- Login/Register Links (nếu chưa login) --}}
                    <a href="{{ route('login') }}" class="text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">{{ __('app.log_in') }}</a>
                    <a href="{{ route('register') }}" class="ms-4 text-sm text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">{{ __('app.register') }}</a>
                @endauth
            </div>

            {{-- Mobile Menu Toggle Button --}}
            <div class="mobile-menu-button">
                <button @click="open = !open" type="button" class="mobile-menu-trigger" aria-label="Toggle mobile menu">
                    <i class="fa-solid fa-bars" x-show="!open" x-cloak></i>
                    <i class="fa-solid fa-times" x-show="open" x-cloak></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Navigation Menu (ẩn/hiện khi click hamburger) --}}
    <div x-show="open" x-cloak x-transition class="mobile-navigation">
        <div class="mobile-navigation-content">
            <!-- Mobile Quick Access -->
            <div class="mobile-quick-access">
                <form action="#" method="GET" class="mobile-quick-access-form">
                    <div class="mobile-quick-access-field">
                        <input 
                            type="text" 
                            name="code" 
                            placeholder="{{ __('messages.enter_code') }}" 
                            class="mobile-quick-access-input"
                        >
                        <button type="submit" class="mobile-quick-access-button">
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Mobile Navigation Links -->
            <div class="mobile-navigation-links">
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    <i class="fa-solid fa-chart-pie"></i>
                    {{ __('app.dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('polls.create')" :active="request()->routeIs('polls.create')">
                    <i class="fa-solid fa-plus-circle"></i>
                    {{ __('messages.create_poll') }}
                </x-responsive-nav-link>
            </div>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            @auth
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('app.profile') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('app.logout') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                <div class="px-4">
                    <a href="{{ route('login') }}" class="text-sm text-gray-600 dark:text-gray-300">{{ __('app.log_in') }}</a>
                    <a href="{{ route('register') }}" class="ms-4 text-sm text-indigo-600 dark:text-indigo-400">{{ __('app.register') }}</a>
                </div>
            @endauth
        </div>
    </div>
</nav>


<script>
/**
 * Quick Access Functionality
 * 
 * Xử lý form Quick Access để truy cập poll nhanh bằng code/slug:
 * - Lắng nghe submit event từ Quick Access forms (desktop và mobile)
 * - Lấy code từ input field
 * - Redirect đến /quick-access/{code}
 * 
 * Forms:
 * - .quick-access-form: Desktop quick access
 * - .mobile-quick-access-form: Mobile quick access
 */
document.addEventListener('DOMContentLoaded', function() {
    /**
     * Handle Quick Access form submission
     * 
     * @param {Event} event - Form submit event
     */
    function handleQuickAccess(event) {
        event.preventDefault();
        const code = event.target.querySelector('input[name="code"]').value.trim();
        if (code) {
            // Redirect đến quick access route
            window.location.href = `/quick-access/${code}`;
        }
    }
    
    // Attach event listeners to all Quick Access forms
    document.querySelectorAll('.quick-access-form, .mobile-quick-access-form').forEach(form => {
        form.addEventListener('submit', handleQuickAccess);
    });
});
</script>
