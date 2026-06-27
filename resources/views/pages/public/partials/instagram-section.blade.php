@php
  $instagramAccounts = [
      [
          'name' => 'Etherno Photography',
          'handle' => '@etherno.photography',
          'url' => 'https://www.instagram.com/etherno.photography/',
          'label' => 'Wedding & Portrait',
          'description' => 'Kumpulan dokumentasi wedding, prewedding, dan visual signature Etherno.',
          'initial' => 'EP',
          'image' => 'assets/etherno/ig_profile/etherno.jpg',
      ],
      [
          'name' => 'Etherno Wedding',
          'handle' => '@etherno.wedding',
          'url' => 'https://www.instagram.com/etherno.wedding/',
          'label' => 'Wedding Stories',
          'description' => 'Highlight wedding story, detail acara, dan inspirasi dokumentasi pernikahan.',
          'initial' => 'EW',
          'image' => 'assets/etherno/ig_profile/wedding.jpg',
      ],
      [
          'name' => 'Etherno Graduate',
          'handle' => '@eth.graduate',
          'url' => 'https://www.instagram.com/eth.graduate/',
          'label' => 'Graduation Session',
          'description' => 'Referensi sesi wisuda, personal portrait, dan dokumentasi milestone akademik.',
          'initial' => 'EG',
          'image' => 'assets/etherno/ig_profile/graduate.jpg',
      ],
  ];
@endphp

<section class="section-block container instagram-section" id="instagram">
  <div class="section-heading">
    <p class="eyebrow">Instagram</p>
    <h2>Ikuti cerita terbaru Etherno di Instagram</h2>
    <p class="section-lead">Lihat update portfolio, behind the scene, dan inspirasi sesi terbaru dari akun Etherno yang paling sesuai dengan kebutuhan acara Anda.</p>
  </div>

  <div class="instagram-account-grid">
    @foreach ($instagramAccounts as $account)
      @php
        $profileImage = $account['image'] ?? '';
        $profileImagePath = $profileImage !== '' ? public_path($profileImage) : '';
        $profileImageUrl = $profileImage !== '' && file_exists($profileImagePath) ? asset($profileImage) : '';
      @endphp
      <article class="instagram-account-card">
        <div class="instagram-account-visual" aria-hidden="true">
          <span class="instagram-account-avatar">
            @if($profileImageUrl !== '')
              <img src="{{ $profileImageUrl }}" alt="" loading="lazy">
            @else
              {{ $account['initial'] }}
            @endif
          </span>
          <span class="instagram-feed-tile tile-one"></span>
          <span class="instagram-feed-tile tile-two"></span>
          <span class="instagram-feed-tile tile-three"></span>
        </div>
        <div class="instagram-account-body">
          <span class="instagram-account-label">{{ $account['label'] }}</span>
          <h3>{{ $account['name'] }}</h3>
          <p class="instagram-account-handle">{{ $account['handle'] }}</p>
          <p class="instagram-account-copy">{{ $account['description'] }}</p>
          <a class="cta cta-outline instagram-account-link" href="{{ $account['url'] }}" target="_blank" rel="noopener noreferrer">
            <i class="ri-instagram-line" aria-hidden="true"></i> Lihat Instagram
          </a>
        </div>
      </article>
    @endforeach
  </div>
</section>
