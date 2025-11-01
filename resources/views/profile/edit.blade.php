{{--
    Profile Edit Page - profile/edit.blade.php
    
    Trang quản lý profile của user với tabbed interface.
    
    Layout:
    - Left sidebar: Navigation tabs với sticky positioning
    - Right content: Tab content với Material Design cards
    
    Tabs:
    - Account: Update profile information (name, email)
    - Security: Change password, Google OAuth link/unlink
    - Notifications: Email notification preferences
    - Preferences: Locale (language) settings
    
    Alpine.js:
    - x-data: Tab state management
    - x-show: Show/hide tab content với transitions
    - x-cloak: Hide until Alpine.js loads
    
    Forms:
    - Update Profile: ProfileUpdateRequest validation
    - Update Password: Password confirmation required
    - Update Notifications: Boolean checkboxes
    - Update Locale: Select language preference
    
    @author QuickPoll Team
--}}
<x-app-layout>
    <x-slot name="header">
        <div class="container-material">
            <div class="flex-between">
                <div>
                    <h1 class="text-headline-large text-[color:var(--on-surface)]">
                        {{ __('app.profile') }}
                    </h1>
                    <p class="text-body-medium text-[color:var(--on-surface-variant)] mt-1">
                        {{ __('app.profile_welcome') }}
                    </p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="section-padding-sm" x-data="{ tab: 'account' }">
        <div class="container-material">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                {{-- Left Navigation Rail: Sticky sidebar với user info và tabs --}}
                <aside class="lg:col-span-3 lg:sticky lg:top-6" style="align-self: start;">
                    <div class="card">
                        {{-- User Avatar và Info --}}
                        <div class="p-4 flex items-center gap-4 border-b border-[color:var(--outline)]">
                            <div class="w-14 h-14 rounded-full bg-primary-500 text-white flex items-center justify-center text-lg font-semibold">
                                {{ strtoupper(substr(Auth::user()->name,0,1)) }}
                            </div>
                            <div class="min-w-0">
                                <div class="font-semibold text-[color:var(--on-surface)] truncate">{{ Auth::user()->name }}</div>
                                <div class="text-sm text-[color:var(--on-surface-variant)] truncate">{{ Auth::user()->email }}</div>
                            </div>
                        </div>
                        {{-- Tab Navigation --}}
                        <nav class="p-2">
                            {{-- Account Details Tab --}}
                            <button @click="tab='account'"
                                    :class="tab==='account' ? 'bg-[var(--primary)] text-white' : 'text-[color:var(--on-surface-variant)] hover:bg-[var(--surface-variant)]'"
                                    class="w-full text-left px-3 py-2 rounded-md font-medium flex items-center gap-2">
                                <i class="fa-solid fa-id-card"></i>
                                {{ __('app.account_details') }}
                            </button>
                            {{-- Security Tab --}}
                            <button @click="tab='security'"
                                    :class="tab==='security' ? 'bg-[var(--primary)] text-white' : 'text-[color:var(--on-surface-variant)] hover:bg-[var(--surface-variant)]'"
                                    class="w-full text-left px-3 py-2 rounded-md font-medium flex items-center gap-2">
                                <i class="fa-solid fa-shield-halved"></i>
                                {{ __('app.security') }}
                            </button>
                            {{-- Notifications Tab --}}
                            <button @click="tab='notifications'"
                                    :class="tab==='notifications' ? 'bg-[var(--primary)] text-white' : 'text-[color:var(--on-surface-variant)] hover:bg-[var(--surface-variant)]'"
                                    class="w-full text-left px-3 py-2 rounded-md font-medium flex items-center gap-2">
                                <i class="fa-solid fa-bell"></i>
                                {{ __('app.notifications') }}
                            </button>
                            {{-- Preferences Tab --}}
                            <button @click="tab='preferences'"
                                    :class="tab==='preferences' ? 'bg-[var(--primary)] text-white' : 'text-[color:var(--on-surface-variant)] hover:bg-[var(--surface-variant)]'"
                                    class="w-full text-left px-3 py-2 rounded-md font-medium flex items-center gap-2">
                                <i class="fa-solid fa-gear"></i>
                                {{ __('app.settings') }}
                            </button>
                        </nav>
                    </div>
                </aside>

                {{-- Right Content: Tab content panels --}}
                <section class="lg:col-span-9">
                    {{-- Account Details Tab Content --}}
                    <div x-show="tab==='account'" x-transition.opacity.duration.200ms class="card card-elevated" x-cloak>
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fa-solid fa-user text-primary-500"></i>
                                {{ __('app.account_details') }}
                        </div>
                        <div class="card-subtitle">
                            {{ __('app.profile_update_info') }}
                        </div>
                    </div>
                        <div class="card-content space-y-6">
                            <!-- Avatar + Change Photo -->
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 rounded-full bg-[var(--surface-variant)] border border-[color:var(--outline)] flex items-center justify-center text-lg font-semibold text-[color:var(--on-surface)]">
                                    {{ strtoupper(substr(Auth::user()->name,0,1)) }}
                                </div>
                                <button type="button" class="text-button">
                                    <i class="fa-solid fa-camera"></i>
                                    {{ __('app.change_photo') }}
                                </button>
                            </div>
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                    <!-- Security -->
                    <div x-show="tab==='security'" x-transition.opacity.duration.200ms class="card card-elevated" x-cloak>
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fa-solid fa-lock text-primary-500"></i>
                                {{ __('app.security_signin') }}
                            </div>
                            <div class="card-subtitle">
                                {{ __('app.security_signin_desc') }}
                            </div>
                        </div>
                        <div class="card-content space-y-6">
                            @include('profile.partials.update-password-form')

                            <!-- 2FA switch (UI only) -->
                            <div class="material-switch">
                                <input id="twoFactor" type="checkbox" class="switch-input">
                                <label for="twoFactor" class="switch-label">
                                    <span class="switch-slider"></span>
                                </label>
                                <span class="ml-3 text-sm text-[color:var(--on-surface)]">{{ __('app.enable_2fa') }}</span>
                            </div>

                            <!-- Google Account Link/Unlink -->
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-[color:var(--on-surface)]">{{ __('app.google_account') }}</div>
                                    <div class="text-sm text-[color:var(--on-surface-variant)]">{{ __('app.google_account_quick_signin') }}</div>
                                </div>
                                @if (Auth::user()->google_id)
                                    <form method="POST" action="{{ route('oauth.google.unlink') }}">
                                        @csrf
                                        <button type="submit" class="btn btn-neutral">{{ __('app.unlink') }}</button>
                                    </form>
                                @else
                                    <a href="{{ route('oauth.google.redirect') }}" class="btn btn-neutral">{{ __('app.link_google') }}</a>
                                @endif
                            </div>

                            <!-- Recent sign-in activity (static sample) -->
                            <div class="material-progress-item">
                                <div class="progress-header">
                                    <div class="progress-label">{{ __('app.recent_activity') }}</div>
                                </div>
                                <ul class="space-y-2 text-sm text-[color:var(--on-surface-variant)]">
                                    <li>Chrome · Windows · {{ __('app.today') }} 10:32</li>
                                    <li>Safari · iOS · {{ __('app.yesterday') }} 21:14</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Notifications -->
                    <div x-show="tab==='notifications'" x-transition.opacity.duration.200ms class="card card-elevated" x-cloak>
                        <div class="card-header">
                            <div class="card-title">
                                <i class="fa-solid fa-bell text-primary-500"></i>
                                {{ __('app.notifications') }}
                            </div>
                            <div class="card-subtitle">
                                {{ __('app.notifications_desc') }}
                            </div>
                        </div>
                        <form method="POST" action="{{ route('profile.notifications.update') }}" class="card-content space-y-5">
                            @csrf
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-[color:var(--on-surface)]">{{ __('app.email_me_on_vote') }}</div>
                                    <div class="text-sm text-[color:var(--on-surface-variant)]">{{ __('app.recommended_to_stay_informed') }}</div>
                                </div>
                                <label class="material-switch">
                                    <input type="checkbox" name="email_on_vote" value="1" class="switch-input" {{ old('email_on_vote', $user->email_on_vote ?? true) ? 'checked' : '' }}>
                                    <span class="switch-label"><span class="switch-slider"></span></span>
                                </label>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-[color:var(--on-surface)]">{{ __('app.notify_before_autoclose') }}</div>
                                    <div class="text-sm text-[color:var(--on-surface-variant)]">{{ __('app.reminder_before_close') }}</div>
                                </div>
                                <label class="material-switch">
                                    <input type="checkbox" name="notify_before_autoclose" value="1" class="switch-input" {{ old('notify_before_autoclose', $user->notify_before_autoclose ?? true) ? 'checked' : '' }}>
                                    <span class="switch-label"><span class="switch-slider"></span></span>
                                </label>
                            </div>

                            <div class="pt-4 border-t border-[color:var(--outline)]">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('messages.save') }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Settings & Preferences -->
                    <div x-show="tab==='preferences'" x-transition.opacity.duration.200ms class="card card-elevated" x-cloak>
                        <div class="card-header">
                            <div class="card-title">
                                <i class="fa-solid fa-gear text-primary-500"></i>
                                {{ __('app.settings_preferences') }}
                            </div>
                            <div class="card-subtitle">
                                {{ __('app.personalize_experience') }}
                            </div>
                        </div>
                        <div class="card-content space-y-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-[color:var(--on-surface)]">{{ __('app.language') }}</div>
                                    <div class="text-sm text-[color:var(--on-surface-variant)]">{{ __('app.choose_display_language') }}</div>
                                </div>
                                <div class="relative">
                                    <button type="button" id="langPrefBtn" class="footer-lang-button flex items-center gap-2 px-3 py-2 rounded hover:bg-[var(--surface-variant)]">
                                        <span class="material-symbols-rounded">language</span>
                                        <span id="langPrefDisplay">{{ strtoupper(app()->getLocale()) == 'EN' ? __('app.english') : __('app.vietnamese') }}</span>
                                        <i class="fa-solid fa-caret-down"></i>
                                    </button>
                                    <div id="langPrefDropdown" class="absolute right-0 z-30 bg-[var(--surface)] text-[color:var(--on-surface)] rounded shadow min-w-[140px] border border-[color:var(--outline)] mt-2 hidden">
                                        <button type="button" class="w-full text-left px-4 py-2 hover:bg-[var(--surface-variant)] {{ app()->getLocale() == 'en' ? 'font-bold text-primary-600' : '' }} lang-option" data-lang="en">🇺🇸 {{ __('app.english') }}</button>
                                        <button type="button" class="w-full text-left px-4 py-2 hover:bg-[var(--surface-variant)] {{ app()->getLocale() == 'vi' ? 'font-bold text-primary-600' : '' }} lang-option" data-lang="vi">🇻🇳 {{ __('app.vietnamese') }}</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Form ẩn để lưu ngôn ngữ -->
                            <form id="langSwitchForm" action="{{ route('profile.locale') }}" method="POST" style="display:none;">
                                @csrf
                                <input type="hidden" name="lang" id="langInput">
                            </form>

                            <script>
                            document.addEventListener('DOMContentLoaded', function(){
                                const btn = document.getElementById('langPrefBtn');
                                const dropdown = document.getElementById('langPrefDropdown');
                                const form = document.getElementById('langSwitchForm');
                                const langInput = document.getElementById('langInput');
                                const options = dropdown ? dropdown.querySelectorAll('.lang-option') : [];
                                
                                if (btn && dropdown) {
                                    btn.addEventListener('click', function(e) {
                                        e.stopPropagation();
                                        dropdown.classList.toggle('hidden');
                                    });
                                    
                                    document.addEventListener('click', function(e) {
                                        if (!btn.contains(e.target) && !dropdown.contains(e.target)) {
                                            dropdown.classList.add('hidden');
                                        }
                                    });
                                    
                                    options.forEach(option => {
                                        option.addEventListener('click', function(e) {
                                            e.preventDefault();
                                            const lang = this.getAttribute('data-lang');
                                            langInput.value = lang;
                                            form.submit();
                                        });
                                    });
                                }
                            });
                            </script>


                            
                    </div>
                </div>

                    <!-- Danger Zone -->
                    <div class="card card-elevated mt-6">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fa-solid fa-trash text-error-500"></i>
                            {{ __('app.delete_account') }}
                        </div>
                        <div class="card-subtitle">
                            {{ __('app.delete_account_desc') }}
                        </div>
                    </div>
                    <div class="card-content">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
