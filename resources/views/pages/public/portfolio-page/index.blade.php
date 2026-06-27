@extends('layouts.guest.guest')

@section('content')
@php
  $portfolioDirectory = public_path('assets/etherno/portfolio_image');
  $portfolioExtensions = ['jpg', 'jpeg', 'png', 'webp', 'JPG', 'JPEG', 'PNG', 'WEBP'];
  $portfolioImages = collect($portfolioExtensions)
      ->flatMap(fn ($extension) => glob($portfolioDirectory . DIRECTORY_SEPARATOR . '*.' . $extension) ?: [])
      ->unique()
      ->sortBy(fn ($path) => strtolower(basename($path)), SORT_NATURAL)
      ->values()
      ->map(fn ($path) => 'assets/etherno/portfolio_image/' . basename($path));
@endphp

<section class="section-block container portfolio-page-section">
  <div class="section-heading">
    <p class="eyebrow">Portfolio Etherno</p>
    <h2>Rangkaian momen yang kami dokumentasikan dengan tenang dan elegan</h2>
    <p class="section-lead">Lihat tone warna, komposisi, dan cara Etherno menangkap emosi pada berbagai suasana acara, dari wedding, engagement, traditional, indoor, hingga outdoor.</p>
  </div>

  <div class="portfolio-category-list" aria-label="Kategori portfolio">
    <span>Wedding</span>
    <span>Engagement</span>
    <span>Traditional</span>
    <span>Indoor</span>
    <span>Outdoor</span>
  </div>

  @if ($portfolioImages->isNotEmpty())
    <div class="portfolio-page-grid" aria-label="Semua portfolio dokumentasi Etherno">
      @foreach ($portfolioImages as $imagePath)
        <figure class="portfolio-page-item {{ $loop->iteration % 7 === 1 ? 'is-featured' : '' }} {{ $loop->iteration % 5 === 0 ? 'is-tall' : '' }}">
          <img src="{{ asset($imagePath) }}" alt="Portfolio Etherno Photography {{ $loop->iteration }}" loading="lazy">
        </figure>
      @endforeach
    </div>
  @else
    <div class="package-empty">
      <p>Portfolio sedang diperbarui. Silakan hubungi kami untuk melihat contoh dokumentasi terbaru.</p>
    </div>
  @endif

  <div class="portfolio-page-actions">
    <a class="cta" href="{{ route('booking.page') }}">Booking Sekarang</a>
    <a class="cta cta-outline" href="{{ route('home') }}#packages">Lihat Paket</a>
  </div>
</section>

@include('pages.public.partials.instagram-section')
@endsection
