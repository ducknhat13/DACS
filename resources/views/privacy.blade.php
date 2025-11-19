<!--
    Privacy Page
    - Trang Chính sách bảo mật, mô tả cách thu thập và xử lý dữ liệu.
    - Tĩnh, không phụ thuộc script ngoài để tối ưu tốc độ tải.
    - Gợi ý khối: dữ liệu thu thập, mục đích, lưu trữ, chia sẻ, cookie, liên hệ.
    - Frontend: cho danh sách bullets ngắn gọn; liên kết tới email/contact nếu cần.
-->
<x-app-layout>
    <x-slot name="header">
        <div class="hidden"></div>
    </x-slot>

    <!-- HERO -->
    <section class="relative py-12 sm:py-16 md:py-20 lg:py-28 page-transition" style="background: linear-gradient(135deg, rgba(23,107,239,0.1) 0%, rgba(23,107,239,0.05) 100%);">
        <div class="container-material px-4">
            <div class="max-w-3xl mx-auto text-center">
                <h1 class="text-2xl sm:text-3xl md:text-display-medium lg:text-display-large font-semibold mb-4">{{ __('messages.privacy_title') }}</h1>
                <p class="text-base sm:text-lg md:text-title-large text-[color:var(--on-surface-variant)]">{{ __('messages.privacy_last_updated') }}</p>
            </div>
        </div>
    </section>

    <!-- CONTENT -->
    <section class="section-padding-sm">
        <div class="container-material px-4">
            <div class="max-w-4xl mx-auto">
                <div class="card p-4 sm:p-6 md:p-8 lg:p-10">
                    <!-- Introduction -->
                    <div class="mb-8">
                        <h2 class="text-headline-medium font-semibold mb-4">{{ __('messages.privacy_intro_title') }}</h2>
                        <p class="text-body-large text-[color:var(--on-surface-variant)] mb-4">{{ __('messages.privacy_intro_text') }}</p>
                    </div>

                    <!-- Section 1 -->
                    <div class="mb-8 pb-8 border-b border-[color:var(--outline)]">
                        <h3 class="text-headline-small font-semibold mb-4">{{ __('messages.privacy_collect_title') }}</h3>
                        <p class="text-body-medium text-[color:var(--on-surface-variant)] mb-4">{{ __('messages.privacy_collect_text') }}</p>
                        <ul class="list-disc list-inside space-y-2 text-body-medium text-[color:var(--on-surface-variant)] ml-4">
                            <li>{{ __('messages.privacy_collect_item1') }}</li>
                            <li>{{ __('messages.privacy_collect_item2') }}</li>
                            <li>{{ __('messages.privacy_collect_item3') }}</li>
                            <li>{{ __('messages.privacy_collect_item4') }}</li>
                        </ul>
                    </div>

                    <!-- Section 2 -->
                    <div class="mb-8 pb-8 border-b border-[color:var(--outline)]">
                        <h3 class="text-headline-small font-semibold mb-4">{{ __('messages.privacy_use_title') }}</h3>
                        <p class="text-body-medium text-[color:var(--on-surface-variant)] mb-4">{{ __('messages.privacy_use_text') }}</p>
                        <ul class="list-disc list-inside space-y-2 text-body-medium text-[color:var(--on-surface-variant)] ml-4">
                            <li>{{ __('messages.privacy_use_item1') }}</li>
                            <li>{{ __('messages.privacy_use_item2') }}</li>
                            <li>{{ __('messages.privacy_use_item3') }}</li>
                            <li>{{ __('messages.privacy_use_item4') }}</li>
                        </ul>
                    </div>

                    <!-- Section 3 -->
                    <div class="mb-8 pb-8 border-b border-[color:var(--outline)]">
                        <h3 class="text-headline-small font-semibold mb-4">{{ __('messages.privacy_share_title') }}</h3>
                        <p class="text-body-medium text-[color:var(--on-surface-variant)] mb-4">{{ __('messages.privacy_share_text') }}</p>
                    </div>

                    <!-- Section 4 -->
                    <div class="mb-8 pb-8 border-b border-[color:var(--outline)]">
                        <h3 class="text-headline-small font-semibold mb-4">{{ __('messages.privacy_security_title') }}</h3>
                        <p class="text-body-medium text-[color:var(--on-surface-variant)] mb-4">{{ __('messages.privacy_security_text') }}</p>
                    </div>

                    <!-- Section 5 -->
                    <div class="mb-8 pb-8 border-b border-[color:var(--outline)]">
                        <h3 class="text-headline-small font-semibold mb-4">{{ __('messages.privacy_cookies_title') }}</h3>
                        <p class="text-body-medium text-[color:var(--on-surface-variant)] mb-4">{{ __('messages.privacy_cookies_text') }}</p>
                    </div>

                    <!-- Section 6 -->
                    <div class="mb-8 pb-8 border-b border-[color:var(--outline)]">
                        <h3 class="text-headline-small font-semibold mb-4">{{ __('messages.privacy_rights_title') }}</h3>
                        <p class="text-body-medium text-[color:var(--on-surface-variant)] mb-4">{{ __('messages.privacy_rights_text') }}</p>
                        <ul class="list-disc list-inside space-y-2 text-body-medium text-[color:var(--on-surface-variant)] ml-4">
                            <li>{{ __('messages.privacy_rights_item1') }}</li>
                            <li>{{ __('messages.privacy_rights_item2') }}</li>
                            <li>{{ __('messages.privacy_rights_item3') }}</li>
                            <li>{{ __('messages.privacy_rights_item4') }}</li>
                        </ul>
                    </div>

                    <!-- Section 7 -->
                    <div class="mb-8 pb-8 border-b border-[color:var(--outline)]">
                        <h3 class="text-headline-small font-semibold mb-4">{{ __('messages.privacy_changes_title') }}</h3>
                        <p class="text-body-medium text-[color:var(--on-surface-variant)] mb-4">{{ __('messages.privacy_changes_text') }}</p>
                    </div>

                    <!-- Contact -->
                    <div class="mt-8">
                        <h3 class="text-headline-small font-semibold mb-4">{{ __('messages.privacy_contact_title') }}</h3>
                        <p class="text-body-medium text-[color:var(--on-surface-variant)] mb-4">{{ __('messages.privacy_contact_text') }}</p>
                        <p class="text-body-medium">
                            <a href="{{ route('contact') }}" class="text-primary-600 hover:underline">{{ __('messages.contact_us') }}</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>

