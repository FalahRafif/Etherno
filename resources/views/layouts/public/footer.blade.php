<footer class="public-footer" aria-label="Footer">
    {{-- Main footer body --}}
    <div class="footer-body">
        <div class="container">

            {{-- Gold ornament line --}}
            <div class="footer-ornament" aria-hidden="true">
                <span class="footer-ornament-line"></span>
                <span class="footer-ornament-diamond"></span>
                <span class="footer-ornament-line"></span>
            </div>

            <div class="footer-grid">

                {{-- Brand column --}}
                <div class="footer-brand">
                    @php
                        $icon = public_path('assets/etherno/public/icon_trans_white_1.png');
                    @endphp
                    @if(file_exists($icon))
                        <div class="footer-logo-wrap">
                            <img src="{{ asset('assets/etherno/public/icon_trans_white_1.png') }}" alt="Etherno Photography" class="footer-logo">
                        </div>
                    @endif
                    <p class="footer-brand-tagline">Etherno Photography</p>
                    <p class="footer-text">Mengabadikan momen terpenting dalam hidup Anda dengan sentuhan sinematik, elegan, dan penuh makna — untuk dikenang selamanya.</p>

                    <div class="footer-socials">
                        <a href="#" class="footer-social-btn" aria-label="Instagram" rel="noopener">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                        </a>
                        <a href="https://wa.me/62" class="footer-social-btn" aria-label="WhatsApp" rel="noopener" target="_blank">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                        </a>
                        <a href="mailto:hello@etherno.id" class="footer-social-btn" aria-label="Email">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </a>
                    </div>
                </div>

                {{-- Nav column --}}
                <div class="footer-col">
                    <p class="footer-col-title">
                        <span class="footer-col-title-decor" aria-hidden="true"></span>
                        Jelajahi
                    </p>
                    <nav class="footer-nav" aria-label="Footer navigasi">
                        <a href="{{ route('home') }}">Beranda</a>
                        <a href="{{ route('home') }}#portfolio">Portofolio</a>
                        <a href="{{ route('packages.page') }}">Semua Paket</a>
                        <a href="{{ route('home') }}#packages">Paket Unggulan</a>
                        <a href="{{ route('home') }}#faq">FAQ</a>
                        <a href="{{ route('about.etherno') }}">Tentang Kami</a>
                    </nav>
                </div>

                {{-- Booking column --}}
                <div class="footer-col">
                    <p class="footer-col-title">
                        <span class="footer-col-title-decor" aria-hidden="true"></span>
                        Booking
                    </p>
                    <nav class="footer-nav" aria-label="Footer booking">
                        <a href="{{ route('booking.page') }}">Pesan Sekarang</a>
                        <a href="{{ route('booking.status') }}">Cek Status Booking</a>
                        <a href="{{ route('booking.policy') }}">Kebijakan Pembatalan</a>
                        <a href="{{ route('booking.reschedule') }}">Reschedule</a>
                    </nav>

                    <div class="footer-contact-block">
                        <p class="footer-contact-label">Hubungi Kami</p>
                        <a href="mailto:hello@etherno.id" class="footer-contact-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            hello@etherno.id
                        </a>
                        <a href="https://wa.me/62" class="footer-contact-item" target="_blank" rel="noopener">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                            WhatsApp
                        </a>
                    </div>
                </div>

            </div>{{-- /.footer-grid --}}

            {{-- Bottom bar --}}
            <div class="footer-bottom">
                <div class="footer-bottom-line" aria-hidden="true"></div>
                <div class="footer-bottom-inner">
                    <span class="footer-copyright">&copy; {{ date('Y') }} Etherno Photography. Seluruh hak dilindungi.</span>
                    <span class="footer-bottom-tagline">Crafted with care &mdash; Bina Sarana Informatika</span>
                </div>
            </div>

        </div>{{-- /.container --}}
    </div>{{-- /.footer-body --}}

</footer>
