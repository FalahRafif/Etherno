<footer class="public-footer">
    <div class="container footer-grid">
        <div class="footer-brand">
            <div class="footer-brandline">
                @php
                    $icon = public_path('assets/images/icon.jpg');
                    $fallback = public_path('assets/images/photos/aboutmain.jpg');
                @endphp
                @if(file_exists($icon))
                    <img src="{{ asset('assets/images/icon.jpg') }}" alt="Etherno" class="footer-logo">
                @elseif(file_exists($fallback))
                    <img src="{{ asset('assets/images/photos/aboutmain.jpg') }}" alt="Etherno" class="footer-logo">
                @endif
                <div>
                    <p class="eyebrow footer-eyebrow">Etherno Wedding</p>
                    <p class="footer-text">Wedding photography with a cinematic and elegant visual identity.</p>
                </div>
            </div>
        </div>

        <div class="footer-links">
            <p class="footer-title">Navigate</p>
            <a href="#portfolio">Portfolio</a>
            <a href="#packages">Packages</a>
            <a href="#faq">FAQ</a>
        </div>

        <div class="footer-links">
            <p class="footer-title">Contact</p>
            <a href="#booking">Book Sekarang</a>
            <a href="mailto:hello@etherno.id">hello@etherno.id</a>
            <a href="#">Instagram</a>
        </div>
    </div>
    <div class="container footer-bottom">
        <span>© {{ date('Y') }} Etherno. All rights reserved.</span>
        <span>Elegant wedding photography.</span>
    </div>
</footer>
