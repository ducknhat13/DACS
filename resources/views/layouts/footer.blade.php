<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-top">
            <div class="footer-brand">
                <a href="{{ route('dashboard') }}" class="footer-logo">
                    <x-application-logo class="h-6 w-auto" />
                </a>
                <p class="footer-description">QuickPoll — Create, Share, Analyze.</p>
            </div>

            <div class="footer-links">
                <div class="footer-column">
                    <div class="footer-title">Products</div>
                    <a href="{{ route('dashboard') }}" class="footer-link">Dashboard</a>
                    <a href="{{ route('polls.create') }}" class="footer-link">Create Poll</a>
                    <a href="#" class="footer-link">View Results</a>
                    <a href="#" class="footer-link">Share</a>
                </div>
                <div class="footer-column">
                    <div class="footer-title">Company</div>
                    <a href="#" class="footer-link">About Us</a>
                    <a href="#" class="footer-link">Contact</a>
                    <a href="#" class="footer-link">Career</a>
                </div>
                <div class="footer-column">
                    <div class="footer-title">Legal</div>
                    <a href="#" class="footer-link">Terms of Service</a>
                    <a href="#" class="footer-link">Privacy Policy</a>
                </div>
            </div>
        </div>

        <div class="footer-separator"></div>

        <div class="footer-bottom">
            <div class="footer-copyright">© {{ date('Y') }} QuickPoll. All rights reserved.</div>
            <div class="footer-lang">
                <button type="button" class="footer-lang-button">
                    <span class="material-symbols-rounded">language</span>
                    <span>English</span>
                </button>
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


