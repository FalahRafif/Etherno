<section class="section-block container portfolio-slider-section" id="wedding-stories">
  <div class="section-heading">
    <p class="eyebrow">Portfolio</p>
    <h2>Cuplikan dokumentasi wedding dan momen istimewa</h2>
    <p class="section-lead">Beberapa hasil visual Etherno untuk membantu Anda merasakan tone dokumentasi, komposisi, dan cerita yang akan dibangun di hari acara.</p>
  </div>

  <div class="portfolio-slider" aria-label="Portfolio dokumentasi Etherno">
    <div class="portfolio-track">
      @foreach (range(1, 5) as $imageNumber)
        <figure class="portfolio-slide">
          <img src="{{ asset('assets/etherno/portfolio_image/img_' . $imageNumber . '.jpg') }}" alt="Portfolio dokumentasi Etherno {{ $imageNumber }}" loading="lazy">
        </figure>
      @endforeach

      @foreach (range(1, 5) as $imageNumber)
        <figure class="portfolio-slide" aria-hidden="true">
          <img src="{{ asset('assets/etherno/portfolio_image/img_' . $imageNumber . '.jpg') }}" alt="" loading="lazy">
        </figure>
      @endforeach
    </div>
  </div>
</section>
