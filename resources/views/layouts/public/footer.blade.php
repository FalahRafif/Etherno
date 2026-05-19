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
                    <p class="eyebrow footer-eyebrow">Etherno Pernikahan</p>
                    <p class="footer-text">Dokumentasi pernikahan dengan identitas visual yang sinematik dan elegan.</p>
                </div>
            </div>
        </div>

        <div class="footer-links">
            <p class="footer-title">Navigasi</p>
            <a href="#portfolio">Portofolio</a>
            <a href="#packages">Paket</a>
            <a href="#faq">FAQ</a>
        </div>

        <div class="footer-links">
            <p class="footer-title">Kontak</p>
            <a href="#booking">Pesan Sekarang</a>
            <a href="mailto:hello@etherno.id">hello@etherno.id</a>
            <a href="#">Instagram</a>
        </div>
    </div>
    <div class="container footer-bottom">
        <span>© {{ date('Y') }} Etherno. Seluruh hak dilindungi.</span>
        <span>Dokumentasi pernikahan yang elegan.</span>
    </div>
</footer>
