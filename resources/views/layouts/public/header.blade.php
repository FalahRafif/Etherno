<header class="public-header">
    <div class="container header-inner">
        <a class="brand" href="{{ url('/') }}" aria-label="Etherno homepage">
            @php
                $icon = public_path('assets/etherno/icon.jpg');
                $fallback = public_path('assets/images/photos/aboutmain.jpg');
            @endphp
            @if(file_exists($icon))
                <img src="{{ asset('assets/etherno/icon.jpg') }}" alt="Etherno" class="brand-logo">
            @elseif(file_exists($fallback))
                <img src="{{ asset('assets/images/photos/aboutmain.jpg') }}" alt="Etherno" class="brand-logo">
            @else
                <span class="brand-text">Etherno</span>
            @endif
        </a>
        <nav class="nav small-uppercase">
            <a href="#portfolio">Portfolio</a>
            <a href="#packages">Paket</a>
            <a href="#faq">FAQ</a>
        </nav>
        <a class="cta" href="#booking" aria-label="Book Sekarang">Book Sekarang</a>
    </div>
</header>
