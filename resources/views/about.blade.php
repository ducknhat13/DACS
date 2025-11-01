{{--
    About Us Page - about.blade.php
    
    Trang giới thiệu về QuickPoll với Material Design 3 design.
    
    Sections:
    - Hero: Title và subtitle
    - Our Mission: Mission statement với statistics
    - Our Values: 4 value cards (Simplicity, Privacy, Innovation, Community)
    - Technology Stack: Logos của technologies sử dụng
    - Meet the Team: Team members với contribution details
    
    Features:
    - Fully responsive: Mobile-first design
    - Team member modals: Click để xem chi tiết contribution
    - Technology logos: Laravel, PHP, JavaScript, Tailwind CSS
    - Localization: Tất cả text sử dụng __('messages.key')
    
    JavaScript:
    - Team member modals: Open/close với Material Design 3 animations
    - Smooth scroll: Navigation giữa sections
    
    @author QuickPoll Team
--}}
<x-app-layout>
    <x-slot name="header">
        <div class="hidden"></div>
    </x-slot>

    {{-- Hero Section: Title và subtitle --}}
    <section class="relative py-12 sm:py-16 md:py-20 lg:py-28 page-transition" style="background: linear-gradient(135deg, rgba(23,107,239,0.1) 0%, rgba(23,107,239,0.05) 100%);">
        <div class="container-material px-4">
            <div class="max-w-3xl mx-auto text-center">
                <h1 class="text-2xl sm:text-3xl md:text-display-medium lg:text-display-large font-semibold mb-4">{{ __('messages.about_title') }}</h1>
                <p class="text-base sm:text-lg md:text-title-large text-[color:var(--on-surface-variant)]">{{ __('messages.about_subtitle') }}</p>
            </div>
        </div>
    </section>

    <!-- OUR MISSION -->
    <section class="section-padding-sm">
        <div class="container-material px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8 lg:gap-12 items-center">
                <div>
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary-100 dark:bg-primary-900/30 mb-6">
                        <i class="fa-solid fa-bullseye text-primary-600 dark:text-primary-400"></i>
                        <span class="text-label-medium text-primary-600 dark:text-primary-400 font-semibold">{{ __('messages.our_mission') }}</span>
                    </div>
                    <h2 class="text-headline-large font-semibold mb-4">{{ __('messages.mission_title') }}</h2>
                    <p class="text-body-large text-[color:var(--on-surface-variant)] mb-4">{{ __('messages.mission_desc_1') }}</p>
                    <p class="text-body-large text-[color:var(--on-surface-variant)]">{{ __('messages.mission_desc_2') }}</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="card text-center p-4 sm:p-6">
                        <div class="w-12 h-12 sm:w-16 sm:h-16 mx-auto mb-3 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                            <i class="fa-solid fa-users text-primary-600 dark:text-primary-400 text-xl sm:text-2xl"></i>
                        </div>
                        <div class="text-xl sm:text-2xl md:text-display-small font-semibold text-primary-600">1.5M+</div>
                        <div class="text-xs sm:text-body-small text-[color:var(--on-surface-variant)] mt-1">{{ __('messages.users') }}</div>
                    </div>
                    <div class="card text-center p-4 sm:p-6">
                        <div class="w-12 h-12 sm:w-16 sm:h-16 mx-auto mb-3 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                            <i class="fa-solid fa-poll text-primary-600 dark:text-primary-400 text-xl sm:text-2xl"></i>
                        </div>
                        <div class="text-xl sm:text-2xl md:text-display-small font-semibold text-primary-600">11M+</div>
                        <div class="text-xs sm:text-body-small text-[color:var(--on-surface-variant)] mt-1">{{ __('messages.polls') }}</div>
                    </div>
                    <div class="card text-center p-4 sm:p-6">
                        <div class="w-12 h-12 sm:w-16 sm:h-16 mx-auto mb-3 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                            <i class="fa-solid fa-check-to-slot text-primary-600 dark:text-primary-400 text-xl sm:text-2xl"></i>
                        </div>
                        <div class="text-xl sm:text-2xl md:text-display-small font-semibold text-primary-600">260M+</div>
                        <div class="text-xs sm:text-body-small text-[color:var(--on-surface-variant)] mt-1">{{ __('messages.votes') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- OUR VALUES -->
    <section class="section-padding-sm" style="background-color: var(--surface-variant);">
        <div class="container-material px-4">
            <div class="text-center mb-8 sm:mb-12">
                <div class="inline-flex items-center gap-2 px-3 sm:px-4 py-2 rounded-full bg-primary-100 dark:bg-primary-900/30 mb-4">
                    <i class="fa-solid fa-heart text-primary-600 dark:text-primary-400"></i>
                    <span class="text-xs sm:text-label-medium text-primary-600 dark:text-primary-400 font-semibold">{{ __('messages.our_values') }}</span>
                </div>
                <h2 class="text-xl sm:text-2xl md:text-headline-large font-semibold mb-3">{{ __('messages.what_we_stand_for') }}</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <!-- Simplicity -->
                <div class="card p-6 text-center hover:shadow-material-lg transition-all duration-300">
                    <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-success-100 dark:bg-success-900/30 flex items-center justify-center">
                        <i class="fa-solid fa-bolt text-success-600 dark:text-success-400 text-3xl"></i>
                    </div>
                    <h3 class="text-title-large font-semibold mb-3">{{ __('messages.value_simplicity_title') }}</h3>
                    <p class="text-body-medium text-[color:var(--on-surface-variant)]">{{ __('messages.value_simplicity_desc') }}</p>
                </div>
                <!-- Privacy -->
                <div class="card p-6 text-center hover:shadow-material-lg transition-all duration-300">
                    <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                        <i class="fa-solid fa-shield-halved text-primary-600 dark:text-primary-400 text-3xl"></i>
                    </div>
                    <h3 class="text-title-large font-semibold mb-3">{{ __('messages.value_privacy_title') }}</h3>
                    <p class="text-body-medium text-[color:var(--on-surface-variant)]">{{ __('messages.value_privacy_desc') }}</p>
                </div>
                <!-- Innovation -->
                <div class="card p-6 text-center hover:shadow-material-lg transition-all duration-300">
                    <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-warning-100 dark:bg-warning-900/30 flex items-center justify-center">
                        <i class="fa-solid fa-lightbulb text-warning-600 dark:text-warning-400 text-3xl"></i>
                    </div>
                    <h3 class="text-title-large font-semibold mb-3">{{ __('messages.value_innovation_title') }}</h3>
                    <p class="text-body-medium text-[color:var(--on-surface-variant)]">{{ __('messages.value_innovation_desc') }}</p>
                </div>
                <!-- Community -->
                <div class="card p-6 text-center hover:shadow-material-lg transition-all duration-300">
                    <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-error-100 dark:bg-error-900/30 flex items-center justify-center">
                        <i class="fa-solid fa-people-group text-error-600 dark:text-error-400 text-3xl"></i>
                    </div>
                    <h3 class="text-title-large font-semibold mb-3">{{ __('messages.value_community_title') }}</h3>
                    <p class="text-body-medium text-[color:var(--on-surface-variant)]">{{ __('messages.value_community_desc') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- HOW IT WORKS -->
    <section class="section-padding-sm">
        <div class="container-material">
            <div class="text-center mb-12">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary-100 dark:bg-primary-900/30 mb-4">
                    <i class="fa-solid fa-gears text-primary-600 dark:text-primary-400"></i>
                    <span class="text-label-medium text-primary-600 dark:text-primary-400 font-semibold">{{ __('messages.how_it_works') }}</span>
                </div>
                <h2 class="text-headline-large font-semibold">{{ __('messages.how_it_works') }}</h2>
            </div>
            <div class="max-w-4xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Step 1: Create -->
                    <div class="card p-6 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-primary-600 text-white flex items-center justify-center text-title-large font-bold">1</div>
                        <h3 class="text-title-medium font-semibold mb-2">{{ __('messages.step_create_title') }}</h3>
                        <p class="text-body-small text-[color:var(--on-surface-variant)]">{{ __('messages.step_create_desc') }}</p>
                    </div>
                    <!-- Step 2: Share -->
                    <div class="card p-6 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-primary-600 text-white flex items-center justify-center text-title-large font-bold">2</div>
                        <h3 class="text-title-medium font-semibold mb-2">{{ __('messages.step_share_title') }}</h3>
                        <p class="text-body-small text-[color:var(--on-surface-variant)]">{{ __('messages.step_share_desc') }}</p>
                    </div>
                    <!-- Step 3: Analyze -->
                    <div class="card p-6 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-primary-600 text-white flex items-center justify-center text-title-large font-bold">3</div>
                        <h3 class="text-title-medium font-semibold mb-2">{{ __('messages.step_analyze_title') }}</h3>
                        <p class="text-body-small text-[color:var(--on-surface-variant)]">{{ __('messages.step_analyze_desc') }}</p>
                    </div>
                    <!-- Step 4: Decide -->
                    <div class="card p-6 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-primary-600 text-white flex items-center justify-center text-title-large font-bold">4</div>
                        <h3 class="text-title-medium font-semibold mb-2">{{ __('messages.step_decide_title') }}</h3>
                        <p class="text-body-small text-[color:var(--on-surface-variant)]">{{ __('messages.step_decide_desc') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MEET THE TEAM -->
    <section class="section-padding-sm" style="background-color: var(--surface-variant);">
        <div class="container-material">
            <div class="text-center mb-12">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary-100 dark:bg-primary-900/30 mb-4">
                    <i class="fa-solid fa-user-group text-primary-600 dark:text-primary-400"></i>
                    <span class="text-label-medium text-primary-600 dark:text-primary-400 font-semibold">{{ __('messages.meet_the_team') }}</span>
                </div>
                <h2 class="text-headline-large font-semibold">{{ __('messages.meet_the_team') }}</h2>
            </div>
            <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
                <!-- Team Member 1: Nguyễn Đức Nhật -->
                <div class="card p-4 sm:p-6 text-center cursor-pointer hover:shadow-material-lg transition-all duration-300 member-card" data-member="nhat">
                    <div class="w-24 h-24 sm:w-32 sm:h-32 mx-auto mb-4 rounded-full bg-gradient-to-br from-primary-100 to-primary-200 dark:from-primary-900/30 dark:to-primary-800/30 flex items-center justify-center">
                        <i class="fa-solid fa-user text-3xl sm:text-5xl text-primary-600 dark:text-primary-400"></i>
                    </div>
                    <h3 class="text-lg sm:text-xl md:text-title-large font-semibold mb-2">{{ __('messages.member_nhat_name') }}</h3>
                    <p class="text-sm sm:text-body-medium text-primary-600 mb-3">{{ __('messages.member_nhat_role') }}</p>
                    <div class="flex items-center justify-center gap-2 text-xs sm:text-body-small text-[color:var(--on-surface-variant)]">
                        <i class="fa-solid fa-chart-pie text-primary-600"></i>
                        <span>{{ __('messages.contribution') }}: 60%</span>
                    </div>
                    <div class="mt-3 text-xs sm:text-body-small text-primary-600">
                        <i class="fa-solid fa-arrow-right mr-1"></i>
                        {{ __('messages.click_to_view_details') }}
                    </div>
                </div>
                <!-- Team Member 2: Đoàn Trung Đức -->
                <div class="card p-4 sm:p-6 text-center cursor-pointer hover:shadow-material-lg transition-all duration-300 member-card" data-member="duc">
                    <div class="w-24 h-24 sm:w-32 sm:h-32 mx-auto mb-4 rounded-full bg-gradient-to-br from-success-100 to-success-200 dark:from-success-900/30 dark:to-success-800/30 flex items-center justify-center">
                        <i class="fa-solid fa-user text-3xl sm:text-5xl text-success-600 dark:text-success-400"></i>
                    </div>
                    <h3 class="text-lg sm:text-xl md:text-title-large font-semibold mb-2">{{ __('messages.member_duc_name') }}</h3>
                    <p class="text-sm sm:text-body-medium text-success-600 mb-3">{{ __('messages.member_duc_role') }}</p>
                    <div class="flex items-center justify-center gap-2 text-xs sm:text-body-small text-[color:var(--on-surface-variant)]">
                        <i class="fa-solid fa-chart-pie text-success-600"></i>
                        <span>{{ __('messages.contribution') }}: 40%</span>
                    </div>
                    <div class="mt-3 text-xs sm:text-body-small text-success-600">
                        <i class="fa-solid fa-arrow-right mr-1"></i>
                        {{ __('messages.click_to_view_details') }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- TECHNOLOGY STACK -->
    <section class="section-padding-sm">
        <div class="container-material">
            <div class="text-center mb-8">
                <h2 class="text-headline-large font-semibold mb-4">{{ __('messages.tech_stack') }}</h2>
                <p class="text-body-medium text-[color:var(--on-surface-variant)]">{{ __('messages.built_with_desc') ?? 'QuickPoll is built with modern, reliable technologies' }}</p>
            </div>
            <div class="max-w-3xl mx-auto flex flex-wrap items-center justify-center gap-8">
                <!-- Laravel -->
                <div class="flex flex-col items-center gap-2">
                    <div class="w-16 h-16 rounded-xl bg-white dark:bg-surface-variant flex items-center justify-center p-2 shadow-sm">
                        <img src="https://laravel.com/img/logomark.min.svg" alt="Laravel" class="w-full h-full object-contain" style="filter: brightness(0) saturate(100%) invert(27%) sepia(51%) saturate(2878%) hue-rotate(346deg) brightness(104%) contrast(97%);">
                    </div>
                    <span class="text-body-small text-[color:var(--on-surface-variant)]">Laravel</span>
                </div>
                <!-- PHP -->
                <div class="flex flex-col items-center gap-2">
                    <div class="w-16 h-16 rounded-xl bg-white dark:bg-surface-variant flex items-center justify-center p-2 shadow-sm">
                        <img src="https://www.php.net/images/logos/new-php-logo.svg" alt="PHP" class="w-full h-full object-contain">
                    </div>
                    <span class="text-body-small text-[color:var(--on-surface-variant)]">PHP</span>
                </div>
                <!-- JavaScript -->
                <div class="flex flex-col items-center gap-2">
                    <div class="w-16 h-16 rounded-xl bg-white dark:bg-surface-variant flex items-center justify-center p-2 shadow-sm">
                        <svg viewBox="0 0 24 24" class="w-full h-full" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="24" height="24" rx="4" fill="#F7DF1E"/>
                            <path d="M9.619 17.281c.188.387.361.714.729.714.372 0 .608-.186.608-.908v-4.948h1.168v4.978c0 1.498-.88 2.185-2.17 2.185-1.163 0-1.838-.61-2.186-1.343l1.01-.578zm5.119.103c.27.47.617.814 1.32.814.554 0 .91-.276.91-.645 0-.356-.287-.491-.769-.701l-.265-.113c-.761-.325-1.266-.734-1.266-1.594 0-.793.604-1.397 1.55-1.397.672 0 1.155.233 1.504.844l-1.098.705c-.145-.257-.302-.358-.406-.358-.172 0-.281.108-.281.265 0 .183.11.293.565.479l.261.112c.895.383 1.398.773 1.398 1.646 0 .951-.747 1.471-1.751 1.471-.987 0-1.623-.466-1.938-1.075l1.123-.684z" fill="#000"/>
                        </svg>
                    </div>
                    <span class="text-body-small text-[color:var(--on-surface-variant)]">JavaScript</span>
                </div>
                <!-- Tailwind CSS -->
                <div class="flex flex-col items-center gap-2">
                    <div class="w-16 h-16 rounded-xl bg-white dark:bg-surface-variant flex items-center justify-center p-2 shadow-sm">
                        <svg viewBox="0 0 24 24" class="w-full h-full" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 6c-2.67 0-4.33 1.33-5 4 1-1.33 2.17-1.83 3.5-1.5.76.19 1.31.74 1.91 1.35.98.98 2.12 2.12 4.59 2.12 2.67 0 4.33-1.33 5-4-1 1.33-2.17 1.83-3.5 1.5-.76-.19-1.31-.74-1.91-1.35C15.61 7.15 14.47 6 12 6zm-5 6c-2.67 0-4.33 1.33-5 4 1-1.33 2.17-1.83 3.5-1.5.76.19 1.31.74 1.91 1.35C8.39 17.15 9.53 18 12 18c2.67 0 4.33-1.33 5-4-1 1.33-2.17 1.83-3.5 1.5-.76-.19-1.31-.74-1.91-1.35C11.61 13.15 10.47 12 7 12z" fill="#06B6D4"/>
                        </svg>
                    </div>
                    <span class="text-body-small text-[color:var(--on-surface-variant)]">Tailwind CSS</span>
                </div>
            </div>
        </div>
    </section>

    <!-- CONTACT CTA -->
    <section class="section-padding-sm">
        <div class="container-material">
            <div class="card flex flex-col md:flex-row items-center justify-between gap-6 p-8">
                <div class="text-center md:text-left">
                    <h3 class="text-headline-medium font-semibold mb-2">{{ __('messages.contact_us') }}</h3>
                    <p class="text-body-medium text-[color:var(--on-surface-variant)]">{{ __('messages.contact_desc') }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="#" class="btn btn-neutral">{{ __('messages.view_documentation') }}</a>
                    <a href="{{ route('contact') }}" class="btn btn-primary">{{ __('messages.contact_button') }}</a>
                </div>
            </div>
        </div>
    </section>

    <!-- MEMBER DETAIL MODALS -->
    
    <!-- Modal: Nguyễn Đức Nhật -->
    <div id="memberModalNhat" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4" style="backdrop-filter: blur(4px);">
        <div class="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="card p-0 relative">
                <!-- Header -->
                <div class="p-4 sm:p-6 pb-4 border-b border-[color:var(--outline)]">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-3 sm:gap-4 min-w-0 flex-1">
                            <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-full bg-gradient-to-br from-primary-100 to-primary-200 dark:from-primary-900/30 dark:to-primary-800/30 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-user text-xl sm:text-3xl text-primary-600 dark:text-primary-400"></i>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-lg sm:text-xl md:text-headline-medium font-semibold truncate">{{ __('messages.member_nhat_name') }}</h3>
                                <p class="text-xs sm:text-sm md:text-body-medium text-primary-600 mt-1 truncate">{{ __('messages.member_nhat_role') }}</p>
                            </div>
                        </div>
                        <button class="member-modal-close icon-button flex-shrink-0" data-modal="nhat" aria-label="Close">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                    <div class="mt-4 flex items-center gap-2">
                        <div class="px-3 py-1 rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 text-label-medium font-semibold">
                            {{ __('messages.contribution') }}: 60%
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
                    <!-- Contributions -->
                    <div>
                        <h4 class="text-lg sm:text-xl md:text-headline-small font-semibold mb-3 sm:mb-4">{{ __('messages.contributions') }}</h4>
                        <div class="space-y-3 sm:space-y-4">
                            <!-- Backend Core -->
                            <div class="card p-3 sm:p-4">
                                <div class="flex items-center justify-between mb-2 gap-2">
                                    <span class="text-sm sm:text-base md:text-title-medium font-medium flex-1">{{ __('messages.contribution_backend') }}</span>
                                    <span class="text-sm sm:text-body-medium text-primary-600 font-semibold flex-shrink-0">25%</span>
                                </div>
                                <div class="w-full h-2 bg-surface-variant rounded-full overflow-hidden">
                                    <div class="bg-primary-600 h-full rounded-full" style="width: 100%;"></div>
                                </div>
                                <ul class="mt-3 space-y-1 text-body-small text-[color:var(--on-surface-variant)] list-disc list-inside">
                                    <li>{{ __('messages.contribution_backend_item1') }}</li>
                                    <li>{{ __('messages.contribution_backend_item2') }}</li>
                                    <li>{{ __('messages.contribution_backend_item3') }}</li>
                                </ul>
                            </div>

                            <!-- Authentication -->
                            <div class="card p-3 sm:p-4">
                                <div class="flex items-center justify-between mb-2 gap-2">
                                    <span class="text-sm sm:text-base md:text-title-medium font-medium flex-1">{{ __('messages.contribution_auth') }}</span>
                                    <span class="text-sm sm:text-body-medium text-primary-600 font-semibold flex-shrink-0">10%</span>
                                </div>
                                <div class="w-full h-2 bg-surface-variant rounded-full overflow-hidden">
                                    <div class="bg-primary-600 h-full rounded-full" style="width: 100%;"></div>
                                </div>
                                <ul class="mt-3 space-y-1 text-body-small text-[color:var(--on-surface-variant)] list-disc list-inside">
                                    <li>{{ __('messages.contribution_auth_item1') }}</li>
                                    <li>{{ __('messages.contribution_auth_item2') }}</li>
                                </ul>
                            </div>

                            <!-- Core Features -->
                            <div class="card p-3 sm:p-4">
                                <div class="flex items-center justify-between mb-2 gap-2">
                                    <span class="text-sm sm:text-base md:text-title-medium font-medium flex-1">{{ __('messages.contribution_core') }}</span>
                                    <span class="text-sm sm:text-body-medium text-primary-600 font-semibold flex-shrink-0">15%</span>
                                </div>
                                <div class="w-full h-2 bg-surface-variant rounded-full overflow-hidden">
                                    <div class="bg-primary-600 h-full rounded-full" style="width: 100%;"></div>
                                </div>
                                <ul class="mt-3 space-y-1 text-body-small text-[color:var(--on-surface-variant)] list-disc list-inside">
                                    <li>{{ __('messages.contribution_core_item1') }}</li>
                                    <li>{{ __('messages.contribution_core_item2') }}</li>
                                    <li>{{ __('messages.contribution_core_item3') }}</li>
                                </ul>
                            </div>

                            <!-- Advanced Features -->
                            <div class="card p-3 sm:p-4">
                                <div class="flex items-center justify-between mb-2 gap-2">
                                    <span class="text-sm sm:text-base md:text-title-medium font-medium flex-1">{{ __('messages.contribution_advanced') }}</span>
                                    <span class="text-sm sm:text-body-medium text-primary-600 font-semibold flex-shrink-0">10%</span>
                                </div>
                                <div class="w-full h-2 bg-surface-variant rounded-full overflow-hidden">
                                    <div class="bg-primary-600 h-full rounded-full" style="width: 100%;"></div>
                                </div>
                                <ul class="mt-3 space-y-1 text-body-small text-[color:var(--on-surface-variant)] list-disc list-inside">
                                    <li>{{ __('messages.contribution_advanced_item1') }}</li>
                                    <li>{{ __('messages.contribution_advanced_item2') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="p-6 pt-4 border-t border-[color:var(--outline)] flex items-center justify-end">
                    <button class="member-modal-close btn btn-neutral" data-modal="nhat">
                        {{ __('messages.close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Đoàn Trung Đức -->
    <div id="memberModalDuc" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4" style="backdrop-filter: blur(4px);">
        <div class="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="card p-0 relative">
                <!-- Header -->
                <div class="p-4 sm:p-6 pb-4 border-b border-[color:var(--outline)]">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-3 sm:gap-4 min-w-0 flex-1">
                            <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-full bg-gradient-to-br from-success-100 to-success-200 dark:from-success-900/30 dark:to-success-800/30 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-user text-xl sm:text-3xl text-success-600 dark:text-success-400"></i>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-lg sm:text-xl md:text-headline-medium font-semibold truncate">{{ __('messages.member_duc_name') }}</h3>
                                <p class="text-xs sm:text-sm md:text-body-medium text-success-600 mt-1 truncate">{{ __('messages.member_duc_role') }}</p>
                            </div>
                        </div>
                        <button class="member-modal-close icon-button flex-shrink-0" data-modal="duc" aria-label="Close">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                    <div class="mt-4 flex items-center gap-2">
                        <div class="px-3 py-1 rounded-full bg-success-100 dark:bg-success-900/30 text-success-600 dark:text-success-400 text-label-medium font-semibold">
                            {{ __('messages.contribution') }}: 40%
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
                    <!-- Contributions -->
                    <div>
                        <h4 class="text-lg sm:text-xl md:text-headline-small font-semibold mb-3 sm:mb-4">{{ __('messages.contributions') }}</h4>
                        <div class="space-y-3 sm:space-y-4">
                            <!-- Frontend/UI -->
                            <div class="card p-3 sm:p-4">
                                <div class="flex items-center justify-between mb-2 gap-2">
                                    <span class="text-sm sm:text-base md:text-title-medium font-medium flex-1">{{ __('messages.contribution_frontend') }}</span>
                                    <span class="text-sm sm:text-body-medium text-success-600 font-semibold flex-shrink-0">20%</span>
                                </div>
                                <div class="w-full h-2 bg-surface-variant rounded-full overflow-hidden">
                                    <div class="bg-success-600 h-full rounded-full" style="width: 100%;"></div>
                                </div>
                                <ul class="mt-3 space-y-1 text-body-small text-[color:var(--on-surface-variant)] list-disc list-inside">
                                    <li>{{ __('messages.contribution_frontend_item1') }}</li>
                                    <li>{{ __('messages.contribution_frontend_item2') }}</li>
                                    <li>{{ __('messages.contribution_frontend_item3') }}</li>
                                </ul>
                            </div>

                            <!-- Static Pages -->
                            <div class="card p-3 sm:p-4">
                                <div class="flex items-center justify-between mb-2 gap-2">
                                    <span class="text-sm sm:text-base md:text-title-medium font-medium flex-1">{{ __('messages.contribution_pages') }}</span>
                                    <span class="text-sm sm:text-body-medium text-success-600 font-semibold flex-shrink-0">10%</span>
                                </div>
                                <div class="w-full h-2 bg-surface-variant rounded-full overflow-hidden">
                                    <div class="bg-success-600 h-full rounded-full" style="width: 100%;"></div>
                                </div>
                                <ul class="mt-3 space-y-1 text-body-small text-[color:var(--on-surface-variant)] list-disc list-inside">
                                    <li>{{ __('messages.contribution_pages_item1') }}</li>
                                    <li>{{ __('messages.contribution_pages_item2') }}</li>
                                    <li>{{ __('messages.contribution_pages_item3') }}</li>
                                </ul>
                            </div>

                            <!-- UI Components -->
                            <div class="card p-3 sm:p-4">
                                <div class="flex items-center justify-between mb-2 gap-2">
                                    <span class="text-sm sm:text-base md:text-title-medium font-medium flex-1">{{ __('messages.contribution_components') }}</span>
                                    <span class="text-sm sm:text-body-medium text-success-600 font-semibold flex-shrink-0">10%</span>
                                </div>
                                <div class="w-full h-2 bg-surface-variant rounded-full overflow-hidden">
                                    <div class="bg-success-600 h-full rounded-full" style="width: 100%;"></div>
                                </div>
                                <ul class="mt-3 space-y-1 text-body-small text-[color:var(--on-surface-variant)] list-disc list-inside">
                                    <li>{{ __('messages.contribution_components_item1') }}</li>
                                    <li>{{ __('messages.contribution_components_item2') }}</li>
                                    <li>{{ __('messages.contribution_components_item3') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="p-6 pt-4 border-t border-[color:var(--outline)] flex items-center justify-end">
                    <button class="member-modal-close btn btn-neutral" data-modal="duc">
                        {{ __('messages.close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const memberCards = document.querySelectorAll('.member-card');
    
    memberCards.forEach(card => {
        card.addEventListener('click', function(){
            const member = this.getAttribute('data-member');
            const modal = document.getElementById(`memberModal${member.charAt(0).toUpperCase() + member.slice(1)}`);
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            }
        });
    });

    const closeButtons = document.querySelectorAll('.member-modal-close');
    closeButtons.forEach(btn => {
        btn.addEventListener('click', function(){
            const modalName = this.getAttribute('data-modal');
            const modal = document.getElementById(`memberModal${modalName.charAt(0).toUpperCase() + modalName.slice(1)}`);
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            }
        });
    });

    // Close on backdrop click
    const modals = document.querySelectorAll('[id^="memberModal"]');
    modals.forEach(modal => {
        modal.addEventListener('click', function(e){
            if (e.target === this) {
                this.classList.add('hidden');
                this.classList.remove('flex');
                document.body.style.overflow = '';
            }
        });
    });
});
</script>
