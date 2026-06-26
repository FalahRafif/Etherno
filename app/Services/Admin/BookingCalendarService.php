<?php

namespace App\Services\Admin;

use App\Models\Booking;
use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Repositories\Contracts\ReferenceRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class BookingCalendarService
{
    private const STATUS_GROUP = 'booking_status';

    /**
     * @var array<int, string>
     */
    private const WAITING_STATUS_CODES = [
        'BS_WAITING_APPROVAL',
        'BS_APPROVED_WAITING_DP',
    ];

    /**
     * @var array<int, string>
     */
    private const ACTIVE_STATUS_CODES = [
        'BS_APPROVED_WAITING_FINAL_PAYMENT',
        'BS_CONFIRMED',
        'BS_COMPLETE',
    ];

    /**
     * Status berikut sudah cukup kuat untuk diposisikan pada tanggal acara.
     * Status lain masih ditampilkan pada tanggal pengajuan agar petugas melihat antrian masuknya.
     *
     * @var array<int, string>
     */
    private const EVENT_DATE_STATUS_CODES = [
        'BS_APPROVED_WAITING_FINAL_PAYMENT',
        'BS_CONFIRMED',
        'BS_COMPLETE',
        'BS_FORCE_MAJEURE',
        'BS_RESCHEDULE',
        'BS_REFUND',
    ];

    /**
     * @var array<int, string>
     */
    private const CLOSED_STATUS_CODES = [
        'BS_CANCEL',
        'BS_EXPIRED',
        'BS_EXPIRED_DP',
        'BS_REFUND',
        'BS_RESCHEDULE',
        'BS_FORCE_MAJEURE',
    ];

    public function __construct(
        private BookingRepositoryInterface $bookingRepository,
        private ReferenceRepositoryInterface $referenceRepository
    ) {
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function getPagePayload(array $filters): array
    {
        $resolvedFilters = $this->resolveFilters($filters);

        $baseQuery = $this->bookingRepository
            ->query(true);

        $this->applyCalendarDateRangeFilter($baseQuery, $resolvedFilters['date_start'], $resolvedFilters['date_end']);

        $statusOptions = $this->getStatusOptions();
        $totalCount = (clone $baseQuery)->count();
        $statusFilters = $this->buildStatusFilters($statusOptions, $resolvedFilters, $baseQuery, $totalCount);

        return [
            'filters' => $resolvedFilters,
            'statusFilters' => $statusFilters,
            'scheduleSummary' => $this->buildScheduleSummary($baseQuery, $resolvedFilters),
            'upcomingBookings' => $this->buildUpcomingBookings($baseQuery),
            'agendaReadiness' => $this->buildAgendaReadiness($baseQuery),
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<int, array<string, mixed>>
     */
    public function getCalendarEvents(array $filters): array
    {
        $resolvedFilters = $this->resolveEventFilters($filters);

        $query = $this->bookingRepository
            ->query(true)
            ->with([
                'customer:id,first_name,last_name,phone_number',
                'package:id,name',
                'status:id,code,description',
                'eventSession:id,code,description',
                'location:id,name',
            ])
            ->where(function (Builder $builder): void {
                $builder->whereNotNull('event_date')->orWhereNotNull('created_at');
            });

        $this->applyStatusFilter($query, $resolvedFilters['status']);
        if ($resolvedFilters['view_start'] !== '') {
            $this->applyCalendarDateRangeFilter($query, $resolvedFilters['view_start'], $resolvedFilters['view_end']);
        }

        $this->applyCalendarDateRangeFilter($query, $resolvedFilters['date_start'], $resolvedFilters['date_end']);

        $bookings = $query
            ->orderBy('event_date')
            ->orderBy('id')
            ->limit(1000)
            ->get([
                'id',
                'uuid',
                'customer_id',
                'package_id',
                'status_id',
                'location_id',
                'event_date',
                'event_session',
                'force_majeure_date',
                'created_at',
            ]);

        return $bookings->map(function (Booking $booking): array {
            $caseId = $this->buildCaseId($booking);
            $customerName = trim(implode(' ', array_filter([
                $booking->customer?->first_name,
                $booking->customer?->last_name,
            ])));
            $customerName = $customerName !== '' ? $customerName : 'Customer';

            $statusCode = strtoupper(trim((string) ($booking->status?->code ?? '')));
            $statusLabel = $this->resolveStatusLabel(
                $statusCode,
                (string) ($booking->status?->description ?? '')
            );
            $readiness = $this->resolveBookingReadiness($booking, $statusCode);
            $riskLevel = $this->resolveBookingRiskLevel($booking, $statusCode);
            $color = $this->resolveRiskColor($riskLevel, $statusCode);
            $displayDate = $this->resolveCalendarDisplayDate($booking, $statusCode);
            $dateSource = $this->resolveCalendarDateSource($statusCode);
            $eventSessionLabel = trim((string) ($booking->eventSession?->description ?? '-'));
            $packageName = trim((string) ($booking->package?->name ?? '-'));
            $locationName = trim((string) ($booking->location?->name ?? '-'));

            return [
                'id' => (string) $booking->getKey(),
                'title' => sprintf('%s • %s • %s', $eventSessionLabel !== '' ? $eventSessionLabel : '-', $caseId, $readiness['label']),
                'start' => $displayDate,
                'allDay' => true,
                'backgroundColor' => $dateSource === 'event_date' ? $color : '#f1f5f9',
                'borderColor' => $color,
                'textColor' => $dateSource === 'event_date' ? '#ffffff' : '#334155',
                'classNames' => [$dateSource === 'event_date' ? 'calendar-event-date' : 'calendar-submission-date'],
                'extendedProps' => [
                    'case_id' => $caseId,
                    'status_code' => $statusCode,
                    'status_label' => $statusLabel,
                    'session_label' => $eventSessionLabel !== '' ? $eventSessionLabel : '-',
                    'package_name' => $packageName !== '' ? $packageName : '-',
                    'location_name' => $locationName !== '' ? $locationName : '-',
                    'customer_name' => $customerName,
                    'date_source' => $dateSource,
                    'date_source_label' => $dateSource === 'event_date' ? 'Tanggal acara' : 'Tanggal pengajuan',
                    'status_color' => $color,
                    'event_date_label' => $booking->event_date?->format('d M Y') ?? '-',
                    'display_date_label' => $displayDate !== '' ? Carbon::parse($displayDate)->format('d M Y') : '-',
                    'readiness_label' => $readiness['label'],
                    'readiness_tone' => $readiness['tone'],
                    'risk_level' => $riskLevel,
                    'next_action_label' => $readiness['action_label'],
                    'detail_url' => panel_route('admin.bookings.detail', ['booking' => $caseId]),
                ],
            ];
        })->all();
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, string>
     */
    private function resolveFilters(array $filters): array
    {
        $dateStart = $this->normalizeDateString((string) ($filters['date_start'] ?? ''));
        $dateEnd = $this->normalizeDateString((string) ($filters['date_end'] ?? ''));

        if ($dateStart === '' && $dateEnd === '') {
            $dateStart = Carbon::now()->startOfMonth()->toDateString();
            $dateEnd = Carbon::now()->endOfMonth()->toDateString();
        }

        return [
            'status' => strtoupper(trim((string) ($filters['status'] ?? ''))),
            'date_start' => $dateStart,
            'date_end' => $dateEnd,
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, string>
     */
    private function resolveEventFilters(array $filters): array
    {
        $resolved = $this->resolveFilters($filters);

        return array_merge($resolved, [
            'view_start' => $this->normalizeDateString((string) ($filters['start'] ?? '')),
            'view_end' => $this->normalizeDateString((string) ($filters['end'] ?? '')),
        ]);
    }

    private function applyCustomDateRangeFilter(Builder $query, array $filters): void
    {
        $dateStart = trim((string) ($filters['date_start'] ?? ''));
        $dateEnd = trim((string) ($filters['date_end'] ?? ''));

        if ($dateStart !== '' && $dateEnd !== '') {
            $query->whereBetween('event_date', [$dateStart, $dateEnd]);
            return;
        }

        if ($dateStart !== '') {
            $query->whereDate('event_date', '>=', $dateStart);
        }

        if ($dateEnd !== '') {
            $query->whereDate('event_date', '<=', $dateEnd);
        }
    }

    private function applyCalendarDateRangeFilter(Builder $query, string $dateStart, string $dateEnd): void
    {
        $dateStart = trim($dateStart);
        $dateEnd = trim($dateEnd);

        if ($dateStart === '' && $dateEnd === '') {
            return;
        }

        $query->where(function (Builder $outer) use ($dateStart, $dateEnd): void {
            $outer->where(function (Builder $eventDateQuery) use ($dateStart, $dateEnd): void {
                $eventDateQuery->whereHas('status', function (Builder $statusQuery): void {
                    $statusQuery
                        ->where('group_id', self::STATUS_GROUP)
                        ->whereIn('code', self::EVENT_DATE_STATUS_CODES);
                });

                $this->applyDateColumnRange($eventDateQuery, 'event_date', $dateStart, $dateEnd);
            })->orWhere(function (Builder $createdDateQuery) use ($dateStart, $dateEnd): void {
                $createdDateQuery->whereHas('status', function (Builder $statusQuery): void {
                    $statusQuery
                        ->where('group_id', self::STATUS_GROUP)
                        ->whereNotIn('code', self::EVENT_DATE_STATUS_CODES);
                });

                $this->applyDateColumnRange($createdDateQuery, 'created_at', $dateStart, $dateEnd);
            });
        });
    }

    private function applyDateColumnRange(Builder $query, string $column, string $dateStart, string $dateEnd): void
    {
        if ($dateStart !== '' && $dateEnd !== '') {
            $query->whereBetween($column, [Carbon::parse($dateStart)->startOfDay(), Carbon::parse($dateEnd)->endOfDay()]);
            return;
        }

        if ($dateStart !== '') {
            $query->whereDate($column, '>=', $dateStart);
            return;
        }

        $query->whereDate($column, '<=', $dateEnd);
    }

    private function applyStatusFilter(Builder $query, string $statusCode): void
    {
        $normalizedCode = strtoupper(trim($statusCode));
        if ($normalizedCode === '') {
            return;
        }

        $query->whereHas('status', function (Builder $builder) use ($normalizedCode): void {
            $builder
                ->where('group_id', self::STATUS_GROUP)
                ->where('code', $normalizedCode);
            });
    }

    /**
     * @return array<string, mixed>
     */
    /**
     * @param  array<string, string>  $filters
     * @return array<string, mixed>
     */
    private function buildScheduleSummary(Builder $baseQuery, array $filters): array
    {
        $total = (clone $baseQuery)->count();
        $unpaid = $this->countByStatusCodes($baseQuery, ['BS_APPROVED_WAITING_DP', 'BS_APPROVED_WAITING_FINAL_PAYMENT']);
        $needsAction = $this->countByStatusCodes($baseQuery, ['BS_WAITING_APPROVAL', 'BS_RESCHEDULE', 'BS_FORCE_MAJEURE', 'BS_REFUND']);
        $todayQuery = clone $baseQuery;
        $this->applyCalendarDateRangeFilter($todayQuery, Carbon::today()->toDateString(), Carbon::today()->toDateString());
        $today = $todayQuery->count();
        $currentMonthStart = Carbon::now()->startOfMonth()->toDateString();
        $currentMonthEnd = Carbon::now()->endOfMonth()->toDateString();
        $isCurrentMonth = ($filters['date_start'] ?? '') === $currentMonthStart && ($filters['date_end'] ?? '') === $currentMonthEnd;
        $scopeLabel = $isCurrentMonth ? 'Bulan ini' : 'Sesuai filter';
        $headline = $total > 0
            ? $scopeLabel . ' ada ' . $total . ' booking. ' . $unpaid . ' belum lunas, ' . $needsAction . ' perlu dicek.'
            : $scopeLabel . ' belum ada booking.';

        return [
            'headline' => $headline,
            'subline' => 'Data kalender mengikuti filter status dan tanggal di bawah.',
            'metrics' => [
                ['label' => $isCurrentMonth ? 'Booking Bulan Ini' : 'Booking Terfilter', 'value' => $total, 'tone' => 'primary'],
                ['label' => 'Perlu Dicek', 'value' => $needsAction, 'tone' => $needsAction > 0 ? 'warning' : 'success'],
                ['label' => 'Belum Lunas', 'value' => $unpaid, 'tone' => $unpaid > 0 ? 'danger' : 'success'],
                ['label' => 'Agenda Hari Ini', 'value' => $today, 'tone' => 'info'],
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildAgendaReadiness(Builder $baseQuery): array
    {
        $rows = (clone $baseQuery)
            ->with(['customer:id,first_name,last_name', 'status:id,code,description', 'eventSession:id,description'])
            ->whereDate('event_date', '>=', Carbon::today()->toDateString())
            ->orderBy('event_date')
            ->orderBy('event_session')
            ->limit(8)
            ->get(['id', 'case_id', 'event_date', 'event_session', 'force_majeure_date', 'status_id', 'customer_id', 'created_at']);

        return $rows->map(function (Booking $booking): array {
            $statusCode = strtoupper(trim((string) ($booking->status?->code ?? '')));
            $readiness = $this->resolveBookingReadiness($booking, $statusCode);
            $caseId = $this->buildCaseId($booking);
            $customerName = trim(implode(' ', array_filter([$booking->customer?->first_name, $booking->customer?->last_name])));

            return [
                'case_id' => $caseId,
                'customer' => $customerName !== '' ? $customerName : '-',
                'date' => $booking->event_date?->format('d M Y') ?? '-',
                'session' => trim((string) ($booking->eventSession?->description ?? '-')),
                'readiness_label' => $readiness['label'],
                'tone' => $readiness['tone'],
                'action_label' => $readiness['action_label'],
                'url' => panel_route('admin.bookings.detail', ['booking' => $caseId]),
            ];
        })->values()->all();
    }

    private function resolveCalendarDateSource(string $statusCode): string
    {
        return in_array(strtoupper(trim($statusCode)), self::EVENT_DATE_STATUS_CODES, true) ? 'event_date' : 'created_at';
    }

    private function resolveCalendarDisplayDate(Booking $booking, string $statusCode): string
    {
        $source = $this->resolveCalendarDateSource($statusCode);
        if ($source === 'event_date' && $booking->event_date) {
            return $booking->event_date->toDateString();
        }

        return $booking->created_at?->toDateString() ?? $booking->event_date?->toDateString() ?? '';
    }

    /**
     * @return array<int, array{code:string,label:string}>
     */
    private function getStatusOptions(): array
    {
        $rows = $this->referenceRepository
            ->query(true)
            ->where('group_id', self::STATUS_GROUP)
            ->orderBy('id')
            ->get(['code', 'description']);

        return $rows->map(function ($row): array {
            $code = strtoupper(trim((string) $row->code));
            $label = $this->resolveStatusLabel($code, (string) ($row->description ?? ''));

            return [
                'code' => $code,
                'label' => $label,
            ];
        })->values()->all();
    }

    /**
     * @param  array<int, array{code:string,label:string}>  $statusOptions
     * @param  array<string, string>  $filters
     * @return array<int, array{code:string,label:string,count:int,is_active:bool,tone:string}>
     */
    private function buildStatusFilters(array $statusOptions, array $filters, Builder $baseQuery, int $totalCount): array
    {
        $currentStatus = strtoupper(trim((string) ($filters['status'] ?? '')));
        $filtersOutput = [
            [
                'code' => '',
                'label' => 'Semua Status',
                'count' => $totalCount,
                'is_active' => $currentStatus === '',
                'tone' => 'primary',
            ],
        ];

        foreach ($statusOptions as $status) {
            $count = (clone $baseQuery)
                ->whereHas('status', function (Builder $query) use ($status): void {
                    $query
                        ->where('group_id', self::STATUS_GROUP)
                        ->where('code', $status['code']);
                })
                ->count();

            $filtersOutput[] = [
                'code' => $status['code'],
                'label' => $status['label'],
                'count' => $count,
                'is_active' => $currentStatus === $status['code'],
                'tone' => $this->resolveStatusTone($status['code']),
            ];
        }

        return $filtersOutput;
    }

    /**
     * @return array<int, array{label:string,value:int,hint:string,tone:string}>
     */
    private function buildStats(Builder $baseQuery): array
    {
        $total = (clone $baseQuery)->count();
        $waiting = $this->countByStatusCodes($baseQuery, self::WAITING_STATUS_CODES);
        $active = $this->countByStatusCodes($baseQuery, self::ACTIVE_STATUS_CODES);
        $closed = $this->countByStatusCodes($baseQuery, self::CLOSED_STATUS_CODES);
        $occupiedDays = (clone $baseQuery)->distinct('event_date')->count('event_date');

        return [
            ['label' => 'Total Booking', 'value' => $total, 'hint' => 'Sesuai filter tanggal', 'tone' => 'primary'],
            ['label' => 'Menunggu Proses', 'value' => $waiting, 'hint' => 'Perlu review atau tindak lanjut', 'tone' => 'warning'],
            ['label' => 'Booking Aktif', 'value' => $active, 'hint' => 'Masih berjalan hingga hari acara', 'tone' => 'success'],
            ['label' => 'Hari Terisi', 'value' => $occupiedDays, 'hint' => 'Jumlah tanggal event', 'tone' => 'info'],
            ['label' => 'Selesai / Tidak Aktif', 'value' => $closed, 'hint' => 'Selesai, batal, expired, atau refund', 'tone' => 'danger'],
        ];
    }

    /**
     * @return array<int, array{case_id:string,customer:string,date:string,session:string,status_label:string,tone:string}>
     */
    private function buildUpcomingBookings(Builder $baseQuery): array
    {
        $today = Carbon::today()->toDateString();

        $rows = (clone $baseQuery)
            ->with([
                'customer:id,first_name,last_name',
                'status:id,code,description',
                'eventSession:id,description',
            ])
            ->whereDate('event_date', '>=', $today)
            ->orderBy('event_date')
            ->orderBy('id')
            ->limit(8)
            ->get(['id', 'event_date', 'event_session', 'status_id', 'customer_id', 'created_at']);

        return $rows->map(function (Booking $booking): array {
            $statusCode = strtoupper(trim((string) ($booking->status?->code ?? '')));
            $statusLabel = $this->resolveStatusLabel(
                $statusCode,
                (string) ($booking->status?->description ?? '')
            );
            $customerName = trim(implode(' ', array_filter([
                $booking->customer?->first_name,
                $booking->customer?->last_name,
            ])));

            return [
                'case_id' => $this->buildCaseId($booking),
                'customer' => $customerName !== '' ? $customerName : '-',
                'date' => $booking->event_date?->format('d M Y') ?? '-',
                'session' => trim((string) ($booking->eventSession?->description ?? '-')),
                'status_label' => $statusLabel,
                'tone' => $this->resolveStatusTone($statusCode),
            ];
        })->all();
    }

    /**
     * @param  array<int, string>  $codes
     */
    private function countByStatusCodes(Builder $baseQuery, array $codes): int
    {
        return (clone $baseQuery)
            ->whereHas('status', function (Builder $query) use ($codes): void {
                $query
                    ->where('group_id', self::STATUS_GROUP)
                    ->whereIn('code', $codes);
            })
            ->count();
    }

    private function buildCaseId(Booking $booking): string
    {
        $storedCaseId = trim((string) ($booking->case_id ?? ''));
        if ($storedCaseId !== '') {
            return $storedCaseId;
        }

        $createdAt = $booking->created_at ?? now();
        $id = (int) $booking->getKey();

        return sprintf('ETH-%s-%05d', $createdAt->format('Ymd'), $id);
    }

    private function normalizeDateString(string $rawDate): string
    {
        $normalized = trim($rawDate);
        if ($normalized === '') {
            return '';
        }

        try {
            return Carbon::parse($normalized)->toDateString();
        } catch (\Throwable) {
            return '';
        }
    }

    private function resolveStatusLabel(string $statusCode, string $description): string
    {
        return match ($statusCode) {
            'BS_WAITING_APPROVAL' => 'Menunggu Review',
            'BS_APPROVED_WAITING_DP' => 'Disetujui - Menunggu DP',
            'BS_APPROVED_WAITING_FINAL_PAYMENT' => 'Disetujui - Menunggu Pelunasan',
            'BS_CONFIRMED' => 'Booking Terkonfirmasi',
            'BS_COMPLETE' => 'Booking Selesai',
            'BS_CANCEL' => 'Booking Dibatalkan',
            'BS_EXPIRED' => 'Booking Expired',
            'BS_EXPIRED_DP' => 'Expired - DP Tidak Dibayar',
            'BS_REFUND' => 'Refund Diproses',
            'BS_REJECTED' => 'Pengajuan Ditolak',
            'BS_RESCHEDULE' => 'Menunggu Review Reschedule',
            'BS_FORCE_MAJEURE' => 'Force Majeure',
            default => (trim(preg_replace('/\s*\(.*\)$/', '', trim($description)) ?? '') !== '')
                ? trim((string) preg_replace('/\s*\(.*\)$/', '', trim($description)))
                : '-',
        };
    }

    private function resolveStatusTone(string $statusCode): string
    {
        $normalized = strtoupper(trim($statusCode));

        return match ($normalized) {
            'BS_WAITING_APPROVAL' => 'warning',
            'BS_APPROVED_WAITING_DP', 'BS_APPROVED_WAITING_FINAL_PAYMENT' => 'info',
            'BS_CONFIRMED', 'BS_COMPLETE' => 'success',
            'BS_CANCEL', 'BS_EXPIRED', 'BS_EXPIRED_DP', 'BS_REFUND' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * @return array{label:string,tone:string,action_label:string}
     */
    private function resolveBookingReadiness(Booking $booking, string $statusCode): array
    {
        return match ($statusCode) {
            'BS_CONFIRMED' => ['label' => 'Siap Jalan', 'tone' => 'success', 'action_label' => 'Buka Detail'],
            'BS_APPROVED_WAITING_DP' => ['label' => 'Menunggu DP', 'tone' => 'warning', 'action_label' => 'Cek DP'],
            'BS_APPROVED_WAITING_FINAL_PAYMENT' => ['label' => 'Belum Lunas', 'tone' => 'danger', 'action_label' => 'Tinjau Pelunasan'],
            'BS_WAITING_APPROVAL' => ['label' => 'Butuh Review', 'tone' => 'warning', 'action_label' => 'Review'],
            'BS_RESCHEDULE' => ['label' => 'Review Reschedule', 'tone' => 'warning', 'action_label' => 'Review'],
            'BS_FORCE_MAJEURE' => ['label' => !empty($booking->force_majeure_date) ? 'FM Reschedule' : 'FM Refund', 'tone' => 'danger', 'action_label' => 'Follow-up FM'],
            'BS_REFUND' => ['label' => 'Refund Diproses', 'tone' => 'danger', 'action_label' => 'Cek Refund'],
            'BS_COMPLETE' => ['label' => 'Selesai', 'tone' => 'success', 'action_label' => 'Detail'],
            default => ['label' => 'Perlu Dicek', 'tone' => 'secondary', 'action_label' => 'Detail'],
        };
    }

    private function resolveBookingRiskLevel(Booking $booking, string $statusCode): string
    {
        if (in_array($statusCode, ['BS_FORCE_MAJEURE', 'BS_REFUND'], true)) {
            return 'critical';
        }

        if ($statusCode === 'BS_APPROVED_WAITING_FINAL_PAYMENT' && $booking->event_date && $booking->event_date->lte(Carbon::tomorrow()->endOfDay())) {
            return 'critical';
        }

        if (in_array($statusCode, ['BS_WAITING_APPROVAL', 'BS_RESCHEDULE', 'BS_APPROVED_WAITING_DP', 'BS_APPROVED_WAITING_FINAL_PAYMENT'], true)) {
            return 'high';
        }

        if ($statusCode === 'BS_CONFIRMED') {
            return 'ready';
        }

        return 'normal';
    }

    private function resolveRiskColor(string $riskLevel, string $statusCode): string
    {
        return match ($riskLevel) {
            'critical' => '#e6533c',
            'high' => '#f59e0b',
            'ready' => '#22c55e',
            default => $this->resolveStatusColor($statusCode),
        };
    }

    private function resolveStatusColor(string $statusCode): string
    {
        $normalized = strtoupper(trim($statusCode));

        return match ($normalized) {
            'BS_WAITING_APPROVAL' => '#f59e0b',
            'BS_APPROVED_WAITING_DP', 'BS_APPROVED_WAITING_FINAL_PAYMENT' => '#0ea5e9',
            'BS_CONFIRMED', 'BS_COMPLETE' => '#10b981',
            'BS_CANCEL', 'BS_EXPIRED', 'BS_EXPIRED_DP', 'BS_REFUND' => '#ef4444',
            'BS_RESCHEDULE', 'BS_FORCE_MAJEURE' => '#8b5cf6',
            default => '#64748b',
        };
    }
}
