@include('layouts.public.assets')

@include('layouts.public.header')

<main class="public-main">
    @yield('content')
</main>

@include('layouts.public.footer')

<button class="scroll-top-button" type="button" aria-label="Kembali ke atas" data-scroll-top>
    <i class="ri-arrow-up-line" aria-hidden="true"></i>
</button>

@include('layouts.public.scripts')
