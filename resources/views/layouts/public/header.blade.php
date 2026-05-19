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
        <nav class="nav small-uppercase">
            <a href="#portfolio">Portofolio</a>
            <a href="#packages">Paket</a>
            <a href="#faq">FAQ</a>
        </nav>
        <a class="cta" href="#booking" aria-label="Pesan Sekarang">Pesan Sekarang</a>
    </div>
</header>
