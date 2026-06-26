@extends('layouts.guest.guest')

@section('content')
@php
  $weddingCollection = $weddingPackages ?? collect();
  $nonWeddingCollection = $nonWeddingPackages ?? collect();
  $totalPackages = $weddingCollection->count() + $nonWeddingCollection->count();

  $packageGroups = [
      [
          'title' => 'Paket Wedding',
          'description' => 'Semua paket wedding aktif yang tersedia saat ini.',
          'packages' => $weddingCollection,
          'empty_message' => 'Belum ada paket wedding aktif saat ini. Hubungi kami untuk rekomendasi.',
      ],
      [
          'title' => 'Paket Non Wedding',
          'description' => 'Semua paket non wedding aktif yang tersedia saat ini.',
          'packages' => $nonWeddingCollection,
          'empty_message' => 'Belum ada paket non wedding aktif saat ini. Hubungi kami untuk rekomendasi.',
      ],
  ];
@endphp

<section class="section-block container packages-page-section" id="all-packages">
  <div class="section-heading">
    <p class="eyebrow">Semua Produk</p>
    <h2>Daftar lengkap paket Etherno</h2>
    <p class="section-lead">Saat ini tersedia {{ $totalPackages }} paket aktif untuk kebutuhan wedding maupun non wedding.</p>
  </div>

  @foreach ($packageGroups as $group)
    <div class="package-group">
      <div class="package-group-heading">
        <h3 class="package-group-title">{{ $group['title'] }}</h3>
        <p class="package-group-copy">{{ $group['description'] }}</p>
      </div>

      <div class="package-grid">
        @forelse (($group['packages'] ?? collect()) as $package)
          @php
            $benefits = $package->benefits->pluck('name')->filter()->values();
            $thumbnailUrl = null;
            if ($package->thumbnailAttachment) {
                $thumbnailUrl = \Illuminate\Support\Facades\URL::signedRoute(
                    'api.public.attachments.package-thumbnail',
                    ['attachmentUuid' => $package->thumbnailAttachment->uuid],
                    now()->addMinutes((int) config('app.attachments.temp_url_ttl_minutes', 30))
                );
            }
            $packageTag = match (true) {
                $loop->first => 'Best Seller',
                $loop->index === 1 => 'Paling Direkomendasikan',
                default => 'Value Terbaik',
            };
          @endphp
          <article class="package {{ $loop->index === 1 ? 'package-featured' : 'package-soft' }}">
            @if ($thumbnailUrl)
              <figure class="package-image">
                <img src="{{ $thumbnailUrl }}" alt="{{ $package->name }}" loading="lazy">
              </figure>
            @endif
            <p class="package-tag">{{ $packageTag }}</p>
            <h3>{{ $package->name }}</h3>
            <div class="price">Rp {{ number_format((float) $package->price, 0, ',', '.') }}</div>
            <p class="package-copy">{{ $package->description ?: 'Ideal untuk Anda yang ingin hasil dokumentasi rapi, emosional, dan siap dibagikan.' }}</p>
            <ul class="package-list">
              @forelse ($benefits as $benefit)
                <li>{{ $benefit }}</li>
              @empty
                <li>Benefit detail sedang diperbarui, konsultasi cepat tersedia via WhatsApp.</li>
              @endforelse
            </ul>
          </article>
        @empty
          <article class="package-empty">
            <p>{{ $group['empty_message'] }}</p>
          </article>
        @endforelse
      </div>
    </div>
  @endforeach

  <div class="packages-page-actions">
    <a class="cta cta-outline" href="{{ route('home') }}#packages">Kembali ke Landing</a>
    <a class="cta" href="{{ route('booking.page') }}">Lanjut Booking</a>
  </div>
</section>
@endsection
