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

  @if ($portfolioImages->isNotEmpty())
    <div class="portfolio-slider" aria-label="Portfolio dokumentasi Etherno">
      <div class="portfolio-track">
        @foreach ($portfolioImages as $imagePath)
          <figure class="portfolio-slide">
            <img src="{{ asset($imagePath) }}" alt="Portfolio dokumentasi Etherno {{ $loop->iteration }}" loading="lazy">
          </figure>
        @endforeach

        @foreach ($portfolioImages as $imagePath)
          <figure class="portfolio-slide" aria-hidden="true">
            <img src="{{ asset($imagePath) }}" alt="" loading="lazy">
          </figure>
        @endforeach
      </div>
    </div>
  @endif
</section>
