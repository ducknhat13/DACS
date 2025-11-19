{{--
    Partial: layouts/footer
    - Footer toàn site: links chính sách, bản quyền, mạng xã hội (nếu có).
--}}
{{--
    Footer Layout - layouts/footer.blade.php
    
    Component này render site footer với Material Design 3 styling.
    
    Features:
    - Footer brand: Logo và description
    - Footer links: Products, Company, Legal sections
    - Language switcher: Chuyển đổi giữa tiếng Việt và tiếng Anh
    - Scroll to top button: Hiển thị khi scroll xuống > 240px
    - Copyright: Dynamic year từ date('Y')
    
    Footer Sections:
    - Products: Dashboard, Create Poll
    - Company: About, Contact, Career
    - Legal: Terms of Service, Privacy Policy
    
    JavaScript:
    - Scroll to top: Smooth scroll animation
    - Show/hide button: Dựa trên scroll position
    
    @author QuickPoll Team
--}}
<footer class="site-footer">
    <div class="footer-inner">
        {{-- Footer Top: Brand và Links --}}
        <div class="footer-top">
            {{-- Footer Brand: Logo và description --}}
            <div class="footer-brand">
                <a href="{{ route('home') }}" class="footer-logo">
                    <x-application-logo class="h-6 w-auto" />
                </a>
                <p class="footer-description">{{ __('messages.create_poll_subtext') }}</p>
            </div>

            {{-- Footer Links: Products, Company, Legal --}}
            <div class="footer-links grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Products Section --}}
                <div class="footer-column flex flex-col">
                    <div class="footer-title mb-2">{{ __('app.products') }}</div>
                    <a href="{{ route('dashboard') }}" class="footer-link mb-1">{{ __('app.dashboard') }}</a>
                    <a href="{{ route('polls.create') }}" class="footer-link mb-1">{{ __('messages.create_poll') }}</a>
                </div>
                {{-- Company Section --}}
                <div class="footer-column flex flex-col mt-4 md:mt-0">
                    <div class="footer-title mb-2">{{ __('app.company') }}</div>
                    <a href="{{ route('about') }}" class="footer-link mb-1">{{ __('app.about_us') }}</a>
                    <a href="{{ route('contact') }}" class="footer-link mb-1">{{ __('app.contact') }}</a>
                    <a href="#" class="footer-link">{{ __('app.career') }}</a>
                </div>
                {{-- Legal Section --}}
                <div class="footer-column flex flex-col mt-4 md:mt-0">
                    <div class="footer-title mb-2">{{ __('app.legal') }}</div>
                    <a href="{{ route('terms') }}" class="footer-link mb-1">{{ __('app.terms_of_service') }}</a>
                    <a href="{{ route('privacy') }}" class="footer-link">{{ __('app.privacy_policy') }}</a>
                </div>
            </div>
        </div>

        {{-- Footer Separator --}}
        <div class="footer-separator"></div>

        {{-- Footer Bottom: Copyright và Language Switcher --}}
        <div class="footer-bottom">
            {{-- Copyright: Dynamic year --}}
            <div class="footer-copyright">© {{ date('Y') }} QuickPoll. {{ __('app.all_rights_reserved') }}.</div>
            {{-- Language Switcher: Toggle giữa vi và en --}}
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

    {{-- Scroll to Top Button: Hiển thị khi scroll > 240px --}}
    <button id="scrollTopBtn" class="scroll-top" aria-label="Scroll to top" title="Scroll to top">
        <span class="material-symbols-rounded">arrow_upward</span>
    </button>
</footer>

<script>
/**
 * Scroll to Top Functionality
 * 
 * Hiển thị/ẩn scroll to top button dựa trên scroll position:
 * - Hiển thị khi scrollY > 240px
 * - Ẩn khi scrollY <= 240px
 * - Smooth scroll animation khi click
 */
document.addEventListener('DOMContentLoaded', function(){
    const btn = document.getElementById('scrollTopBtn');
    if (!btn) return;
    
    /**
     * Toggle button visibility dựa trên scroll position
     */
    const toggle = () => {
        if (window.scrollY > 240) btn.classList.add('show');
        else btn.classList.remove('show');
    };
    
    // Check initial position
    toggle();
    // Listen to scroll events
    window.addEventListener('scroll', toggle);
    
    // Smooth scroll to top khi click
    btn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
});
</script>


