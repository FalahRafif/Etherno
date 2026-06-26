<?php

namespace App\Services\Admin;

use App\Models\Billing;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Location;
use App\Models\Payment;
use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Repositories\Contracts\ReferenceRepositoryInterface;
use App\Repositories\Contracts\SettingRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class BookingListService
{
    private const STATUS_GROUP = 'booking_status';
    private const CASE_ID_PATTERN = '/^ETH-(\d{8})-(\d{5})$/i';
    private const PER_PAGE = 15;

    public function __construct(
        private BookingRepositoryInterface $bookingRepository,
        private ReferenceRepositoryInterface $referenceRepository,
        private CustomerRepositoryInterface $customerRepository,
        private SettingRepositoryInterface $settingRepository
    ) {
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function getPagePayload(array $filters): array
    {
        $resolvedFilters = $this->resolveFilters($filters);
        $baseQuery = $this->buildQuery($resolvedFilters, false);

        $totalCount = (clone $baseQuery)->count();
        $statusOptions = $this->getStatusOptions();
        $statusFilters = $this->buildStatusFilters($statusOptions, $resolvedFilters, $baseQuery, $totalCount);
        $stats = $this->buildStats($baseQuery);

        $filteredQuery = $this->buildQuery($resolvedFilters, true);
        $filteredCount = (clone $filteredQuery)->count();

        $bookings = $filteredQuery
            ->orderByDesc('created_at')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        $rows = $bookings->getCollection()->map(function (Booking $booking): array {
            $caseId = $this->buildCaseId($booking);
            $customerName = trim(implode(' ', array_filter([
                $booking->customer?->first_name,
                $booking->customer?->last_name,
            ])));
            $customerName = $customerName !== '' ? $customerName : '-';
            $phone = trim((string) ($booking->customer?->phone_number ?? '-'));
            $createdDate = $booking->created_at?->format('Y-m-d') ?? '-';
            $eventDate = $booking->event_date?->format('Y-m-d') ?? (string) ($booking->event_date ?? '-');
            $sessionLabel = trim((string) ($booking->eventSession?->description ?? '-'));
            $packageName = trim((string) ($booking->package?->name ?? '-'));
            $locationName = trim((string) ($booking->location?->name ?? '-'));
            $statusCode = strtoupper(trim((string) ($booking->status?->code ?? '')));
            $statusBadge = $this->resolveStatusBadge($statusCode, $booking);
            $locationDetails = $this->resolveLocationDetails($booking->location);
            $mapsPin = trim((string) ($booking->google_maps_pin ?? ''));
            $mapsUrl = $this->buildMapsUrl($mapsPin);

            return [
                [
                    'type' => 'link',
                    'label' => $caseId,
                    'url' => panel_route('admin.bookings.detail', ['booking' => $caseId]),
                    'class' => 'btn btn-sm btn-outline-primary',
                ],
                [
                    'type' => 'stack',
                    'primary' => $customerName,
                    'secondary' => $phone,
                ],
                $createdDate,
                $eventDate,
                $sessionLabel,
                $packageName,
                [
                    'type' => 'location',
                    'tone' => 'info',
                    'label' => $locationName !== '' ? $locationName : '-',
                    'details' => $locationDetails,
                    'maps_pin' => $mapsPin,
                    'maps_url' => $mapsUrl,
                ],
                $statusBadge,
                [
                    'type' => 'link',
                    'label' => 'Detail',
                    'url' => panel_route('admin.bookings.detail', ['booking' => $caseId]),
                ],
            ];
        })->all();

        return [
            'filters' => $resolvedFilters,
            'stats' => $stats,
            'statusFilters' => $statusFilters,
            'rows' => $rows,
            'pagination' => $bookings,
            'totalCount' => $totalCount,
            'filteredCount' => $filteredCount,
        ];
    }

    public function getDashboardPayload(): array
    {
        $baseQuery = $this->bookingRepository->query(true);
        $actionCenter = $this->buildActionCenter();
        $alertCenter = $this->buildAlertCenter($baseQuery);
        $todayTimeline = $this->buildTodayTimeline();
        $upcomingReadiness = $this->buildUpcomingReadiness();

        return [
            'operationalSummary' => $this->buildOperationalSummary($actionCenter, $alertCenter, $todayTimeline),
            'actionCenter' => $actionCenter,
            'alertCenter' => $alertCenter,
            'todayTimeline' => $todayTimeline,
            'upcomingReadiness' => $upcomingReadiness,
            'columns' => ['Kode', 'Customer', 'Tanggal Acara', 'Status Booking', 'Status Payment', 'Tindak Lanjut'],
            'rows' => $this->buildDashboardQueue(),
            'sideCards' => $this->buildDashboardSideCards($baseQuery),
        ];
    }

    public function getCustomersPayload(array $filters = []): array
    {
        $resolvedFilters = [
            'name' => trim((string) ($filters['name'] ?? '')),
            'phone' => trim((string) ($filters['phone'] ?? '')),
        ];

        $query = $this->customerRepository
            ->query(true)
            ->withCount('bookings')
            ->with(['bookings' => function (Builder $q): void {
                $q->with('status:id,code,description')->orderBy('created_at', 'desc');
            }]);

        if ($resolvedFilters['name'] !== '') {
            $query->where(function (Builder $q) use ($resolvedFilters): void {
                $q->where('first_name', 'LIKE', "%{$resolvedFilters['name']}%")
                    ->orWhere('last_name', 'LIKE', "%{$resolvedFilters['name']}%");
            });
        }

        if ($resolvedFilters['phone'] !== '') {
            $query->where('phone_number', 'LIKE', "%{$resolvedFilters['phone']}%");
        }

        $customers = $query->orderByDesc('created_at')->limit(50)->get();

        $rows = $customers->map(function (Customer $customer): array {
            $name = trim(implode(' ', array_filter([$customer->first_name, $customer->last_name])));
            $latestBooking = $customer->bookings->first();
            $statusCode = strtoupper(trim((string) ($latestBooking?->status?->code ?? '')));
            $statusBadge = $statusCode !== '' ? $this->resolveStatusBadge($statusCode, $latestBooking) : ['type' => 'badge', 'tone' => 'light', 'label' => '-'];
            $latestCaseId = $latestBooking ? $this->buildCaseId($latestBooking) : '';

            return [
                $name !== '' ? $name : '-',
                $customer->phone_number ?? '-',
                $customer->email ?? '-',
                (string) ($customer->bookings_count ?? 0),
                $statusBadge,
                $latestCaseId !== '' ? ['type' => 'link', 'label' => 'Lihat Booking', 'url' => panel_route('admin.bookings.detail', ['booking' => $latestCaseId])] : '-',
            ];
        })->all();

        $sideCards = [
            [
                'title' => 'Customer Tracking',
                'bullets' => [
                    'Identitas utama customer menggunakan nama dan nomor WhatsApp.',
                    'Riwayat booking dipakai untuk follow-up layanan berikutnya.',
                    'Status terakhir membantu admin menentukan prioritas komunikasi.',
                ],
            ],
        ];

        return [
            'filters' => $resolvedFilters,
            'columns' => ['Nama', 'WhatsApp', 'Email', 'Total Booking', 'Status Terakhir', 'Aksi'],
            'rows' => $rows,
            'sideCards' => $sideCards,
        ];
    }

    public function getSettingsPayload(): array
    {
        $settings = $this->settingRepository
            ->query(true)
            ->with('type:id,code,description')
            ->whereIn('group_id', ['paymet_date_rule', 'payment_type_price_percentage', 'package_date_rule'])
            ->orderByRaw("FIELD(group_id, 'paymet_date_rule', 'payment_type_price_percentage', 'package_date_rule')")
            ->orderBy('code')
            ->get();

        $groupLabels = [
            'paymet_date_rule' => 'Aturan Tanggal Pembayaran',
            'payment_type_price_percentage' => 'Persentase DP',
            'package_date_rule' => 'Aturan Paket & Kuota',
        ];

        $rows = $settings->map(function ($setting) use ($groupLabels): array {
            $groupLabel = $groupLabels[$setting->group_id] ?? $setting->group_id;
            $value = $setting->value ?? '-';
            if ($setting->group_id === 'payment_type_price_percentage' && is_numeric($value)) {
                $value .= '%';
            }
            $typeLabel = $setting->type?->description ?? '-';

            return [
                $groupLabel,
                $setting->description ?? $setting->code,
                $value,
                $typeLabel,
                '<code>' . $setting->code . '</code>',
            ];
        })->all();

        $installmentTypes = $this->referenceRepository
            ->query(true)
            ->where('group_id', 'intallment_type')
            ->orderBy('code')
            ->get(['code', 'description']);

        foreach ($installmentTypes as $ref) {
            $rows[] = [
                'Tipe Installment',
                $ref->description ?? $ref->code,
                $ref->code,
                '-',
                '<code>' . $ref->code . '</code>',
            ];
        }

        $sideCards = [
            [
                'title' => 'Kelola Settings',
                'bullets' => [
                    'Aturan tanggal pembayaran mengatur deadline DP dan final payment.',
                    'Persentase DP berbeda per tipe paket (wedding / non-wedding).',
                    'Kuota sesi mengatur jumlah booking maksimal per slot per hari.',
                ],
                'actions' => [
                    ['label' => 'Aturan Tanggal Bayar', 'url' => route('admin.payment-date-rules'), 'class' => 'btn btn-outline-primary btn-sm'],
                    ['label' => 'Persentase DP', 'url' => route('admin.dp-percentage-rules'), 'class' => 'btn btn-outline-primary btn-sm'],
                    ['label' => 'Aturan Paket & Kuota', 'url' => route('admin.package-date-rules'), 'class' => 'btn btn-outline-primary btn-sm'],
                ],
            ],
            [
                'title' => 'Checklist Operasional',
                'items' => [
                    ['label' => 'Template pesan WA approval', 'value' => 'Siap pakai', 'class' => 'text-success'],
                    ['label' => 'Template reminder H-1', 'value' => 'Siap pakai', 'class' => 'text-success'],
                    ['label' => 'Nomor rekening cadangan', 'value' => 'Opsional', 'class' => 'text-muted'],
                    ['label' => 'SLA verifikasi pembayaran', 'value' => 'Maksimal hari yang sama', 'class' => 'text-primary'],
                ],
            ],
        ];

        return [
            'columns' => ['Kategori', 'Item', 'Nilai', 'Tipe Paket', 'Kode'],
            'rows' => $rows,
            'sideCards' => $sideCards,
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    private function resolveFilters(array $filters): array
    {
        return [
            'status' => isset($filters['status']) ? trim((string) $filters['status']) : '',
            'case_id' => isset($filters['case_id']) ? trim((string) $filters['case_id']) : '',
            'date_range' => isset($filters['date_range']) ? trim((string) $filters['date_range']) : 'month',
            'date_start' => isset($filters['date_start']) ? trim((string) $filters['date_start']) : '',
            'date_end' => isset($filters['date_end']) ? trim((string) $filters['date_end']) : '',
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function buildQuery(array $filters, bool $includeStatusFilter): Builder
    {
        $query = $this->bookingRepository
            ->query(true)
            ->with([
                'customer:id,first_name,last_name,phone_number',
                'package:id,name',
                'status:id,code,description',
                'eventSession:id,code,description',
                'location:id,name,parent_id,level_id,wilayah_id',
                'location.parent:id,name,parent_id,level_id,wilayah_id',
                'location.parent.parent:id,name,parent_id,level_id,wilayah_id',
                'location.parent.parent.parent:id,name,parent_id,level_id,wilayah_id',
                'location.level:id,description',
                'location.parent.level:id,description',
                'location.parent.parent.level:id,description',
                'location.parent.parent.parent.level:id,description',
                'location.wilayah:kode,nama',
                'location.parent.wilayah:kode,nama',
                'location.parent.parent.wilayah:kode,nama',
                'location.parent.parent.parent.wilayah:kode,nama',
            ]);

        $this->applyDateRangeFilter($query, $filters);
        $this->applyCaseIdFilter($query, $filters['case_id'] ?? '');

        if ($includeStatusFilter) {
            $status = strtoupper(trim((string) ($filters['status'] ?? '')));
            if ($status !== '') {
                $query->whereHas('status', function (Builder $builder) use ($status): void {
                    $builder
                        ->where('group_id', self::STATUS_GROUP)
                        ->where('code', $status);
                });
            }
        }

        return $query;
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function applyDateRangeFilter(Builder $query, array $filters): void
    {
        $range = strtolower(trim((string) ($filters['date_range'] ?? '')));
        if ($range === '' || $range === 'all') {
            return;
        }
        if ($range === 'week') {
            $start = Carbon::now()->startOfWeek();
            $end = Carbon::now()->endOfWeek();
            $query->whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()]);
            return;
        }

        if ($range === 'month') {
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
            $query->whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()]);
            return;
        }

        if ($range === 'last_month') {
            $start = Carbon::now()->subMonthNoOverflow()->startOfMonth();
            $end = Carbon::now()->subMonthNoOverflow()->endOfMonth();
            $query->whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()]);
            return;
        }

        if ($range === 'last_3_months') {
            $start = Carbon::now()->subMonthsNoOverflow(3)->startOfMonth();
            $end = Carbon::now()->endOfMonth();
            $query->whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()]);
            return;
        }

        if ($range === 'year') {
            $start = Carbon::now()->startOfYear();
            $end = Carbon::now()->endOfYear();
            $query->whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()]);
            return;
        }

        if ($range === 'last_year') {
            $start = Carbon::now()->subYearNoOverflow()->startOfYear();
            $end = Carbon::now()->subYearNoOverflow()->endOfYear();
            $query->whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()]);
            return;
        }

        if ($range === 'custom') {
            $start = trim((string) ($filters['date_start'] ?? ''));
            $end = trim((string) ($filters['date_end'] ?? ''));

            if ($start !== '' && $end !== '') {
                $query->whereBetween('created_at', [Carbon::parse($start)->startOfDay(), Carbon::parse($end)->endOfDay()]);
                return;
            }

            if ($start !== '') {
                $query->whereDate('created_at', '>=', $start);
                return;
            }

            if ($end !== '') {
                $query->whereDate('created_at', '<=', $end);
            }
        }
    }

    private function applyCaseIdFilter(Builder $query, string $caseId): void
    {
        $caseId = strtoupper(trim($caseId));
        if ($caseId === '') {
            return;
        }

        if (preg_match(self::CASE_ID_PATTERN, $caseId, $matches)) {
            $date = Carbon::createFromFormat('Ymd', $matches[1])->toDateString();
            $id = (int) ltrim($matches[2], '0');

            $query->whereDate('created_at', $date)->where('id', $id);
            return;
        }

        if (ctype_digit($caseId)) {
            $query->where('id', (int) $caseId);
            return;
        }

        $query->where('uuid', $caseId);
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
            $description = trim((string) $row->description);
            $label = $description !== '' ? preg_replace('/\s*\(.*\)$/', '', $description) : '';
            $label = trim((string) $label);

            if ($label === '') {
                $label = str_replace('_', ' ', strtolower(str_replace('BS_', '', $code)));
            }

            return [
                'code' => $code,
                'label' => ucwords(strtolower($label)),
            ];
        })->values()->all();
    }

    /**
     * @param  array<int, array{code:string,label:string}>  $statusOptions
     * @return array<int, array{code:string,label:string,count:int,is_active:bool,tone:string}>
     */
    private function buildStatusFilters(array $statusOptions, array $filters, Builder $baseQuery, int $totalCount): array
    {
        $currentStatus = strtoupper(trim((string) ($filters['status'] ?? '')));
        $filtersOutput = [
            [
                'code' => '',
                'label' => 'Semua',
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
        $waitingApproval = $this->countByStatusCodes($baseQuery, ['BS_WAITING_APPROVAL']);
        $active = $this->countByStatusCodes($baseQuery, ['BS_APPROVED_WAITING_FINAL_PAYMENT', 'BS_CONFIRMED', 'BS_COMPLETE']);
        $inactive = $this->countByStatusCodes($baseQuery, ['BS_CANCEL', 'BS_EXPIRED', 'BS_EXPIRED_DP', 'BS_REFUND', 'BS_REJECTED']);

        return [
        ];
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

    /**
     * @param  array<int, array<string, mixed>>  $actionCenter
     * @param  array<int, array<string, mixed>>  $alertCenter
     * @param  array<int, array<string, mixed>>  $todayTimeline
     * @return array<string, mixed>
     */
    private function buildOperationalSummary(array $actionCenter, array $alertCenter, array $todayTimeline): array
    {
        $criticalCount = count(array_filter($actionCenter, static fn (array $item): bool => ($item['severity'] ?? '') === 'critical'));
        $highCount = count(array_filter($actionCenter, static fn (array $item): bool => ($item['severity'] ?? '') === 'high'));
        $taskCount = count($actionCenter);
        $alertCount = count($alertCenter);
        $timelineCount = count($todayTimeline);

        $parts = [];
        if ($criticalCount > 0) {
            $parts[] = $criticalCount . ' harus dicek dulu';
        }
        if ($highCount > 0) {
            $parts[] = $highCount . ' perlu diproses';
        }
        if ($alertCount > 0) {
            $parts[] = $alertCount . ' catatan';
        }
        if ($timelineCount > 0) {
            $parts[] = $timelineCount . ' agenda hari ini';
        }

        $headline = $taskCount > 0
            ? $taskCount . ' booking perlu dicek hari ini.'
            : 'Belum ada booking yang perlu dicek segera.';

        return [
            'eyebrow' => 'Ringkasan Hari Ini',
            'headline' => $headline,
            'subline' => !empty($parts)
                ? implode(', ', $parts) . '.'
                : 'Cek kalender untuk melihat jadwal acara terdekat.',
            'primary_action' => [
                'label' => $taskCount > 0 ? 'Cek Booking' : 'Lihat Kalender',
                'url' => $taskCount > 0 ? '#dashboard_action_center' : panel_route('admin.calendar'),
            ],
            'secondary_actions' => [
                ['label' => 'Booking Menunggu Review', 'url' => panel_route('admin.bookings.list', ['status' => 'BS_WAITING_APPROVAL'])],
                ['label' => 'Kalender & Slot', 'url' => panel_route('admin.calendar')],
            ],
            'metrics' => [
                ['label' => 'Perlu Dicek', 'value' => $taskCount, 'tone' => $taskCount > 0 ? 'warning' : 'success'],
                ['label' => 'Mendesak', 'value' => $criticalCount, 'tone' => $criticalCount > 0 ? 'danger' : 'success'],
                ['label' => 'Catatan', 'value' => $alertCount, 'tone' => $alertCount > 0 ? 'danger' : 'success'],
                ['label' => 'Agenda Hari Ini', 'value' => $timelineCount, 'tone' => 'info'],
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildActionCenter(): array
    {
        $items = [];

        $reviewBookings = $this->dashboardBookingsByStatus(['BS_WAITING_APPROVAL'], 4);
        foreach ($reviewBookings as $booking) {
            $items[] = $this->makeBookingActionItem(
                $booking,
                'high',
                'Review booking baru',
                'Pengajuan baru belum diproses.',
                $this->relativeCreatedLabel($booking),
                'Review Booking',
                'review'
            );
        }

        $paymentBookings = $this->dashboardBookingsByStatus(['BS_APPROVED_WAITING_DP', 'BS_APPROVED_WAITING_FINAL_PAYMENT'], 4);
        foreach ($paymentBookings as $booking) {
            $statusCode = strtoupper(trim((string) ($booking->status?->code ?? '')));
            $isFinal = $statusCode === 'BS_APPROVED_WAITING_FINAL_PAYMENT';
            $items[] = $this->makeBookingActionItem(
                $booking,
                $isFinal && $booking->event_date && $booking->event_date->lte(Carbon::tomorrow()->endOfDay()) ? 'critical' : 'medium',
                $isFinal ? 'Cek pelunasan' : 'Cek pembayaran DP',
                $isFinal ? 'Pelunasan belum selesai.' : 'DP belum dibayar atau belum diverifikasi.',
                $booking->event_date ? 'Acara ' . $booking->event_date->format('d M Y') : 'Tanggal acara belum tersedia',
                $isFinal ? 'Tinjau Pelunasan' : 'Cek DP',
                $isFinal ? 'payment-final' : 'payment-dp'
            );
        }

        $specialBookings = $this->dashboardBookingsByStatus(['BS_RESCHEDULE', 'BS_FORCE_MAJEURE', 'BS_REFUND'], 5);
        foreach ($specialBookings as $booking) {
            $statusCode = strtoupper(trim((string) ($booking->status?->code ?? '')));
            $items[] = $this->makeBookingActionItem(
                $booking,
                in_array($statusCode, ['BS_FORCE_MAJEURE', 'BS_REFUND'], true) ? 'critical' : 'high',
                $this->resolveActionTitle($statusCode, $booking),
                $this->resolveActionReason($statusCode, $booking),
                $booking->event_date ? 'Acara ' . $booking->event_date->format('d M Y') : 'Butuh follow-up',
                $this->resolveActionLabel($statusCode),
                strtolower(str_replace('BS_', '', $statusCode))
            );
        }

        usort($items, function (array $a, array $b): int {
            $rank = ['critical' => 0, 'high' => 1, 'medium' => 2, 'normal' => 3];
            return ($rank[$a['severity']] ?? 9) <=> ($rank[$b['severity']] ?? 9)
                ?: strcmp((string) ($a['sort_date'] ?? ''), (string) ($b['sort_date'] ?? ''));
        });

        return array_slice($items, 0, 10);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildAlertCenter(Builder $baseQuery): array
    {
        $alerts = [];
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        $finalDue = $this->countByStatusCodes(
            (clone $baseQuery)->whereNotNull('event_date')->whereDate('event_date', '<=', $tomorrow->toDateString()),
            ['BS_APPROVED_WAITING_FINAL_PAYMENT']
        );
        if ($finalDue > 0) {
            $alerts[] = [
                'severity' => 'danger',
                'title' => 'Pelunasan H-1/Hari Ini',
                'description' => $finalDue . ' booking belum lunas mendekati tanggal acara.',
                'action_label' => 'Lihat Booking',
                'url' => panel_route('admin.bookings.list', ['status' => 'BS_APPROVED_WAITING_FINAL_PAYMENT']),
            ];
        }

        $pendingPayments = Payment::query()
            ->where('delete_status', false)
            ->whereHas('status', function (Builder $query): void {
                $query->where('group_id', 'payment_status')->where('code', 'PYS_PEDING');
            })
            ->count();
        if ($pendingPayments > 0) {
            $alerts[] = [
                'severity' => 'warning',
                'title' => 'Bukti Pembayaran Pending',
                'description' => $pendingPayments . ' pembayaran menunggu verifikasi.',
                'action_label' => 'Cek Pembayaran',
                'url' => panel_route('admin.bookings.list'),
            ];
        }

        $reviewAging = $this->countByStatusCodes(
            (clone $baseQuery)->where('created_at', '<=', Carbon::now()->subHours(6)),
            ['BS_WAITING_APPROVAL']
        );
        if ($reviewAging > 0) {
            $alerts[] = [
                'severity' => 'warning',
                'title' => 'Review Tertahan',
                'description' => $reviewAging . ' booking menunggu review lebih dari 6 jam.',
                'action_label' => 'Review Sekarang',
                'url' => panel_route('admin.bookings.list', ['status' => 'BS_WAITING_APPROVAL']),
            ];
        }

        $forceMajeure = $this->countByStatusCodes($baseQuery, ['BS_FORCE_MAJEURE']);
        if ($forceMajeure > 0) {
            $alerts[] = [
                'severity' => 'danger',
                'title' => 'Force Majeure Aktif',
                'description' => $forceMajeure . ' booking sedang force majeure.',
                'action_label' => 'Lihat FM',
                'url' => panel_route('admin.bookings.list', ['status' => 'BS_FORCE_MAJEURE']),
            ];
        }

        $todayEvents = $this->countByStatusCodes(
            (clone $baseQuery)->whereDate('event_date', $today->toDateString()),
            ['BS_CONFIRMED', 'BS_APPROVED_WAITING_FINAL_PAYMENT']
        );
        if ($todayEvents > 0) {
            $alerts[] = [
                'severity' => 'info',
                'title' => 'Agenda Acara Hari Ini',
                'description' => $todayEvents . ' booking dijadwalkan hari ini.',
                'action_label' => 'Buka Kalender',
                'url' => panel_route('admin.calendar', ['date_start' => $today->toDateString(), 'date_end' => $today->toDateString()]),
            ];
        }

        return array_slice($alerts, 0, 5);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildTodayTimeline(): array
    {
        $today = Carbon::today();
        $items = [];

        $events = $this->bookingRepository
            ->query(true)
            ->with(['customer:id,first_name,last_name', 'status:id,code,description', 'eventSession:id,description'])
            ->whereDate('event_date', $today->toDateString())
            ->whereHas('status', function (Builder $query): void {
                $query->where('group_id', self::STATUS_GROUP)->whereIn('code', ['BS_CONFIRMED', 'BS_APPROVED_WAITING_FINAL_PAYMENT']);
            })
            ->orderBy('event_session')
            ->limit(6)
            ->get();

        foreach ($events as $booking) {
            $items[] = [
                'tone' => 'success',
                'time_label' => trim((string) ($booking->eventSession?->description ?? 'Hari ini')),
                'title' => 'Acara terjadwal',
                'description' => $this->buildCaseId($booking) . ' - ' . $this->resolveCustomerName($booking),
                'meta' => $this->resolveReadableStatusBadgeLabel(strtoupper(trim((string) ($booking->status?->code ?? ''))), $booking),
                'url' => panel_route('admin.bookings.detail', ['booking' => $this->buildCaseId($booking)]),
            ];
        }

        $reviews = $this->dashboardBookingsByStatus(['BS_WAITING_APPROVAL'], 3);
        foreach ($reviews as $booking) {
            $items[] = [
                'tone' => 'warning',
                'time_label' => $this->relativeCreatedLabel($booking),
                'title' => 'Review booking baru',
                'description' => $this->buildCaseId($booking) . ' - ' . $this->resolveCustomerName($booking),
                'meta' => 'Menunggu keputusan petugas',
                'url' => panel_route('admin.bookings.detail', ['booking' => $this->buildCaseId($booking)]),
            ];
        }

        return array_slice($items, 0, 8);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildUpcomingReadiness(): array
    {
        $rows = $this->bookingRepository
            ->query(true)
            ->with(['customer:id,first_name,last_name', 'status:id,code,description', 'eventSession:id,description'])
            ->whereNotNull('event_date')
            ->whereDate('event_date', '>=', Carbon::today()->toDateString())
            ->whereDate('event_date', '<=', Carbon::today()->addDays(7)->toDateString())
            ->whereHas('status', function (Builder $query): void {
                $query->where('group_id', self::STATUS_GROUP)->whereIn('code', [
                    'BS_APPROVED_WAITING_FINAL_PAYMENT',
                    'BS_CONFIRMED',
                    'BS_RESCHEDULE',
                    'BS_FORCE_MAJEURE',
                ]);
            })
            ->orderBy('event_date')
            ->orderBy('event_session')
            ->limit(8)
            ->get();

        return $rows->map(function (Booking $booking): array {
            $statusCode = strtoupper(trim((string) ($booking->status?->code ?? '')));
            $readiness = $this->resolveReadiness($booking, $statusCode);
            $caseId = $this->buildCaseId($booking);

            return [
                'case_id' => $caseId,
                'customer' => $this->resolveCustomerName($booking),
                'date_label' => $this->relativeEventDateLabel($booking),
                'session' => trim((string) ($booking->eventSession?->description ?? '-')),
                'status_label' => $this->resolveReadableStatusBadgeLabel($statusCode, $booking),
                'readiness_label' => $readiness['label'],
                'tone' => $readiness['tone'],
                'action_label' => $readiness['action_label'],
                'url' => panel_route('admin.bookings.detail', ['booking' => $caseId]),
            ];
        })->values()->all();
    }

    private function buildDashboardStats(Builder $baseQuery): array
    {
        $waitingApproval = $this->countByStatusCodes($baseQuery, ['BS_WAITING_APPROVAL']);
        $waitingDp = $this->countByStatusCodes($baseQuery, ['BS_APPROVED_WAITING_DP']);
        $active = $this->countByStatusCodes($baseQuery, ['BS_APPROVED_WAITING_FINAL_PAYMENT', 'BS_CONFIRMED']);
        $dueSoon = (clone $baseQuery)
            ->whereHas('status', function (Builder $q): void {
                $q->where('group_id', self::STATUS_GROUP)->where('code', 'BS_APPROVED_WAITING_FINAL_PAYMENT');
            })
            ->whereNotNull('event_date')
            ->where('event_date', '<=', Carbon::now()->addDay()->endOfDay())
            ->count();
        $completedThisMonth = (clone $baseQuery)
            ->whereHas('status', function (Builder $q): void {
                $q->where('group_id', self::STATUS_GROUP)->where('code', 'BS_COMPLETE');
            })
            ->whereBetween('updated_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->count();
        $revenue = Billing::query()
            ->where('delete_status', false)
            ->whereHas('booking', function (Builder $q): void {
                $q->where('delete_status', false);
            })
            ->whereBetween('updated_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->sum('total_paid');

        return [
            ['label' => 'Request Baru', 'value' => (string) $waitingApproval, 'hint' => 'Menunggu review', 'tone' => 'warning'],
            ['label' => 'Menunggu Pembayaran DP', 'value' => (string) $waitingDp, 'hint' => 'Belum mengunci slot acara', 'tone' => 'info'],
            ['label' => 'Booking Aktif', 'value' => (string) $active, 'hint' => 'DP terverifikasi atau booking sudah confirmed', 'tone' => 'success'],
            ['label' => 'Pelunasan Jatuh Tempo', 'value' => (string) $dueSoon, 'hint' => 'H-1 atau lewat, perlu follow-up', 'tone' => 'danger'],
            ['label' => 'Selesai Bulan Ini', 'value' => (string) $completedThisMonth, 'hint' => 'Acara yang sudah dilaksanakan', 'tone' => 'primary'],
            ['label' => 'Pendapatan Bulan Ini', 'value' => 'Rp ' . number_format((float) $revenue, 0, ',', '.'), 'hint' => 'Total pembayaran yang sudah masuk', 'tone' => 'secondary'],
        ];
    }

    /**
     * @param  array<int, string>  $statusCodes
     */
    private function dashboardBookingsByStatus(array $statusCodes, int $limit)
    {
        return $this->bookingRepository
            ->query(true)
            ->with(['customer:id,first_name,last_name', 'status:id,code,description', 'eventSession:id,description'])
            ->whereHas('status', function (Builder $query) use ($statusCodes): void {
                $query->where('group_id', self::STATUS_GROUP)->whereIn('code', $statusCodes);
            })
            ->orderBy('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * @return array<string, mixed>
     */
    private function makeBookingActionItem(Booking $booking, string $severity, string $title, string $reason, string $dueLabel, string $actionLabel, string $type): array
    {
        $caseId = $this->buildCaseId($booking);
        $statusCode = strtoupper(trim((string) ($booking->status?->code ?? '')));

        return [
            'type' => $type,
            'severity' => $severity,
            'severity_label' => $this->resolveSeverityLabel($severity),
            'case_id' => $caseId,
            'customer' => $this->resolveCustomerName($booking),
            'title' => $title,
            'reason' => $reason,
            'due_label' => $dueLabel,
            'status_label' => $this->resolveReadableStatusBadgeLabel($statusCode, $booking),
            'action_label' => $actionLabel,
            'detail_url' => panel_route('admin.bookings.detail', ['booking' => $caseId]),
            'list_url' => $statusCode !== '' ? panel_route('admin.bookings.list', ['status' => $statusCode]) : panel_route('admin.bookings.list'),
            'sort_date' => $booking->event_date?->toDateString() ?? $booking->created_at?->toDateTimeString() ?? '',
        ];
    }

    private function resolveCustomerName(Booking $booking): string
    {
        $customerName = trim(implode(' ', array_filter([
            $booking->customer?->first_name,
            $booking->customer?->last_name,
        ])));

        return $customerName !== '' ? $customerName : '-';
    }

    private function resolveSeverityLabel(string $severity): string
    {
        return match ($severity) {
            'critical' => 'Mendesak',
            'high' => 'Perlu Dicek',
            'medium' => 'Segera',
            default => 'Normal',
        };
    }

    private function resolveActionTitle(string $statusCode, Booking $booking): string
    {
        return match ($statusCode) {
            'BS_RESCHEDULE' => 'Review reschedule',
            'BS_FORCE_MAJEURE' => !empty($booking->force_majeure_date) ? 'Follow-up force majeure reschedule' : 'Follow-up force majeure refund',
            'BS_REFUND' => 'Pantau proses refund',
            default => 'Tinjau booking',
        };
    }

    private function resolveActionReason(string $statusCode, Booking $booking): string
    {
        return match ($statusCode) {
            'BS_RESCHEDULE' => 'Ada permintaan perubahan tanggal.',
            'BS_FORCE_MAJEURE' => !empty($booking->force_majeure_date)
                ? 'Ada usulan tanggal baru karena force majeure.'
                : 'Ada pengajuan refund karena force majeure.',
            'BS_REFUND' => 'Refund sedang diproses.',
            default => 'Booking memerlukan tindak lanjut.',
        };
    }

    private function resolveActionLabel(string $statusCode): string
    {
        return match ($statusCode) {
            'BS_RESCHEDULE' => 'Review Reschedule',
            'BS_FORCE_MAJEURE' => 'Follow-up FM',
            'BS_REFUND' => 'Cek Refund',
            default => 'Buka Detail',
        };
    }

    private function relativeCreatedLabel(Booking $booking): string
    {
        if (!$booking->created_at) {
            return 'Baru masuk';
        }

        if ($booking->created_at->isToday()) {
            return 'Masuk ' . $booking->created_at->diffForHumans();
        }

        return 'Masuk ' . $booking->created_at->format('d M Y');
    }

    private function relativeEventDateLabel(Booking $booking): string
    {
        if (!$booking->event_date) {
            return 'Tanggal belum tersedia';
        }

        if ($booking->event_date->isToday()) {
            return 'Hari ini';
        }

        if ($booking->event_date->isTomorrow()) {
            return 'Besok';
        }

        return $booking->event_date->format('d M Y');
    }

    /**
     * @return array{label:string,tone:string,action_label:string}
     */
    private function resolveReadiness(Booking $booking, string $statusCode): array
    {
        return match ($statusCode) {
            'BS_CONFIRMED' => ['label' => 'Siap Jalan', 'tone' => 'success', 'action_label' => 'Detail'],
            'BS_APPROVED_WAITING_FINAL_PAYMENT' => ['label' => 'Belum Lunas', 'tone' => 'danger', 'action_label' => 'Tinjau Pelunasan'],
            'BS_RESCHEDULE' => ['label' => 'Butuh Review', 'tone' => 'warning', 'action_label' => 'Review'],
            'BS_FORCE_MAJEURE' => ['label' => !empty($booking->force_majeure_date) ? 'FM Reschedule' : 'FM Refund', 'tone' => 'danger', 'action_label' => 'Follow-up'],
            default => ['label' => 'Perlu Dicek', 'tone' => 'secondary', 'action_label' => 'Detail'],
        };
    }

    private function buildDashboardQueue(): array
    {
        $actionStatuses = ['BS_WAITING_APPROVAL', 'BS_APPROVED_WAITING_DP', 'BS_APPROVED_WAITING_FINAL_PAYMENT'];

        $bookings = $this->bookingRepository
            ->query(true)
            ->with(['customer:id,first_name,last_name', 'status:id,code,description'])
            ->whereHas('status', function (Builder $q) use ($actionStatuses): void {
                $q->where('group_id', self::STATUS_GROUP)->whereIn('code', $actionStatuses);
            })
            ->orderBy('created_at')
            ->limit(10)
            ->get();

        return $bookings->map(function (Booking $booking): array {
            $caseId = $this->buildCaseId($booking);
            $customerName = trim(implode(' ', array_filter([
                $booking->customer?->first_name,
                $booking->customer?->last_name,
            ])));
            $statusCode = strtoupper(trim((string) ($booking->status?->code ?? '')));
            $actionLabel = match ($statusCode) {
                'BS_WAITING_APPROVAL' => 'Review Booking',
                'BS_APPROVED_WAITING_DP' => 'Verifikasi DP',
                'BS_APPROVED_WAITING_FINAL_PAYMENT' => 'Tinjau Pelunasan',
                default => 'Detail',
            };

            return [
                [
                    'type' => 'link',
                    'label' => $caseId,
                    'url' => panel_route('admin.bookings.detail', ['booking' => $caseId]),
                    'class' => 'btn btn-sm btn-outline-primary',
                ],
                $customerName !== '' ? $customerName : '-',
                $booking->event_date?->format('Y-m-d') ?? '-',
                $this->resolveStatusBadge($statusCode, $booking),
                $this->resolvePaymentBadge($statusCode),
                ['type' => 'link', 'label' => $actionLabel, 'url' => panel_route('admin.bookings.detail', ['booking' => $caseId])],
            ];
        })->all();
    }

    private function buildDashboardSideCards(Builder $baseQuery): array
    {
        $upcomingBookings = $this->bookingRepository
            ->query(true)
            ->with(['customer:id,first_name,last_name', 'status:id,code,description'])
            ->whereHas('status', function (Builder $q): void {
                $q->where('group_id', self::STATUS_GROUP)->where('code', 'BS_CONFIRMED');
            })
            ->whereNotNull('event_date')
            ->whereBetween('event_date', [Carbon::now()->startOfDay(), Carbon::now()->addDays(7)->endOfDay()])
            ->orderBy('event_date')
            ->limit(5)
            ->get();

        $upcomingItems = $upcomingBookings->map(function (Booking $booking): array {
            $customerName = trim(implode(' ', array_filter([
                $booking->customer?->first_name,
                $booking->customer?->last_name,
            ])));

            return [
                'label' => $this->buildCaseId($booking) . ' — ' . ($customerName !== '' ? $customerName : '-'),
                'value' => $booking->event_date?->format('d M Y') ?? '-',
            ];
        })->values()->all();

        $cancelled = $this->countByStatusCodes($baseQuery, ['BS_CANCEL', 'BS_EXPIRED', 'BS_EXPIRED_DP', 'BS_REFUND', 'BS_REJECTED']);

        return [
            [
                'title' => 'Acara 7 Hari ke Depan',
                'items' => !empty($upcomingItems) ? $upcomingItems : [['label' => 'Tidak ada acara terjadwal', 'value' => '']],
            ],
            [
                'title' => 'Catatan Operasional',
                'bullets' => [
                    'Verifikasi DP maksimal di hari yang sama untuk mempercepat locking slot.',
                    'Final payment harus selesai maksimal H-1 acara.',
                    'Semua koordinasi lanjutan tetap dipusatkan melalui WhatsApp.',
                ],
                'items' => [
                    ['label' => 'Batal / Expired / Refund', 'value' => (string) $cancelled],
                ],
            ],
        ];
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

    /**
     * @return array{type:string,tone:string,label:string}
     */
    private function resolveStatusBadge(string $statusCode, ?Booking $booking = null): array
    {
        $statusCode = strtoupper($statusCode);
        $label = $this->resolveReadableStatusBadgeLabel($statusCode, $booking);

        $tone = match ($statusCode) {
            'BS_WAITING_APPROVAL' => 'warning',
            'BS_APPROVED_WAITING_DP', 'BS_APPROVED_WAITING_FINAL_PAYMENT' => 'info',
            'BS_CONFIRMED', 'BS_COMPLETE' => 'success',
            'BS_CANCEL', 'BS_EXPIRED', 'BS_EXPIRED_DP', 'BS_REFUND' => 'danger',
            default => 'light',
        };

        return [
            'type' => 'badge',
            'tone' => $tone,
            'label' => $label,
        ];
    }

    /**
     * @return array{type:string,tone:string,label:string}
     */
    private function resolvePaymentBadge(string $statusCode): array
    {
        $statusCode = strtoupper($statusCode);

        return match ($statusCode) {
            'BS_APPROVED_WAITING_DP' => ['type' => 'badge', 'tone' => 'warning', 'label' => 'Menunggu Pembayaran DP'],
            'BS_APPROVED_WAITING_FINAL_PAYMENT' => ['type' => 'badge', 'tone' => 'info', 'label' => 'Menunggu Pelunasan'],
            'BS_CONFIRMED' => ['type' => 'badge', 'tone' => 'success', 'label' => 'Lunas & Terkonfirmasi'],
            'BS_COMPLETE' => ['type' => 'badge', 'tone' => 'success', 'label' => 'Booking Selesai'],
            'BS_CANCEL', 'BS_EXPIRED', 'BS_EXPIRED_DP', 'BS_REFUND', 'BS_REJECTED' => ['type' => 'badge', 'tone' => 'danger', 'label' => 'Tidak Aktif'],
            default => ['type' => 'badge', 'tone' => 'light', 'label' => 'Belum Tersedia'],
        };
    }

    private function resolveReadableStatusBadgeLabel(string $statusCode, ?Booking $booking = null): string
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
            'BS_FORCE_MAJEURE' => $booking instanceof Booking
                ? (!empty($booking->force_majeure_date) ? 'Force Majeure - Usulan Reschedule' : 'Force Majeure - Usulan Refund')
                : 'Force Majeure',
            default => 'Status Booking',
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
     * @return array{provinsi:string,kota:string,kecamatan:string,kelurahan:string}
     */
    private function resolveLocationDetails(?Location $location): array
    {
        $details = [
            'provinsi' => '-',
            'kota' => '-',
            'kecamatan' => '-',
            'kelurahan' => '-',
        ];

        if (!$location) {
            return $details;
        }

        $fallbackNames = [];
        $node = $location;
        $loop = 0;

        while ($node && $loop < 5) {
            $name = trim((string) ($node->name ?? ''));
            $levelLabel = strtolower(trim((string) ($node->level?->description ?? '')));
            $mappedKey = $this->mapLocationLevel($levelLabel);

            if ($mappedKey !== '' && $name !== '') {
                $details[$mappedKey] = $name;
            } elseif ($name !== '') {
                $fallbackNames[] = $name;
            }

            $node = $node->parent;
            $loop++;
        }

        if ($details['provinsi'] === '-' && !empty($location->wilayah?->nama)) {
            $details['provinsi'] = (string) $location->wilayah->nama;
        }

        $fallbackKeys = ['kelurahan', 'kecamatan', 'kota', 'provinsi'];
        foreach ($fallbackKeys as $index => $key) {
            if ($details[$key] === '-' && isset($fallbackNames[$index])) {
                $details[$key] = $fallbackNames[$index];
            }
        }

        return $details;
    }

    private function mapLocationLevel(string $label): string
    {
        if ($label === '') {
            return '';
        }

        if (str_contains($label, 'prov')) {
            return 'provinsi';
        }

        if (str_contains($label, 'kota') || str_contains($label, 'kab')) {
            return 'kota';
        }

        if (str_contains($label, 'kecamatan') || str_contains($label, 'kec')) {
            return 'kecamatan';
        }

        if (str_contains($label, 'kelurahan') || str_contains($label, 'kel') || str_contains($label, 'desa')) {
            return 'kelurahan';
        }

        return '';
    }

    private function buildMapsUrl(string $mapsPin): string
    {
        $mapsPin = trim($mapsPin);
        if ($mapsPin === '') {
            return '';
        }

        if (preg_match('/^https?:\/\//i', $mapsPin) === 1) {
            return $mapsPin;
        }

        return 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($mapsPin);
    }
}
