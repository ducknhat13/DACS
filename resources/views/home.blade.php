<!--
    Home Page (Landing)
    - Mục tiêu: giới thiệu sản phẩm, dẫn hướng nhanh tạo poll và xem thống kê.
    - Bố cục gợi ý các khối (có thể khác chút tùy code thực tế):
      1) Hero: tagline + CTA (tạo poll)
      2) Features: card mô tả tính năng chính (tạo nhanh, chia sẻ, thống kê...)
      3) How it works: các bước sử dụng
      4) Statistics Section: các con số/tổng quan; đã đổi nền = var(--surface)
      5) Testimonials/FAQ (nếu có)
      6) Call-to-Action cuối trang
    - CSS/Theme: dùng token MD3 (surface, surface-variant, on-surface...)
    - Accessibility: chú ý aria-label trên nút, tương phản màu, heading hierarchy.
-->
{{--
    Home Page - home.blade.php
    
    Trang chủ của ứng dụng với Material Design 3 design.
    
    Sections:
    - Hero: Banner với background image và CTA buttons
    - Statistics: Thống kê số liệu (Users, Polls, Votes)
    - Features: 3 feature sections với images
    - Live Demo Modal: Interactive demo poll với fake data
    - CTA: Call-to-action section
    
    Features:
    - Fully responsive: Mobile-first design
    - Live Demo: Modal với fake poll để demo tính năng
    - Localization: Tất cả text sử dụng __('messages.key')
    - Images: Sử dụng asset() để load từ public/resources/image/ (được copy khi build)
    
    JavaScript:
    - Live Demo Modal: Open/close với animations
    - Vote simulation: Fake voting với loading states
    - Results animation: Transition từ vote sang results
    
    @author QuickPoll Team
--}}
<x-app-layout>
    <x-slot name="header">
        <div class="hidden"></div>
    </x-slot>

    {{-- Hero Section: Banner với background image và primary CTA --}}
    <section class="relative page-transition" style="min-height:400px;">
        <img src="{{ asset('resources/image/review.jpg') }}" alt="QuickPoll review" class="absolute inset-0 w-full h-full object-cover" />
        {{-- Blue overlay: rgba(23,107,239,.4) - Primary color với opacity --}}
        <div class="absolute inset-0" style="background: rgba(23,107,239,.4);"></div>
        <div class="relative container-material py-12 sm:py-16 md:py-20 lg:py-28">
            <div class="max-w-2xl text-white px-4">
                <h1 class="text-2xl sm:text-3xl md:text-display-small lg:text-display-medium font-semibold">{{ __('messages.home_hero_title') }}</h1>
                <p class="mt-3 sm:mt-4 text-base sm:text-body-large opacity-95">{{ __('messages.home_hero_subtitle') }}</p>
                <div class="mt-6 sm:mt-8 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    {{-- Create Poll CTA: Redirect đến create page hoặc login --}}
                    <a href="{{ auth()->check() ? route('polls.create') : route('login') }}" class="btn btn-primary text-center">{{ __('messages.create_poll') }}</a>
                    {{-- Live Demo Button: Mở modal demo --}}
                    <button id="openDemo" class="btn btn-neutral text-center">{{ __('messages.live_demo') }}</button>
                </div>
            </div>
        </div>
    </section>

    {{-- Statistics Section: Trust indicators với số liệu --}}
    <section class="py-12 sm:py-16 md:py-20" style="background-color: var(--surface);">
        <div class="container-material">
            <div class="text-center mb-8 sm:mb-12">
                <p class="text-sm sm:text-label-large uppercase tracking-wider text-[color:var(--on-surface-variant)] px-4">{{ __('messages.trusted_by_users') }}</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 sm:gap-8 max-w-4xl mx-auto px-4">
                <div class="text-center">
                    <div class="text-2xl sm:text-3xl md:text-display-small lg:text-display-medium font-semibold" style="color: var(--primary);">1.5M+</div>
                    <div class="text-sm sm:text-body-medium text-[color:var(--on-surface-variant)] mt-2">{{ __('messages.users') }}</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl sm:text-3xl md:text-display-small lg:text-display-medium font-semibold" style="color: var(--primary);">11M+</div>
                    <div class="text-sm sm:text-body-medium text-[color:var(--on-surface-variant)] mt-2">{{ __('messages.polls') }}</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl sm:text-3xl md:text-display-small lg:text-display-medium font-semibold" style="color: var(--primary);">260M+</div>
                    <div class="text-sm sm:text-body-medium text-[color:var(--on-surface-variant)] mt-2">{{ __('messages.votes') }}</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Features Section: 3 feature cards với alternating layout --}}
    <section class="section-padding-sm">
        <div class="container-material space-y-12 sm:space-y-16 lg:space-y-24 px-4">
            {{-- Feature 1: Create Poll --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8 items-center py-6 sm:py-8 md:py-10 border-b border-[color:var(--outline)]">
                <div>
                    <h3 class="text-lg sm:text-xl md:text-title-large font-semibold">{{ __('messages.feature_create_poll_title') }}</h3>
                    <p class="text-sm sm:text-body-medium text-[color:var(--on-surface-variant)] mt-2">{{ __('messages.feature_create_poll_desc') }}</p>
                    <div class="mt-4">
                        <a href="{{ auth()->check() ? route('polls.create') : route('login') }}" class="btn btn-primary btn-sm sm:btn-base">{{ __('messages.explore') }}</a>
                    </div>
                </div>
                <div class="lg:justify-self-end w-full">
                    <div class="relative overflow-hidden rounded-2xl border h-[250px] sm:h-[320px] md:h-[420px]">
                        <img src="{{ asset('resources/image/create_poll.png') }}" alt="Create poll screenshot" class="w-full h-full object-cover" />
                    </div>
                </div>
            </div>

            <!-- Feature 2 -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8 items-center py-6 sm:py-8 md:py-10 border-b border-[color:var(--outline)]">
                <div class="order-2 lg:order-1 w-full">
                    <div class="relative overflow-hidden rounded-2xl border h-[250px] sm:h-[320px] md:h-[420px]">
                        <img src="{{ asset('resources/image/dashboard.png') }}" alt="Dashboard screenshot" class="w-full h-full object-cover" />
                    </div>
                </div>
                <div class="order-1 lg:order-2 lg:text-right">
                    <h3 class="text-lg sm:text-xl md:text-title-large font-semibold">{{ __('messages.feature_stats_title') }}</h3>
                    <p class="text-sm sm:text-body-medium text-[color:var(--on-surface-variant)] mt-2">{{ __('messages.feature_stats_desc') }}</p>
                    <div class="mt-4 lg:flex lg:justify-end">
                        <a href="{{ auth()->check() ? route('stats.index') : route('login') }}" class="btn btn-primary btn-sm sm:btn-base">{{ __('messages.explore') }}</a>
                    </div>
                </div>
            </div>

            <!-- Feature 3 -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8 items-center py-6 sm:py-8 md:py-10">
                <div>
                    <h3 class="text-lg sm:text-xl md:text-title-large font-semibold">{{ __('messages.feature_privacy_title') }}</h3>
                    <p class="text-sm sm:text-body-medium text-[color:var(--on-surface-variant)] mt-2">{{ __('messages.feature_privacy_desc') }}</p>
                    <div class="mt-4">
                        <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="btn btn-primary btn-sm sm:btn-base">{{ __('messages.explore') }}</a>
                    </div>
                </div>
                <div class="lg:justify-self-end w-full">
                    <div class="relative overflow-hidden rounded-2xl border h-[250px] sm:h-[320px] md:h-[420px]">
                        <img src="{{ asset('resources/image/review.jpg') }}" alt="Privacy options" class="w-full h-full object-cover" />
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="section-padding-sm">
        <div class="container-material px-4">
            <div class="card flex flex-col md:flex-row items-center justify-between gap-4 p-6 sm:p-8">
                <div class="text-center md:text-left">
                    <div class="text-lg sm:text-xl md:text-title-large font-semibold">{{ __('messages.cta_ready') }}</div>
                </div>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto">
                    <a href="{{ auth()->check() ? route('polls.create') : route('login') }}" class="btn btn-primary text-center">{{ __('messages.create_poll') }}</a>
                    <a href="{{ route('register') }}" class="btn btn-neutral text-center">{{ __('messages.sign_up') }}</a>
                </div>
            </div>
        </div>
    </section>

    <!-- LIVE DEMO MODAL -->
    <div id="demoModal" class="hidden fixed inset-0 z-[100] items-center justify-center p-2 sm:p-4" style="backdrop-filter: blur(4px);">
        <div class="absolute inset-0 bg-black/60 transition-opacity duration-300"></div>
        <div class="relative card w-full max-w-2xl mx-auto bg-[var(--surface)] shadow-2xl animate-scale-in p-4 sm:p-6" style="max-height: 90vh; overflow-y: auto;">
            <!-- Header -->
            <div class="flex items-center justify-between pb-3 sm:pb-4 border-b border-[color:var(--outline)] mb-4 sm:mb-6 gap-2">
                <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-rocket text-primary-600 dark:text-primary-400 text-sm sm:text-base"></i>
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-base sm:text-lg md:text-title-large font-semibold truncate">{{ __('messages.live_demo') }}</h4>
                        <p class="text-xs sm:text-body-small text-[color:var(--on-surface-variant)] truncate">{{ __('messages.try_it_now') }}</p>
                    </div>
                </div>
                <button id="closeDemo" class="icon-button hover:bg-surface-variant transition-colors flex-shrink-0" aria-label="{{ __('messages.close') }}">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>

            <!-- Vote Section -->
            <div id="demoVote">
                <div class="text-headline-small font-semibold mb-6 text-[color:var(--on-surface)]">
                    {{ __('messages.demo_question') }}
                </div>
                <form class="space-y-3" id="demoForm">
                    <label class="demo-option flex items-center gap-4 p-4 rounded-xl border-2 border-[color:var(--outline)] hover:border-primary-500 transition-all cursor-pointer group">
                        <input type="radio" name="demo" value="easy" class="demo-radio w-5 h-5 text-primary-600">
                        <span class="flex-1 text-body-large font-medium group-hover:text-primary-600 transition-colors">{{ __('messages.demo_option_easy') }}</span>
                        <i class="fa-solid fa-check-circle text-primary-600 opacity-0 group-has-[:checked]:opacity-100 transition-opacity"></i>
                    </label>
                    <label class="demo-option flex items-center gap-4 p-4 rounded-xl border-2 border-[color:var(--outline)] hover:border-primary-500 transition-all cursor-pointer group">
                        <input type="radio" name="demo" value="so-so" class="demo-radio w-5 h-5 text-primary-600">
                        <span class="flex-1 text-body-large font-medium group-hover:text-primary-600 transition-colors">{{ __('messages.demo_option_medium') }}</span>
                        <i class="fa-solid fa-check-circle text-primary-600 opacity-0 group-has-[:checked]:opacity-100 transition-opacity"></i>
                    </label>
                    <label class="demo-option flex items-center gap-4 p-4 rounded-xl border-2 border-[color:var(--outline)] hover:border-primary-500 transition-all cursor-pointer group">
                        <input type="radio" name="demo" value="hard" class="demo-radio w-5 h-5 text-primary-600">
                        <span class="flex-1 text-body-large font-medium group-hover:text-primary-600 transition-colors">{{ __('messages.demo_option_hard') }}</span>
                        <i class="fa-solid fa-check-circle text-primary-600 opacity-0 group-has-[:checked]:opacity-100 transition-opacity"></i>
                    </label>
                    <div class="pt-4">
                        <button id="btnDemoVote" type="button" class="btn btn-primary w-full" disabled>
                            <span class="btn-text">{{ __('messages.submit_vote') }}</span>
                            <span class="btn-loading hidden">
                                <i class="fa-solid fa-spinner fa-spin mr-2"></i>
                                {{ __('messages.submitting') }}
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Results Section -->
            <div id="demoResults" class="hidden">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-full bg-success-100 dark:bg-success-900/30 flex items-center justify-center">
                        <i class="fa-solid fa-chart-pie text-success-600 dark:text-success-400"></i>
                    </div>
                    <h4 class="text-headline-small font-semibold">{{ __('messages.results') }}</h4>
                </div>
                
                <div class="space-y-4 mb-6">
                    <div class="result-item">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-body-large font-medium">
                                {{ __('messages.demo_option_easy') }}
                                <span class="demo-you-badge hidden text-primary-600 font-semibold ml-2" data-option="easy">({{ __('messages.you') }})</span>
                            </span>
                            <span class="text-title-medium font-semibold text-success-600" id="r-easy">0</span>
                        </div>
                        <div class="w-full h-3 bg-surface-variant rounded-full overflow-hidden">
                            <div class="demo-progress bg-success-600 h-full rounded-full transition-all duration-500 ease-out" data-value="easy" style="width: 0%;"></div>
                        </div>
                    </div>
                    <div class="result-item">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-body-large font-medium">
                                {{ __('messages.demo_option_medium') }}
                                <span class="demo-you-badge hidden text-primary-600 font-semibold ml-2" data-option="so-so">({{ __('messages.you') }})</span>
                            </span>
                            <span class="text-title-medium font-semibold text-warning-600" id="r-so">0</span>
                        </div>
                        <div class="w-full h-3 bg-surface-variant rounded-full overflow-hidden">
                            <div class="demo-progress bg-warning-600 h-full rounded-full transition-all duration-500 ease-out" data-value="so-so" style="width: 0%;"></div>
                        </div>
                    </div>
                    <div class="result-item">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-body-large font-medium">
                                {{ __('messages.demo_option_hard') }}
                                <span class="demo-you-badge hidden text-primary-600 font-semibold ml-2" data-option="hard">({{ __('messages.you') }})</span>
                            </span>
                            <span class="text-title-medium font-semibold text-error-600" id="r-hard">0</span>
                        </div>
                        <div class="w-full h-3 bg-surface-variant rounded-full overflow-hidden">
                            <div class="demo-progress bg-error-600 h-full rounded-full transition-all duration-500 ease-out" data-value="hard" style="width: 0%;"></div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-[color:var(--outline)] flex items-center justify-between">
                    <div class="flex items-center gap-2 text-[color:var(--on-surface-variant)]">
                        <i class="fa-solid fa-heart text-error-500"></i>
                        <span class="text-body-medium">{{ __('messages.thank_you') }}</span>
                    </div>
                    <button id="btnCloseDemo2" class="btn btn-neutral">
                        <i class="fa-solid fa-times mr-2"></i>
                        {{ __('messages.cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
        // Mục đích: Điều khiển Live Demo modal (mở/đóng), mô phỏng chọn & hiển thị kết quả
        const open = document.getElementById('openDemo');
        const modal = document.getElementById('demoModal');
        const close = document.getElementById('closeDemo');
        const close2 = document.getElementById('btnCloseDemo2');
        const voteBtn = document.getElementById('btnDemoVote');
        const voteWrap = document.getElementById('demoVote');
        const resultWrap = document.getElementById('demoResults');
        const btnText = voteBtn?.querySelector('.btn-text');
        const btnLoading = voteBtn?.querySelector('.btn-loading');
        let userChoice = null; // Store user's selected choice
        // Fake data - random numbers between 180-280
        const counts = { 
            easy: Math.floor(Math.random() * 100) + 180,
            'so-so': Math.floor(Math.random() * 80) + 150,
            hard: Math.floor(Math.random() * 60) + 120
        };
        
        // updateFakeResults()
        // - Tính lại tổng, cập nhật số liệu, width progress bar, badge (You)
        function updateFakeResults() {
            const total = counts['easy'] + counts['so-so'] + counts['hard'];
            document.getElementById('r-easy').textContent = counts['easy'];
            document.getElementById('r-so').textContent = counts['so-so'];
            document.getElementById('r-hard').textContent = counts['hard'];
            
            // Show/hide "(You)" badge
            document.querySelectorAll('.demo-you-badge').forEach(badge => {
                badge.classList.add('hidden');
            });
            if (userChoice) {
                const youBadge = document.querySelector(`.demo-you-badge[data-option="${userChoice}"]`);
                if (youBadge) {
                    youBadge.classList.remove('hidden');
                }
            }
            
            ['easy', 'so-so', 'hard'].forEach(key => {
                const progress = document.querySelector(`.demo-progress[data-value="${key}"]`);
                const percent = total > 0 ? (counts[key] / total) * 100 : 0;
                if (progress) {
                    progress.style.width = percent + '%';
                }
            });
        }

        // show(): mở modal demo, reset state dữ liệu và UI
        function show(){ 
            modal.classList.remove('hidden'); 
            modal.classList.add('flex'); 
            document.body.style.overflow = 'hidden';
            // Reset to initial fake data when opening
            counts.easy = Math.floor(Math.random() * 100) + 180;
            counts['so-so'] = Math.floor(Math.random() * 80) + 150;
            counts.hard = Math.floor(Math.random() * 60) + 120;
            userChoice = null; // Reset user choice
            // Reset form
            document.querySelectorAll('input[name="demo"]').forEach(radio => radio.checked = false);
            // Reset option styling
            document.querySelectorAll('.demo-option').forEach(opt => {
                opt.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
            });
            // Reset button state
            if (voteBtn) {
                voteBtn.disabled = true;
            }
            // Reset button loading state
            if (btnText) {
                btnText.classList.remove('hidden');
            }
            if (btnLoading) {
                btnLoading.classList.add('hidden');
            }
            // Reset all inline styles for vote section
            if (voteWrap) {
                voteWrap.style.opacity = '1';
                voteWrap.style.transform = 'translateY(0)';
                voteWrap.style.transition = '';
            }
            // Reset all inline styles for results section
            if (resultWrap) {
                resultWrap.style.opacity = '';
                resultWrap.style.transform = '';
                resultWrap.style.transition = '';
            }
            // Show vote section, hide results
            if (voteWrap) voteWrap.classList.remove('hidden');
            if (resultWrap) resultWrap.classList.add('hidden');
        }
        // hide(): đóng modal demo và khôi phục scroll body
        function hide(){ 
            modal.classList.add('hidden'); 
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }

        open?.addEventListener('click', show);
        close?.addEventListener('click', hide);
        modal?.addEventListener('click', (e)=>{ 
            if (e.target === modal || e.target.classList.contains('absolute')) hide(); 
        });
        close2?.addEventListener('click', hide);

        // Lắng nghe thay đổi radio trong modal để bật nút Vote và highlight dòng được chọn
        modal?.addEventListener('change', (e)=>{
            if (e.target && e.target.name === 'demo') {
                voteBtn.disabled = false;
                // Remove checked state from all options
                document.querySelectorAll('.demo-option').forEach(opt => {
                    opt.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
                });
                // Add checked state to selected
                const selectedOption = e.target.closest('.demo-option');
                if (selectedOption) {
                    selectedOption.classList.add('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
                }
            }
        });

        // Cho phép click anywhere trên option (label container) để chọn radio
        document.querySelectorAll('.demo-option').forEach(opt => {
            opt.addEventListener('click', function(e) {
                if (e.target.tagName !== 'INPUT') {
                    const radio = this.querySelector('input[type="radio"]');
                    if (radio) radio.click();
                }
            });
        });

        // Xử lý Vote: hiển thị loading, tăng số liệu giả lập, chuyển animation sang kết quả
        voteBtn?.addEventListener('click', function(){
            const selected = modal.querySelector('input[name="demo"]:checked');
            if (!selected) return;
            const key = selected.value;
            userChoice = key; // Store user's choice
            
            // Show loading
            btnText?.classList.add('hidden');
            btnLoading?.classList.remove('hidden');
            voteBtn.disabled = true;

            setTimeout(() => {
                counts[key] += Math.floor(Math.random() * 3) + 1; // Add 1-3 votes
                const total = counts['easy'] + counts['so-so'] + counts['hard'];
                
                // Update results with animation
                updateFakeResults();
                
                // Hide vote, show results with animation
                voteWrap.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                voteWrap.style.opacity = '0';
                voteWrap.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    voteWrap.classList.add('hidden');
                    voteWrap.style.opacity = ''; // Clear inline style
                    voteWrap.style.transform = ''; // Clear inline style
                    voteWrap.style.transition = ''; // Clear inline style
                    
                    resultWrap.classList.remove('hidden');
                    resultWrap.style.transition = 'opacity 0.4s ease-out, transform 0.4s ease-out';
                    resultWrap.style.opacity = '0';
                    resultWrap.style.transform = 'translateY(10px)';
                    setTimeout(() => {
                        resultWrap.style.opacity = '1';
                        resultWrap.style.transform = 'translateY(0)';
                    }, 50);
                }, 300);
            }, 800);
        });
    });
    </script>
    
    <style>
    .icon-button {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: none;
        background: transparent;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: var(--on-surface-variant);
        transition: all 0.2s ease;
    }
    .icon-button:hover {
        background-color: var(--surface-variant);
        color: var(--on-surface);
    }
    .demo-option {
        transition: all 0.2s ease;
    }
    .demo-option:has(input:checked) {
        border-color: var(--primary);
        background-color: rgba(23, 107, 239, 0.05);
    }
    .dark .demo-option:has(input:checked) {
        background-color: rgba(23, 107, 239, 0.15);
    }
    #demoVote, #demoResults {
        transition: opacity 0.3s ease, transform 0.3s ease;
    }
    </style>
</x-app-layout>


