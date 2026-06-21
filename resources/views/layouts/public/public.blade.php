@include('layouts.public.assets')

@include('layouts.public.header')

<main class="public-main">
    @yield('content')
</main>

@include('layouts.public.footer')

<button class="scroll-top-button" type="button" aria-label="Kembali ke atas" data-scroll-top>
    <img src="{{ asset('assets/etherno/public/icon_trans_2.png') }}" alt="" aria-hidden="true">
</button>

@include('layouts.public.scripts')
