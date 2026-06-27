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

<section class="section-block container portfolio-slider-section" id="wedding-stories">
  <div class="section-heading">
    <p class="eyebrow">Portfolio</p>
    <h2>Cuplikan dokumentasi wedding dan momen istimewa</h2>
    <p class="section-lead">Beberapa hasil visual Etherno untuk membantu Anda merasakan tone dokumentasi, komposisi, dan cerita yang akan dibangun di hari acara.</p>
  </div>

  <div class="portfolio-category-list" aria-label="Kategori portfolio">
    <span>Wedding</span>
    <span>Engagement</span>
    <span>Traditional</span>
    <span>Indoor</span>
    <span>Outdoor</span>
  </div>

  @if ($portfolioImages->isNotEmpty())
    <div class="portfolio-masonry" aria-label="Portfolio dokumentasi Etherno" data-mobile-auto-scroll>
      @foreach ($portfolioImages->take(8) as $imagePath)
        <figure class="portfolio-masonry-item {{ $loop->first ? 'is-featured' : '' }} {{ $loop->iteration === 4 ? 'is-tall' : '' }}">
          <img src="{{ asset($imagePath) }}" alt="Portfolio dokumentasi Etherno {{ $loop->iteration }}" loading="lazy">
        </figure>
      @endforeach
      @foreach ($portfolioImages->take(8) as $imagePath)
        <figure class="portfolio-masonry-item is-clone" aria-hidden="true">
          <img src="{{ asset($imagePath) }}" alt="" loading="lazy">
        </figure>
      @endforeach
    </div>
    <div class="portfolio-more">
      <a class="cta cta-outline" href="{{ route('portfolio.page') }}">Lihat Semua Portfolio</a>
    </div>
  @endif
</section>
