<header class="public-header">
    <div class="container header-inner">
        <a class="brand" href="{{ url('/') }}">
            @if(file_exists(public_path('assets/images/logo.png')))
                <img src="{{ asset('assets/images/logo.png') }}" alt="Etherno" class="brand-logo">
            @else
                <span class="brand-text">Etherno</span>
            @endif
        </a>
        <nav class="nav">
            <a href="#portfolio">Portfolio</a>
            <a href="#packages">Paket</a>
            <a href="#faq">FAQ</a>
        </nav>
        <a class="cta" href="#booking">Book Sekarang</a>
    </div>
</header>
