<!--
    Terms Page
    - Trang Điều khoản sử dụng của QuickPoll.
    - Nội dung tĩnh, trình bày quy định và trách nhiệm khi sử dụng hệ thống.
    - Gợi ý bố cục: mục lục (anchor), điều khoản, quyền & nghĩa vụ, giới hạn trách nhiệm, liên hệ.
    - Frontend: dùng heading rõ ràng (h2/h3), khoảng cách dòng thoáng để dễ đọc.
-->
<x-app-layout>
    <x-slot name="header">
        <div class="hidden"></div>
    </x-slot>

    <!-- HERO -->
    <section class="relative py-12 sm:py-16 md:py-20 lg:py-28 page-transition" style="background: linear-gradient(135deg, rgba(23,107,239,0.1) 0%, rgba(23,107,239,0.05) 100%);">
        <div class="container-material px-4">
            <div class="max-w-3xl mx-auto text-center">
                <h1 class="text-2xl sm:text-3xl md:text-display-medium lg:text-display-large font-semibold mb-4">{{ __('messages.terms_title') }}</h1>
                <p class="text-base sm:text-lg md:text-title-large text-[color:var(--on-surface-variant)]">{{ __('messages.terms_last_updated') }}</p>
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
                        <h2 class="text-headline-medium font-semibold mb-4">{{ __('messages.terms_intro_title') }}</h2>
                        <p class="text-body-large text-[color:var(--on-surface-variant)] mb-4">{{ __('messages.terms_intro_text') }}</p>
                    </div>

                    <!-- Section 1 -->
                    <div class="mb-8 pb-8 border-b border-[color:var(--outline)]">
                        <h3 class="text-headline-small font-semibold mb-4">{{ __('messages.terms_acceptance_title') }}</h3>
                        <p class="text-body-medium text-[color:var(--on-surface-variant)] mb-4">{{ __('messages.terms_acceptance_text') }}</p>
                        <ul class="list-disc list-inside space-y-2 text-body-medium text-[color:var(--on-surface-variant)] ml-4">
                            <li>{{ __('messages.terms_acceptance_item1') }}</li>
                            <li>{{ __('messages.terms_acceptance_item2') }}</li>
                            <li>{{ __('messages.terms_acceptance_item3') }}</li>
                        </ul>
                    </div>

                    <!-- Section 2 -->
                    <div class="mb-8 pb-8 border-b border-[color:var(--outline)]">
                        <h3 class="text-headline-small font-semibold mb-4">{{ __('messages.terms_use_title') }}</h3>
                        <p class="text-body-medium text-[color:var(--on-surface-variant)] mb-4">{{ __('messages.terms_use_text') }}</p>
                        <ul class="list-disc list-inside space-y-2 text-body-medium text-[color:var(--on-surface-variant)] ml-4">
                            <li>{{ __('messages.terms_use_item1') }}</li>
                            <li>{{ __('messages.terms_use_item2') }}</li>
                            <li>{{ __('messages.terms_use_item3') }}</li>
                            <li>{{ __('messages.terms_use_item4') }}</li>
                        </ul>
                    </div>

                    <!-- Section 3 -->
                    <div class="mb-8 pb-8 border-b border-[color:var(--outline)]">
                        <h3 class="text-headline-small font-semibold mb-4">{{ __('messages.terms_content_title') }}</h3>
                        <p class="text-body-medium text-[color:var(--on-surface-variant)] mb-4">{{ __('messages.terms_content_text') }}</p>
                    </div>

                    <!-- Section 4 -->
                    <div class="mb-8 pb-8 border-b border-[color:var(--outline)]">
                        <h3 class="text-headline-small font-semibold mb-4">{{ __('messages.terms_account_title') }}</h3>
                        <p class="text-body-medium text-[color:var(--on-surface-variant)] mb-4">{{ __('messages.terms_account_text') }}</p>
                    </div>

                    <!-- Section 5 -->
                    <div class="mb-8 pb-8 border-b border-[color:var(--outline)]">
                        <h3 class="text-headline-small font-semibold mb-4">{{ __('messages.terms_privacy_title') }}</h3>
                        <p class="text-body-medium text-[color:var(--on-surface-variant)] mb-4">{{ __('messages.terms_privacy_text') }}</p>
                        <p class="text-body-medium text-[color:var(--on-surface-variant)]">
                            <a href="{{ route('privacy') }}" class="text-primary-600 hover:underline">{{ __('messages.view_privacy_policy') }}</a>
                        </p>
                    </div>

                    <!-- Section 6 -->
                    <div class="mb-8 pb-8 border-b border-[color:var(--outline)]">
                        <h3 class="text-headline-small font-semibold mb-4">{{ __('messages.terms_termination_title') }}</h3>
                        <p class="text-body-medium text-[color:var(--on-surface-variant)] mb-4">{{ __('messages.terms_termination_text') }}</p>
                    </div>

                    <!-- Section 7 -->
                    <div class="mb-8 pb-8 border-b border-[color:var(--outline)]">
                        <h3 class="text-headline-small font-semibold mb-4">{{ __('messages.terms_changes_title') }}</h3>
                        <p class="text-body-medium text-[color:var(--on-surface-variant)] mb-4">{{ __('messages.terms_changes_text') }}</p>
                    </div>

                    <!-- Contact -->
                    <div class="mt-8">
                        <h3 class="text-headline-small font-semibold mb-4">{{ __('messages.terms_contact_title') }}</h3>
                        <p class="text-body-medium text-[color:var(--on-surface-variant)] mb-4">{{ __('messages.terms_contact_text') }}</p>
                        <p class="text-body-medium">
                            <a href="{{ route('contact') }}" class="text-primary-600 hover:underline">{{ __('messages.contact_us') }}</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>

