<?php

namespace App\Services\Admin;

use App\Models\Booking;
use App\Models\Location;
use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Repositories\Contracts\ReferenceRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
class BookingListService
{
    private const STATUS_GROUP = 'booking_status';
    private const CASE_ID_PATTERN = '/^ETH-(\d{8})-(\d{5})$/i';

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
        $baseQuery = $this->buildQuery($resolvedFilters, false);

        $totalCount = (clone $baseQuery)->count();
        $statusOptions = $this->getStatusOptions();
        $statusFilters = $this->buildStatusFilters($statusOptions, $resolvedFilters, $baseQuery, $totalCount);
        $stats = $this->buildStats($baseQuery);

        $filteredQuery = $this->buildQuery($resolvedFilters, true);
        $filteredCount = (clone $filteredQuery)->count();

        $bookings = $filteredQuery
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        $rows = $bookings->map(function (Booking $booking): array {
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
            $statusBadge = $this->resolveStatusBadge($statusCode);
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
            'totalCount' => $totalCount,
            'filteredCount' => $filteredCount,
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
            'date_range' => isset($filters['date_range']) ? trim((string) $filters['date_range']) : 'week',
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
            ['label' => 'Total Booking', 'value' => $total, 'hint' => 'Semua status', 'tone' => 'primary'],
            ['label' => 'Waiting Approval', 'value' => $waitingApproval, 'hint' => 'Belum DP', 'tone' => 'warning'],
            ['label' => 'Active', 'value' => $active, 'hint' => 'DP verified / paid', 'tone' => 'success'],
            ['label' => 'Cancelled/Expired', 'value' => $inactive, 'hint' => 'Tidak aktif', 'tone' => 'danger'],
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
    private function resolveStatusBadge(string $statusCode): array
    {
        $statusCode = strtoupper($statusCode);
        $label = $statusCode !== '' ? strtolower(str_replace('BS_', '', $statusCode)) : 'unknown';

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
            'BS_APPROVED_WAITING_DP' => ['type' => 'badge', 'tone' => 'warning', 'label' => 'dp_waiting_payment'],
            'BS_APPROVED_WAITING_FINAL_PAYMENT' => ['type' => 'badge', 'tone' => 'info', 'label' => 'final_waiting_payment'],
            'BS_CONFIRMED', 'BS_COMPLETE' => ['type' => 'badge', 'tone' => 'success', 'label' => 'paid'],
            'BS_CANCEL', 'BS_EXPIRED', 'BS_EXPIRED_DP', 'BS_REFUND' => ['type' => 'badge', 'tone' => 'danger', 'label' => 'inactive'],
            default => ['type' => 'badge', 'tone' => 'light', 'label' => 'n/a'],
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
