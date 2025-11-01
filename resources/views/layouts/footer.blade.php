<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-top">
            <div class="footer-brand">
                <a href="{{ route('home') }}" class="footer-logo">
                    <x-application-logo class="h-6 w-auto" />
                </a>
                <p class="footer-description">{{ __('messages.create_poll_subtext') }}</p>
            </div>

            <div class="footer-links grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="footer-column flex flex-col">
                    <div class="footer-title mb-2">{{ __('app.products') }}</div>
                    <a href="{{ route('dashboard') }}" class="footer-link mb-1">{{ __('app.dashboard') }}</a>
                    <a href="{{ route('polls.create') }}" class="footer-link mb-1">{{ __('messages.create_poll') }}</a>
                </div>
                <div class="footer-column flex flex-col mt-4 md:mt-0">
                    <div class="footer-title mb-2">{{ __('app.company') }}</div>
                    <a href="{{ route('about') }}" class="footer-link mb-1">{{ __('app.about_us') }}</a>
                    <a href="{{ route('contact') }}" class="footer-link mb-1">{{ __('app.contact') }}</a>
                    <a href="#" class="footer-link">{{ __('app.career') }}</a>
                </div>
                <div class="footer-column flex flex-col mt-4 md:mt-0">
                    <div class="footer-title mb-2">{{ __('app.legal') }}</div>
                    <a href="{{ route('terms') }}" class="footer-link mb-1">{{ __('app.terms_of_service') }}</a>
                    <a href="{{ route('privacy') }}" class="footer-link">{{ __('app.privacy_policy') }}</a>
                </div>
            </div>
        </div>

        <div class="footer-separator"></div>

        <div class="footer-bottom">
            <div class="footer-copyright">© {{ date('Y') }} QuickPoll. {{ __('app.all_rights_reserved') }}.</div>
            <div class="footer-lang">
                <a
                    href="{{ route('locale.switch', app()->getLocale() === 'en' ? 'vi' : 'en') }}"
                    class="footer-lang-button"
                    title="{{ __('app.language') }}"
                >
                    <span class="material-symbols-rounded">language</span>
                    <span>{{ app()->getLocale() == 'en' ? __('app.english') : __('app.vietnamese') }}</span>
                </a>
            </div>
        </div>
    </div>

    <button id="scrollTopBtn" class="scroll-top" aria-label="Scroll to top" title="Scroll to top">
        <span class="material-symbols-rounded">arrow_upward</span>
    </button>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const btn = document.getElementById('scrollTopBtn');
    if (!btn) return;
    const toggle = () => {
        if (window.scrollY > 240) btn.classList.add('show');
        else btn.classList.remove('show');
    };
    toggle();
    window.addEventListener('scroll', toggle);
    btn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
});
</script>


