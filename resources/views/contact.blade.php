{{--
    Page: contact
    - Form liên hệ: name, email, subject, message; POST tới ContactController@store.
    - Frontend: hiển thị thông báo lỗi/thành công; tránh reload khi chưa cần.
--}}
{{--
    Contact Us Page - contact.blade.php
    
    Trang liên hệ với Material Design 3 design.
    
    Layout:
    - Two-column: Contact info (left) và Contact form (right)
    
    Features:
    - Contact information: Email, phone, response time
    - Social media links: Facebook, Twitter, LinkedIn
    - Contact form: Name, email, subject, message
    - Auto-fill form: Name và email pre-filled nếu user đã login
    - Form validation: Client-side và server-side validation
    - Success message: Flash message sau khi submit thành công
    
    Form Handling:
    - POST đến ContactController@store
    - Email được gửi đến support email từ config
    - Validation: Required fields, email format
    
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
                <h1 class="text-2xl sm:text-3xl md:text-display-medium lg:text-display-large font-semibold mb-4">{{ __('messages.contact_title') }}</h1>
                <p class="text-base sm:text-lg md:text-title-large text-[color:var(--on-surface-variant)]">{{ __('messages.contact_subtitle') }}</p>
            </div>
        </div>
    </section>

    <!-- CONTACT SECTION -->
    <section class="section-padding-sm">
        <div class="container-material px-4">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8 lg:gap-12">
                <!-- LEFT COLUMN: Contact Information -->
                <div class="lg:col-span-1">
                    <div class="mb-8">
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary-100 dark:bg-primary-900/30 mb-6">
                            <i class="fa-solid fa-envelope text-primary-600 dark:text-primary-400"></i>
                            <span class="text-label-medium text-primary-600 dark:text-primary-400 font-semibold">{{ __('messages.get_in_touch') }}</span>
                        </div>
                        <h2 class="text-headline-medium font-semibold mb-4">{{ __('messages.contact_us') }}</h2>
                        <p class="text-body-large text-[color:var(--on-surface-variant)]">{{ __('messages.contact_info_desc') }}</p>
                    </div>

                    <!-- Contact Info Cards -->
                    <div class="space-y-4">
                        <!-- Email -->
                        <div class="card p-6 hover:shadow-material-lg transition-all duration-300">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-envelope text-primary-600 dark:text-primary-400 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-title-medium font-semibold mb-1">{{ __('messages.email') }}</h3>
                                    <a href="mailto:{{ config('mail.support_email', 'support@quickpoll.com') }}" class="text-body-medium text-primary-600 hover:underline">{{ config('mail.support_email', 'support@quickpoll.com') }}</a>
                                </div>
                            </div>
                        </div>

                        <!-- Phone (Optional) -->
                        <div class="card p-6 hover:shadow-material-lg transition-all duration-300">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-full bg-success-100 dark:bg-success-900/30 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-phone text-success-600 dark:text-success-400 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-title-medium font-semibold mb-1">{{ __('messages.phone') }}</h3>
                                    <!-- <p class="text-body-medium text-[color:var(--on-surface-variant)]">{{ __('messages.phone_available') }}</p> -->
                                </div>
                            </div>
                        </div>

                        <!-- Response Time -->
                        <div class="card p-6 hover:shadow-material-lg transition-all duration-300">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-full bg-warning-100 dark:bg-warning-900/30 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-clock text-warning-600 dark:text-warning-400 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-title-medium font-semibold mb-1">{{ __('messages.response_time') }}</h3>
                                    <p class="text-body-medium text-[color:var(--on-surface-variant)]">{{ __('messages.response_time_desc') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Social Links (Optional) -->
                    <div class="mt-8">
                        <h3 class="text-title-medium font-semibold mb-4">{{ __('messages.follow_us') }}</h3>
                        <div class="flex items-center gap-4">
                            <a href="{{ config('app.social.facebook', '#') }}" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-full bg-surface-variant flex items-center justify-center hover:bg-primary-100 dark:hover:bg-primary-900/30 transition-colors" title="Facebook">
                                <i class="fa-brands fa-facebook text-[color:var(--on-surface-variant)] hover:text-primary-600"></i>
                            </a>
                            <a href="{{ config('app.social.twitter', '#') }}" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-full bg-surface-variant flex items-center justify-center hover:bg-primary-100 dark:hover:bg-primary-900/30 transition-colors" title="Twitter">
                                <i class="fa-brands fa-twitter text-[color:var(--on-surface-variant)] hover:text-primary-600"></i>
                            </a>
                            <a href="{{ config('app.social.linkedin', '#') }}" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-full bg-surface-variant flex items-center justify-center hover:bg-primary-100 dark:hover:bg-primary-900/30 transition-colors" title="LinkedIn">
                                <i class="fa-brands fa-linkedin text-[color:var(--on-surface-variant)] hover:text-primary-600"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Contact Form -->
                <div class="lg:col-span-2">
                    <div class="card p-4 sm:p-6 md:p-8 lg:p-10">
                        <h2 class="text-xl sm:text-2xl md:text-headline-medium font-semibold mb-4 sm:mb-6">{{ __('messages.send_us_message') }}</h2>
                        
                        @if(session('success'))
                            <div class="mb-6 p-4 rounded-xl bg-success-100 dark:bg-success-900/30 text-success-600 dark:text-success-400">
                                <i class="fa-solid fa-check-circle mr-2"></i>{{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="mb-6 p-4 rounded-xl bg-error-100 dark:bg-error-900/30 text-error-600 dark:text-error-400">
                                <i class="fa-solid fa-exclamation-circle mr-2"></i>{{ session('error') }}
                            </div>
                        @endif

                        <form id="contactForm" method="POST" action="{{ route('contact.store') }}" class="space-y-6">
                            @csrf
                            
                            <!-- Name Field -->
                            <div class="form-group">
                                <label for="name" class="form-label">{{ __('messages.name') }}</label>
                                <input 
                                    type="text" 
                                    id="name" 
                                    name="name" 
                                    class="form-input @error('name') border-error-500 @enderror" 
                                    value="{{ old('name', Auth::check() ? Auth::user()->name : '') }}"
                                    required
                                    {{ Auth::check() ? 'readonly' : '' }}
                                    placeholder="{{ __('messages.name_placeholder') }}"
                                    style="{{ Auth::check() ? 'background-color: var(--surface-variant); cursor: not-allowed;' : '' }}"
                                >
                                @if(Auth::check())
                                    <p class="mt-1 text-xs text-[color:var(--on-surface-variant)]">
                                        <i class="fa-solid fa-info-circle mr-1"></i>{{ __('messages.using_logged_in_account') }}
                                    </p>
                                @endif
                                @error('name')
                                    <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email Field -->
                            <div class="form-group">
                                <label for="email" class="form-label">{{ __('messages.email') }}</label>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    class="form-input @error('email') border-error-500 @enderror" 
                                    value="{{ old('email', Auth::check() ? Auth::user()->email : '') }}"
                                    required
                                    {{ Auth::check() ? 'readonly' : '' }}
                                    placeholder="{{ __('messages.email_placeholder') }}"
                                    style="{{ Auth::check() ? 'background-color: var(--surface-variant); cursor: not-allowed;' : '' }}"
                                >
                                @if(Auth::check())
                                    <p class="mt-1 text-xs text-[color:var(--on-surface-variant)]">
                                        <i class="fa-solid fa-info-circle mr-1"></i>{{ __('messages.using_logged_in_account') }}
                                    </p>
                                @endif
                                @error('email')
                                    <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Subject Field -->
                            <div class="form-group">
                                <label for="subject" class="form-label">{{ __('messages.subject') }}</label>
                                <input 
                                    type="text" 
                                    id="subject" 
                                    name="subject" 
                                    class="form-input @error('subject') border-error-500 @enderror" 
                                    value="{{ old('subject') }}"
                                    required
                                    placeholder="{{ __('messages.subject_placeholder') }}"
                                >
                                @error('subject')
                                    <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Message Field -->
                            <div class="form-group">
                                <label for="message" class="form-label">{{ __('messages.message') }}</label>
                                <textarea 
                                    id="message" 
                                    name="message" 
                                    rows="6" 
                                    class="form-input resize-none @error('message') border-error-500 @enderror" 
                                    required
                                    placeholder="{{ __('messages.message_placeholder') }}"
                                >{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="mt-1 text-sm text-error-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                                <button type="submit" id="submitBtn" class="btn btn-primary flex-1 sm:flex-none">
                                    <span id="btnText">{{ __('messages.send_message') }}</span>
                                    <span id="btnLoading" class="hidden">
                                        <i class="fa-solid fa-spinner fa-spin mr-2"></i>
                                        {{ __('messages.sending') }}
                                    </span>
                                </button>
                                <button type="reset" class="btn btn-neutral flex-1 sm:flex-none">
                                    {{ __('messages.reset') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const form = document.getElementById('contactForm');
    const btnText = document.getElementById('btnText');
    const btnLoading = document.getElementById('btnLoading');
    const submitBtn = document.getElementById('submitBtn');

    form?.addEventListener('submit', function(e){
        // Show loading
        btnText?.classList.add('hidden');
        btnLoading?.classList.remove('hidden');
        submitBtn.disabled = true;
    });
});
</script>

