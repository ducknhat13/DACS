<nav x-data="{ open: false, quickAccessOpen: false, quickAccessCode: '' }" class="material-header">
    <!-- Primary Navigation Menu -->
    <div class="header-container">
        <div class="header-content">
            <!-- Left Section: Logo + Navigation -->
            <div class="header-left">
                <!-- Logo -->
                <div class="logo-container">
                    <a href="{{ route('home') }}" class="logo-link">
                        <x-application-logo class="logo" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="navigation-links">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <i class="fa-solid fa-chart-pie"></i>
                        {{ __('app.dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('polls.create')" :active="request()->routeIs('polls.create')">
                        <i class="fa-solid fa-plus-circle"></i>
                        {{ __('messages.create_poll') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Center Section: Quick Access -->
            <div class="header-center">
                <div class="quick-access-container">
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

            <!-- Right Section: Dark Mode Toggle + Profile Dropdown -->
            <div class="header-right">
                <!-- Dark Mode Toggle -->
                <x-dark-mode-toggle class="mr-4" />
                
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
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
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('app.profile') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
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
                    <a href="{{ route('login') }}" class="text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">{{ __('app.log_in') }}</a>
                    <a href="{{ route('register') }}" class="ms-4 text-sm text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">{{ __('app.register') }}</a>
                @endauth
            </div>

            <!-- Mobile Menu Button -->
            <div class="mobile-menu-button">
                <button @click="open = !open" type="button" class="mobile-menu-trigger" aria-label="Toggle mobile menu">
                    <i class="fa-solid fa-bars" x-show="!open" x-cloak></i>
                    <i class="fa-solid fa-times" x-show="open" x-cloak></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
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
// Quick Access functionality
document.addEventListener('DOMContentLoaded', function() {
    // Handle Quick Access form submission
        function handleQuickAccess(event) {
        event.preventDefault();
        const code = event.target.querySelector('input[name="code"]').value.trim();
        if (code) {
            window.location.href = `/quick-access/${code}`;
        }
    }
    
    // Attach event listeners to Quick Access forms
    document.querySelectorAll('.quick-access-form, .mobile-quick-access-form').forEach(form => {
        form.addEventListener('submit', handleQuickAccess);
    });
});
</script>
