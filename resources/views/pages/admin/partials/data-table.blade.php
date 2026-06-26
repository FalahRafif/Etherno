@php
    $columns = $columns ?? [];
    $rows = $rows ?? [];
    $emptyMessage = $emptyMessage ?? 'Belum ada data.';
    $pagination = $pagination ?? null;
    $toneClasses = [
        'primary' => 'bg-primary-transparent text-primary',
        'secondary' => 'bg-secondary-transparent text-secondary',
        'success' => 'bg-success-transparent text-success',
        'warning' => 'bg-warning-transparent text-warning',
        'danger' => 'bg-danger-transparent text-danger',
        'info' => 'bg-info-transparent text-info',
        'light' => 'bg-light text-dark',
    ];
@endphp

<div class="card custom-card mb-5">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">{{ $tableTitle }}</h5>
        @if(!empty($tableBadge))
            <span class="badge rounded-pill bg-primary-transparent text-primary">{{ $tableBadge }}</span>
        @endif
    </div>
    <div class="card-body p-0">
        <div class="table-responsive" style="overflow-x: scroll;">
            <table class="table table-hover text-nowrap align-middle mb-0">
                <thead>
                    <tr>
                        @foreach($columns as $column)
                            <th>{{ $column }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        <tr>
                            @foreach($row as $cell)
                                @php
                                    $tdClass = '';
                                    $tdStyle = '';
                                    if (is_array($cell) && ($cell['type'] ?? null) === 'location') {
                                        $tdClass = 'text-wrap align-top';
                                        $tdStyle = 'white-space: normal; min-width: 240px;';
                                    }
                                @endphp
                                <td class="{{ $tdClass }}" style="{{ $tdStyle }}">
                                    @if(is_array($cell) && ($cell['type'] ?? null) === 'badge')
                                        @php
                                            $tone = $cell['tone'] ?? 'secondary';
                                            $toneClass = $toneClasses[$tone] ?? $toneClasses['secondary'];
                                        @endphp
                                        <span class="badge rounded-pill {{ $toneClass }}">{{ $cell['label'] }}</span>
                                    @elseif(is_array($cell) && ($cell['type'] ?? null) === 'location')
                                        @php
                                            $tone = $cell['tone'] ?? 'secondary';
                                            $toneClass = $toneClasses[$tone] ?? $toneClasses['secondary'];
                                            $details = $cell['details'] ?? [];
                                            $mapsPin = trim((string) ($cell['maps_pin'] ?? ''));
                                            $mapsUrl = trim((string) ($cell['maps_url'] ?? ''));
                                        @endphp
                                        <details class="location-detail">
                                            <summary class="badge rounded-pill {{ $toneClass }}" style="cursor: pointer;">
                                                {{ $cell['label'] }}
                                            </summary>
                                            <div class="border rounded p-3 mt-2">
                                                <div class="row g-2">
                                                    <div class="col-12 col-md-6">
                                                        <p class="text-muted small mb-1">Provinsi</p>
                                                        <p class="mb-2 fw-semibold">{{ $details['provinsi'] ?? '-' }}</p>
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <p class="text-muted small mb-1">Kota/Kab</p>
                                                        <p class="mb-2 fw-semibold">{{ $details['kota'] ?? '-' }}</p>
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <p class="text-muted small mb-1">Kecamatan</p>
                                                        <p class="mb-2 fw-semibold">{{ $details['kecamatan'] ?? '-' }}</p>
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <p class="text-muted small mb-1">Kelurahan</p>
                                                        <p class="mb-2 fw-semibold">{{ $details['kelurahan'] ?? '-' }}</p>
                                                    </div>
                                                </div>
                                                <div class="mt-2">
                                                    <p class="text-muted small mb-1">Pin Maps</p>
                                                    <p class="mb-2">{{ $mapsPin !== '' ? $mapsPin : '-' }}</p>
                                                    @if($mapsUrl !== '')
                                                        <a href="{{ $mapsUrl }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary">
                                                            Buka Google Maps
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </details>
                                    @elseif(is_array($cell) && ($cell['type'] ?? null) === 'link')
                                        <a href="{{ $cell['url'] }}" class="{{ $cell['class'] ?? 'btn btn-sm btn-light' }}">
                                            {{ $cell['label'] }}
                                        </a>
                                    @elseif(is_array($cell) && ($cell['type'] ?? null) === 'stack')
                                        <div class="d-flex flex-column">
                                            <span>{{ $cell['primary'] }}</span>
                                            @if(!empty($cell['secondary']))
                                                <small class="text-muted">{{ $cell['secondary'] }}</small>
                                            @endif>
                                        </div>
                                    @elseif(is_array($cell) && ($cell['type'] ?? null) === 'text')
                                        <span class="{{ $cell['class'] ?? '' }}">{{ $cell['label'] }}</span>
                                    @else
                                        {{ $cell }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ max(count($columns), 1) }}" class="text-center text-muted py-4">
                                {{ $emptyMessage }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pagination instanceof \Illuminate\Pagination\LengthAwarePaginator && $pagination->hasPages())
            <div class="px-4 py-3 border-top d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 booking-table-pagination-wrap">
                <p class="text-muted small mb-0">
                    Menampilkan {{ $pagination->firstItem() ?? 0 }}-{{ $pagination->lastItem() ?? 0 }} dari {{ $pagination->total() }} data
                </p>
                <nav aria-label="Navigasi halaman booking">
                    <ul class="booking-table-pagination mb-0">
                        @if ($pagination->onFirstPage())
                            <li class="is-disabled" aria-disabled="true" aria-label="Halaman sebelumnya">
                                <span>Sebelumnya</span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $pagination->previousPageUrl() }}" rel="prev" aria-label="Halaman sebelumnya">Sebelumnya</a>
                            </li>
                        @endif

                        @php
                            $startPage = max(1, $pagination->currentPage() - 1);
                            $endPage = min($pagination->lastPage(), $pagination->currentPage() + 1);
                        @endphp

                        @if ($startPage > 1)
                            <li><a href="{{ $pagination->url(1) }}">1</a></li>
                            @if ($startPage > 2)
                                <li class="is-disabled" aria-disabled="true"><span>...</span></li>
                            @endif
                        @endif

                        @for ($page = $startPage; $page <= $endPage; $page++)
                            @if ($page === $pagination->currentPage())
                                <li class="is-active" aria-current="page"><span>{{ $page }}</span></li>
                            @else
                                <li><a href="{{ $pagination->url($page) }}">{{ $page }}</a></li>
                            @endif
                        @endfor

                        @if ($endPage < $pagination->lastPage())
                            @if ($endPage < $pagination->lastPage() - 1)
                                <li class="is-disabled" aria-disabled="true"><span>...</span></li>
                            @endif
                            <li><a href="{{ $pagination->url($pagination->lastPage()) }}">{{ $pagination->lastPage() }}</a></li>
                        @endif

                        @if ($pagination->hasMorePages())
                            <li>
                                <a href="{{ $pagination->nextPageUrl() }}" rel="next" aria-label="Halaman berikutnya">Berikutnya</a>
                            </li>
                        @else
                            <li class="is-disabled" aria-disabled="true" aria-label="Halaman berikutnya">
                                <span>Berikutnya</span>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
        @endif
    </div>
</div>
