<header class="public-header">
    <div class="container header-inner">
        <a class="brand" href="{{ url('/') }}" aria-label="Beranda Etherno">
            @php
                $icon = public_path('assets/etherno/public/icon_trans_2.png');
                $fallback = public_path('assets/images/photos/aboutmain.jpg');
            @endphp
            @if(file_exists($icon))
                <img src="{{ asset('assets/etherno/public/icon_trans_2.png') }}" alt="Etherno" class="brand-logo">
            @elseif(file_exists($fallback))
                <img src="{{ asset('assets/images/photos/aboutmain.jpg') }}" alt="Etherno" class="brand-logo">
            @else
                <span class="brand-text">Etherno</span>
            @endif
        </a>

        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="public-menu" aria-label="Buka menu navigasi">
            <span class="menu-toggle-bar" aria-hidden="true"></span>
            <span class="menu-toggle-bar" aria-hidden="true"></span>
            <span class="menu-toggle-bar" aria-hidden="true"></span>
        </button>

        <div class="header-menu" id="public-menu">
            <nav class="nav small-uppercase" aria-label="Navigasi utama">
                <a href="{{ route('home') }}#portfolio">Portofolio</a>
                <a href="{{ route('home') }}#packages">Paket</a>
                <a href="{{ route('home') }}#faq">FAQ</a>
            </nav>
            <a class="cta header-cta" href="{{ route('booking.page') }}" aria-label="Pesan Sekarang">Pesan Sekarang</a>
        </div>
    </div>
</header>
