@php
    $columns = $columns ?? [];
    $rows = $rows ?? [];
    $emptyMessage = $emptyMessage ?? 'Belum ada data.';
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

<div class="card custom-card mb-0">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">{{ $tableTitle }}</h5>
        @if(!empty($tableBadge))
            <span class="badge rounded-pill bg-primary-transparent text-primary">{{ $tableBadge }}</span>
        @endif
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
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
                                <td>
                                    @if(is_array($cell) && ($cell['type'] ?? null) === 'badge')
                                        @php
                                            $tone = $cell['tone'] ?? 'secondary';
                                            $toneClass = $toneClasses[$tone] ?? $toneClasses['secondary'];
                                        @endphp
                                        <span class="badge rounded-pill {{ $toneClass }}">{{ $cell['label'] }}</span>
                                    @elseif(is_array($cell) && ($cell['type'] ?? null) === 'link')
                                        <a href="{{ $cell['url'] }}" class="{{ $cell['class'] ?? 'btn btn-sm btn-light' }}">
                                            {{ $cell['label'] }}
                                        </a>
                                    @elseif(is_array($cell) && ($cell['type'] ?? null) === 'stack')
                                        <div class="d-flex flex-column">
                                            <span>{{ $cell['primary'] }}</span>
                                            @if(!empty($cell['secondary']))
                                                <small class="text-muted">{{ $cell['secondary'] }}</small>
                                            @endif
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
    </div>
</div>
