<?php

namespace App\Services\Portal;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Location;
use App\Models\Package;
use App\Models\Reference;
use App\Repositories\Contracts\BookingHistoryRepositoryInterface;
use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Repositories\Contracts\LocationPricingRuleRepositoryInterface;
use App\Repositories\Contracts\LocationRepositoryInterface;
use App\Repositories\Contracts\PackageRepositoryInterface;
use App\Repositories\Contracts\ReferenceRepositoryInterface;
use App\Repositories\Contracts\SettingRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class GuestBookingService
{
    private const INITIAL_BOOKING_STATUS_CODE = 'BS_WAITING_APPROVAL';

    private const ACTIVE_PACKAGE_STATUS_CODE = 'PS_ACTIVE';

    private const PACKAGE_TYPE_WEDDING_CODE = 'PKT_WEDDING';

    private const PACKAGE_TYPE_NON_WEDDING_CODE = 'PKT_NON_WEDDING';

    private const LOCATION_LEVEL_PROVINCE_CODE = 'LL_PV';

    private const LOCATION_LEVEL_CITY_CODE = 'LL_CT';

    private const LOCATION_LEVEL_DISTRICT_CODE = 'LL_KC';

    private const LOCATION_LEVEL_VILLAGE_CODE = 'LL_KL';

    private const EVENT_SESSION_MORNING_CODE = 'ES_PAGI_SIANG';

    private const EVENT_SESSION_EVENING_CODE = 'ES_SORE_MALAM';

    private const QUOTA_SETTING_MORNING_CODE = 'PKDR_MAX_QUOTA_PAGI_SIANG';

    private const QUOTA_SETTING_EVENING_CODE = 'PKDR_MAX_QUOTA_SORE_MALAM';

    private const PAYMENT_DATE_RULE_GROUP_ID = 'paymet_date_rule';

    private const PAYMENT_DATE_RULE_DP_CODE = 'PDR_DP';

    private const PAYMENT_DATE_RULE_FINAL_CODE = 'PDR_MAX_FINAL';

    private const DP_PERCENTAGE_GROUP_ID = 'payment_type_price_percentage';

    /**
     * @var array<int, string>
     */
    private const NON_QUOTA_BOOKING_STATUS_CODES = [
        'BS_EXPIRED',
        'BS_EXPIRED_DP',
        'BS_CANCEL',
        'BS_RESCHEDULE',
        'BS_FORCE_MAJEURE',
        'BS_REFUND',
    ];

    /**
     * @var array{
     *     ids: array<int, int>,
     *     labels_by_code: array<string, string>,
     *     ids_by_code: array<string, int>
     * }|null
     */
    private ?array $locationLevelMeta = null;

    /**
     * @var array{
     *     ids_by_code: array<string, int>,
     *     labels_by_code: array<string, string>
     * }|null
     */
    private ?array $eventSessionMeta = null;

    /**
     * @var array<string, int>|null
     */
    private ?array $quotaMaxByCode = null;

    public function __construct(
        private PackageRepositoryInterface $packageRepository,
        private ReferenceRepositoryInterface $referenceRepository,
        private SettingRepositoryInterface $settingRepository,
        private LocationRepositoryInterface $locationRepository,
        private LocationPricingRuleRepositoryInterface $locationPricingRuleRepository,
        private CustomerRepositoryInterface $customerRepository,
        private BookingRepositoryInterface $bookingRepository,
        private BookingHistoryRepositoryInterface $bookingHistoryRepository
    ) {
    }

    /**
     * @return array{
     *   packageTypeOptions: Collection<int, Reference>,
     *   packageOptions: Collection<int, Package>,
     *   eventSessionOptions: Collection<int, Reference>,
     *   provinceOptions: Collection<int, Location>
     * }
     */
    public function getFormPayload(): array
    {
        return [
            'packageTypeOptions' => $this->resolvePackageTypeOptions(),
            'packageOptions' => $this->resolveActivePackageOptions(),
            'eventSessionOptions' => $this->resolveEventSessionOptions(),
            'provinceOptions' => $this->resolveProvinceOptions(),
        ];
    }

    public function createBookingRequest(array $payload): Booking
    {
        return DB::transaction(function () use ($payload): Booking {
            $initialStatus = $this->resolveReferenceByGroupAndCode(
                'booking_status',
                self::INITIAL_BOOKING_STATUS_CODE,
                'Status awal booking tidak ditemukan.'
            );

            $selectedPackageType = $this->resolveReferenceByIdAndGroup(
                (int) $payload['package_type_id'],
                'package_type',
                'Tipe paket yang dipilih tidak valid.'
            );

            $selectedPackage = $this->resolveActivePackageOrFail((int) $payload['package_id']);
            if ((int) $selectedPackage->package_type !== (int) $selectedPackageType->getKey()) {
                throw new RuntimeException('Paket tidak sesuai dengan tipe paket yang dipilih.');
            }
            $selectedPackage = $this->ensurePackageCaseId($selectedPackage);

            $selectedVillage = $this->resolveLocationHierarchyOrFail(
                (int) $payload['location_province_id'],
                (int) $payload['location_city_id'],
                (int) $payload['location_district_id'],
                (int) $payload['location_village_id'],
                (int) $payload['location_id']
            );

            $selectedSession = $this->resolveReferenceByIdAndGroup(
                (int) $payload['event_session_id'],
                'event_session',
                'Sesi acara yang dipilih tidak valid.'
            );
            $this->assertSessionQuotaAvailable((string) $payload['event_date'], (int) $selectedSession->getKey());

            [$firstName, $lastName] = $this->splitCustomerName((string) $payload['name']);

            /** @var Customer $customer */
            $customer = $this->customerRepository->create([
                'uuid' => (string) Str::uuid(),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone_number' => (string) $payload['phone_number'],
            ]);

            /** @var Booking $booking */
            $booking = $this->bookingRepository->create([
                'uuid' => (string) Str::uuid(),
                'customer_id' => (int) $customer->getKey(),
                'package_id' => (int) $selectedPackage->getKey(),
                'status_id' => (int) $initialStatus->getKey(),
                'location_id' => (int) $selectedVillage->getKey(),
                'event_date' => (string) $payload['event_date'],
                'event_session' => (int) $selectedSession->getKey(),
                'event_detail' => $payload['event_detail'] ?? null,
                'google_maps_pin' => (string) $payload['google_maps_pin'],
            ]);

            $this->bookingHistoryRepository->create([
                'uuid' => (string) Str::uuid(),
                'booking_id' => (int) $booking->getKey(),
                'status_id' => (int) $initialStatus->getKey(),
                'operator_id' => null,
            ]);

            return $booking->loadMissing([
                'customer:id,first_name,last_name,phone_number',
                'package:id,name,price',
                'status:id,code,description',
                'eventSession:id,code,description',
                'location:id,name',
            ]);
        });
    }

    public function buildRequestCode(Booking $booking): string
    {
        $createdAt = $booking->created_at ?? now();
        $bookingId = (int) $booking->getKey();

        return sprintf('ETH-REQ-%s-%06d', $createdAt->format('Y'), $bookingId);
    }

    /**
     * @return array{
     *     date: string,
     *     slots: array{
     *         morning: array{session_id:int,session_code:string,max:int,used:int,remaining:int,status:string,label:string},
     *         evening: array{session_id:int,session_code:string,max:int,used:int,remaining:int,status:string,label:string}
     *     }
     * }
     */
    public function getDateAvailability(string $eventDate): array
    {
        $date = Carbon::parse($eventDate)->toDateString();
        $sessionMeta = $this->resolveEventSessionMeta();
        $quotaMaxByCode = $this->resolveQuotaMaxByCode();

        $morningId = (int) ($sessionMeta['ids_by_code'][self::EVENT_SESSION_MORNING_CODE] ?? 0);
        $eveningId = (int) ($sessionMeta['ids_by_code'][self::EVENT_SESSION_EVENING_CODE] ?? 0);
        $usageBySessionId = $this->resolveBookedCountByDate($date, [$morningId, $eveningId]);

        $morningMax = (int) ($quotaMaxByCode[self::QUOTA_SETTING_MORNING_CODE] ?? 1);
        $morningUsed = (int) ($usageBySessionId[$morningId] ?? 0);
        $morningRemaining = max($morningMax - $morningUsed, 0);
        $morningStatus = $this->resolveQuotaStatus($morningUsed, $morningMax);

        $eveningMax = (int) ($quotaMaxByCode[self::QUOTA_SETTING_EVENING_CODE] ?? 1);
        $eveningUsed = (int) ($usageBySessionId[$eveningId] ?? 0);
        $eveningRemaining = max($eveningMax - $eveningUsed, 0);
        $eveningStatus = $this->resolveQuotaStatus($eveningUsed, $eveningMax);

        return [
            'date' => $date,
            'slots' => [
                'morning' => [
                    'session_id' => $morningId,
                    'session_code' => self::EVENT_SESSION_MORNING_CODE,
                    'max' => $morningMax,
                    'used' => $morningUsed,
                    'remaining' => $morningRemaining,
                    'status' => $morningStatus,
                    'label' => $this->resolveQuotaLabel($morningStatus, $morningRemaining, $morningMax),
                ],
                'evening' => [
                    'session_id' => $eveningId,
                    'session_code' => self::EVENT_SESSION_EVENING_CODE,
                    'max' => $eveningMax,
                    'used' => $eveningUsed,
                    'remaining' => $eveningRemaining,
                    'status' => $eveningStatus,
                    'label' => $this->resolveQuotaLabel($eveningStatus, $eveningRemaining, $eveningMax),
                ],
            ],
        ];
    }

    /**
     * @return Collection<int, Package>
     */
    private function resolveActivePackageOptions(): Collection
    {
        return $this->packageRepository
            ->query(true)
            ->with([
                'packageType:id,code,description',
                'status:id,code,description',
            ])
            ->whereHas('status', function (Builder $query): void {
                $query
                    ->where('group_id', 'package_status')
                    ->where('code', self::ACTIVE_PACKAGE_STATUS_CODE);
            })
            ->whereHas('packageType', function (Builder $query): void {
                $query
                    ->where('group_id', 'package_type')
                    ->whereIn('code', [
                        self::PACKAGE_TYPE_WEDDING_CODE,
                        self::PACKAGE_TYPE_NON_WEDDING_CODE,
                    ]);
            })
            ->orderBy('price')
            ->orderBy('name')
            ->get(['id', 'name', 'address', 'price', 'package_type', 'status_id']);
    }

    /**
     * @return Collection<int, Reference>
     */
    private function resolvePackageTypeOptions(): Collection
    {
        return $this->referenceRepository
            ->query(true)
            ->where('group_id', 'package_type')
            ->whereIn('code', [self::PACKAGE_TYPE_WEDDING_CODE, self::PACKAGE_TYPE_NON_WEDDING_CODE])
            ->orderByRaw("CASE WHEN code = ? THEN 0 WHEN code = ? THEN 1 ELSE 99 END", [
                self::PACKAGE_TYPE_WEDDING_CODE,
                self::PACKAGE_TYPE_NON_WEDDING_CODE,
            ])
            ->get(['id', 'code', 'description']);
    }

    /**
     * @return Collection<int, Reference>
     */
    private function resolveEventSessionOptions(): Collection
    {
        return $this->referenceRepository
            ->query(true)
            ->where('group_id', 'event_session')
            ->orderByRaw("CASE WHEN code = ? THEN 0 WHEN code = ? THEN 1 ELSE 99 END", [
                'ES_PAGI_SIANG',
                'ES_SORE_MALAM',
            ])
            ->get(['id', 'code', 'description']);
    }

    /**
     * @return Collection<int, Location>
     */
    private function resolveProvinceOptions(): Collection
    {
        return $this->locationRepository
            ->query(true)
            ->where('level_id', (int) $this->resolveLocationLevelIdByCodeOrFail(self::LOCATION_LEVEL_PROVINCE_CODE))
            ->orderBy('name')
            ->get(['id', 'name', 'level_id']);
    }

    /**
     * @return Collection<int, array{id: int, name: string}>
     */
    public function getLocationOptions(string $levelCode, ?int $parentId = null): Collection
    {
        $normalizedLevelCode = strtoupper(trim($levelCode));
        $levelId = $this->resolveLocationLevelIdByCodeOrFail($normalizedLevelCode);

        $query = $this->locationRepository
            ->query(true)
            ->where('level_id', $levelId);

        if ($normalizedLevelCode === self::LOCATION_LEVEL_PROVINCE_CODE) {
            $query->whereNull('parent_id');
        } else {
            $parent = is_int($parentId) && $parentId > 0 ? $parentId : null;
            if ($parent === null) {
                return collect();
            }

            $query->where('parent_id', $parent);
        }

        return $query
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(static fn (Location $location): array => [
                'id' => (int) $location->getKey(),
                'name' => $location->name,
            ])
            ->values();
    }

    /**
     * @param  array{
     *   package_id?: int|string|null,
     *   location_province_id?: int|string|null,
     *   location_city_id?: int|string|null,
     *   location_district_id?: int|string|null,
     *   location_village_id?: int|string|null,
     *   event_date?: string|null
     * }  $payload
     * @return array{
     *   package: array{
     *     id: int,
     *     name: string,
     *     type: string,
     *     address: string,
     *     description: string,
     *     benefits: array<int, string>,
     *     price: float,
     *     price_formatted: string
     *   },
     *   location_pricing_rule: array{
     *     found: bool,
     *     level_code: string|null,
     *     level_label: string|null,
     *     location_name: string|null,
     *     price_type_code: string|null,
     *     price_type_label: string|null,
     *     detail: string
     *   },
     *   payment: array{
     *     dp_percentage: int,
     *     dp_amount: float,
     *     dp_amount_formatted: string,
     *     remaining_amount: float,
     *     remaining_amount_formatted: string,
     *     dp_rule: string,
     *     dp_note: string,
     *     final_rule: string,
     *     final_note: string,
     *     final_due_date: string|null,
     *     final_due_date_label: string|null
     *   }
     * }
     */
    public function getPriceEstimate(array $payload): array
    {
        $packageId = (int) ($payload['package_id'] ?? 0);
        $package = $this->resolveActivePackageForEstimateOrFail($packageId);

        $orderedLocationCandidates = [
            [
                'id' => (int) ($payload['location_district_id'] ?? 0),
                'level_code' => self::LOCATION_LEVEL_DISTRICT_CODE,
                'level_label' => 'Kecamatan',
            ],
            [
                'id' => (int) ($payload['location_village_id'] ?? 0),
                'level_code' => self::LOCATION_LEVEL_VILLAGE_CODE,
                'level_label' => 'Kelurahan',
            ],
            [
                'id' => (int) ($payload['location_city_id'] ?? 0),
                'level_code' => self::LOCATION_LEVEL_CITY_CODE,
                'level_label' => 'Kota/Kabupaten',
            ],
            [
                'id' => (int) ($payload['location_province_id'] ?? 0),
                'level_code' => self::LOCATION_LEVEL_PROVINCE_CODE,
                'level_label' => 'Provinsi',
            ],
        ];

        $candidateIds = [];
        foreach ($orderedLocationCandidates as $candidate) {
            $candidateId = (int) ($candidate['id'] ?? 0);
            if ($candidateId > 0) {
                $candidateIds[] = $candidateId;
            }
        }

        $locationRule = null;
        if ($candidateIds !== []) {
            $rulesByLocationId = $this->locationPricingRuleRepository
                ->query(true)
                ->with([
                    'location:id,name,level_id,parent_id',
                    'location.level:id,code,description',
                    'priceType:id,code,description',
                ])
                ->whereIn('location_id', $candidateIds)
                ->get(['id', 'location_id', 'price_type'])
                ->keyBy('location_id');

            foreach ($orderedLocationCandidates as $candidate) {
                $candidateId = (int) ($candidate['id'] ?? 0);
                if ($candidateId <= 0) {
                    continue;
                }

                $matchedRule = $rulesByLocationId->get($candidateId);
                if ($matchedRule === null) {
                    continue;
                }

                $locationRule = [
                    'found' => true,
                    'level_code' => strtoupper((string) ($matchedRule->location?->level?->code ?? (string) $candidate['level_code'])),
                    'level_label' => trim((string) ($matchedRule->location?->level?->description ?? (string) $candidate['level_label'])) ?: (string) $candidate['level_label'],
                    'location_name' => trim((string) ($matchedRule->location?->name ?? '')),
                    'price_type_code' => strtoupper((string) ($matchedRule->priceType?->code ?? '')),
                    'price_type_label' => trim((string) ($matchedRule->priceType?->description ?? '')),
                    'detail' => sprintf(
                        'Prioritas level %s: %s',
                        (string) $candidate['level_label'],
                        trim((string) ($matchedRule->priceType?->description ?? '-'))
                    ),
                ];
                break;
            }
        }

        if (!is_array($locationRule)) {
            $locationRule = [
                'found' => false,
                'level_code' => null,
                'level_label' => null,
                'location_name' => null,
                'price_type_code' => null,
                'price_type_label' => null,
                'detail' => 'Belum ada aturan harga lokasi yang cocok. Silakan lengkapi lokasi acara.',
            ];
        }

        $packageTypeCode = strtoupper(trim((string) ($package->packageType?->code ?? '')));
        $defaultDpPercentageByPackageType = [
            self::PACKAGE_TYPE_WEDDING_CODE => 15,
            self::PACKAGE_TYPE_NON_WEDDING_CODE => 10,
        ];

        $dpSetting = $this->settingRepository
            ->query(true)
            ->where('group_id', self::DP_PERCENTAGE_GROUP_ID)
            ->where('type_id', (int) $package->package_type)
            ->first(['id', 'value']);

        $resolvedDpPercentage = $dpSetting !== null
            ? $this->parsePositiveNumber((string) $dpSetting->value)
            : 0;

        $dpPercentage = $resolvedDpPercentage > 0
            ? $resolvedDpPercentage
            : (int) ($defaultDpPercentageByPackageType[$packageTypeCode] ?? 0);

        $basePrice = (float) $package->price;
        $dpAmount = round(($basePrice * $dpPercentage) / 100, 2);
        $remainingAmount = max($basePrice - $dpAmount, 0);

        $paymentDateRules = $this->settingRepository
            ->query(true)
            ->where('group_id', self::PAYMENT_DATE_RULE_GROUP_ID)
            ->whereIn('code', [self::PAYMENT_DATE_RULE_DP_CODE, self::PAYMENT_DATE_RULE_FINAL_CODE])
            ->get(['code', 'value']);

        $dpRuleValue = '';
        $finalRuleValue = '';
        foreach ($paymentDateRules as $rule) {
            $code = strtoupper(trim((string) $rule->code));
            if ($code === self::PAYMENT_DATE_RULE_DP_CODE) {
                $dpRuleValue = strtoupper(trim((string) $rule->value));
            }
            if ($code === self::PAYMENT_DATE_RULE_FINAL_CODE) {
                $finalRuleValue = strtoupper(trim((string) $rule->value));
            }
        }

        $dpRuleParsed = $this->parseDayRule($dpRuleValue);
        $finalRuleParsed = $this->parseDayRule($finalRuleValue);

        $eventDateString = trim((string) ($payload['event_date'] ?? ''));
        $finalDueDate = null;
        if ($eventDateString !== '' && $finalRuleParsed !== null) {
            try {
                $eventDate = Carbon::parse($eventDateString)->startOfDay();
                if ($finalRuleParsed['operator'] === 'H+') {
                    $finalDueDate = $eventDate->copy()->addDays($finalRuleParsed['days']);
                } else {
                    $finalDueDate = $eventDate->copy()->subDays($finalRuleParsed['days']);
                }
            } catch (\Throwable) {
                $finalDueDate = null;
            }
        }

        $benefits = $package->benefits
            ->map(static function ($benefit): string {
                $name = trim((string) ($benefit->name ?? ''));
                $description = trim((string) ($benefit->description ?? ''));

                if ($name === '' && $description === '') {
                    return '';
                }

                if ($name !== '' && $description !== '') {
                    return $name . ' - ' . $description;
                }

                return $name !== '' ? $name : $description;
            })
            ->filter(static fn (string $benefit): bool => $benefit !== '')
            ->values()
            ->all();

        return [
            'package' => [
                'id' => (int) $package->getKey(),
                'name' => trim((string) $package->name),
                'type' => trim((string) ($package->packageType?->description ?? '-')),
                'address' => trim((string) ($package->address ?? '')),
                'description' => trim((string) ($package->description ?? '')),
                'benefits' => $benefits,
                'price' => $basePrice,
                'price_formatted' => $this->formatRupiah($basePrice),
            ],
            'location_pricing_rule' => $locationRule,
            'payment' => [
                'dp_percentage' => $dpPercentage,
                'dp_amount' => $dpAmount,
                'dp_amount_formatted' => $this->formatRupiah($dpAmount),
                'remaining_amount' => $remainingAmount,
                'remaining_amount_formatted' => $this->formatRupiah($remainingAmount),
                'dp_rule' => $dpRuleValue,
                'dp_note' => $this->resolveDayRuleNote($dpRuleParsed, 'approval/penawaran dibuat'),
                'final_rule' => $finalRuleValue,
                'final_note' => $this->resolveDayRuleNote($finalRuleParsed, 'tanggal acara'),
                'final_due_date' => $finalDueDate?->toDateString(),
                'final_due_date_label' => $finalDueDate?->translatedFormat('d F Y'),
            ],
        ];
    }

    private function assertSessionQuotaAvailable(string $eventDate, int $eventSessionId): void
    {
        $availability = $this->getDateAvailability($eventDate);
        $slots = $availability['slots'] ?? [];

        foreach ($slots as $slot) {
            if ((int) ($slot['session_id'] ?? 0) !== $eventSessionId) {
                continue;
            }

            if (($slot['status'] ?? 'unknown') === 'full') {
                throw new RuntimeException('Slot sesi pada tanggal yang dipilih sudah penuh. Pilih tanggal atau sesi lain.');
            }

            return;
        }
    }

    /**
     * @param  array<int, int>  $sessionIds
     * @return array<int, int>
     */
    private function resolveBookedCountByDate(string $eventDate, array $sessionIds): array
    {
        $filteredSessionIds = array_values(array_filter(array_map('intval', $sessionIds), static fn (int $id): bool => $id > 0));
        if ($filteredSessionIds === []) {
            return [];
        }

        $rows = $this->bookingRepository
            ->query(true)
            ->whereDate('event_date', $eventDate)
            ->whereIn('event_session', $filteredSessionIds)
            ->whereHas('status', function (Builder $query): void {
                $query
                    ->where('group_id', 'booking_status')
                    ->whereNotIn('code', self::NON_QUOTA_BOOKING_STATUS_CODES);
            })
            ->selectRaw('event_session, COUNT(*) AS aggregate')
            ->groupBy('event_session')
            ->get();

        $usageBySessionId = [];
        foreach ($rows as $row) {
            $sessionId = (int) ($row->event_session ?? 0);
            if ($sessionId <= 0) {
                continue;
            }

            $usageBySessionId[$sessionId] = (int) ($row->aggregate ?? 0);
        }

        return $usageBySessionId;
    }

    /**
     * @return array<string, int>
     */
    private function resolveQuotaMaxByCode(): array
    {
        if (is_array($this->quotaMaxByCode)) {
            return $this->quotaMaxByCode;
        }

        $defaults = [
            self::QUOTA_SETTING_MORNING_CODE => 1,
            self::QUOTA_SETTING_EVENING_CODE => 1,
        ];

        $settings = $this->settingRepository
            ->query(true)
            ->where('group_id', 'package_date_rule')
            ->whereIn('code', array_keys($defaults))
            ->get(['code', 'value']);

        foreach ($settings as $setting) {
            $code = strtoupper(trim((string) $setting->code));
            if (!array_key_exists($code, $defaults)) {
                continue;
            }

            $rawValue = trim((string) $setting->value);
            $parsedValue = (int) preg_replace('/[^0-9-]/', '', $rawValue);
            $defaults[$code] = $parsedValue > 0 ? $parsedValue : 1;
        }

        $this->quotaMaxByCode = $defaults;

        return $this->quotaMaxByCode;
    }

    /**
     * @return array{
     *     ids_by_code: array<string, int>,
     *     labels_by_code: array<string, string>
     * }
     */
    private function resolveEventSessionMeta(): array
    {
        if (is_array($this->eventSessionMeta)) {
            return $this->eventSessionMeta;
        }

        $required = [
            self::EVENT_SESSION_MORNING_CODE => 'Pagi-Siang',
            self::EVENT_SESSION_EVENING_CODE => 'Sore-Malam',
        ];

        $references = $this->referenceRepository
            ->query(true)
            ->where('group_id', 'event_session')
            ->whereIn('code', array_keys($required))
            ->get(['id', 'code', 'description']);

        $meta = [
            'ids_by_code' => [],
            'labels_by_code' => [],
        ];

        foreach ($references as $reference) {
            $code = strtoupper(trim((string) $reference->code));
            if (!isset($required[$code])) {
                continue;
            }

            $meta['ids_by_code'][$code] = (int) $reference->id;
            $description = trim((string) $reference->description);
            $meta['labels_by_code'][$code] = $description !== '' ? $description : $required[$code];
        }

        foreach (array_keys($required) as $code) {
            if (!isset($meta['ids_by_code'][$code])) {
                throw new RuntimeException('Reference sesi acara tidak lengkap.');
            }
        }

        $this->eventSessionMeta = $meta;

        return $this->eventSessionMeta;
    }

    private function resolveQuotaStatus(int $used, int $max): string
    {
        if ($max <= 0 || $used >= $max) {
            return 'full';
        }

        $remaining = $max - $used;
        if ($remaining <= 1) {
            return 'limited';
        }

        return 'available';
    }

    private function resolveQuotaLabel(string $status, int $remaining, int $max): string
    {
        if ($max <= 0) {
            return 'Penuh';
        }

        if ($status === 'full') {
            return 'Penuh';
        }

        if ($status === 'limited') {
            return sprintf('Tersisa %d dari %d slot', $remaining, $max);
        }

        return sprintf('Tersedia %d dari %d slot', $remaining, $max);
    }

    private function parsePositiveNumber(string $rawValue): int
    {
        $sanitized = preg_replace('/[^0-9]/', '', $rawValue);
        if ($sanitized === null || $sanitized === '') {
            return 0;
        }

        $parsed = (int) $sanitized;

        return $parsed > 0 ? $parsed : 0;
    }

    /**
     * @return array{operator: string, days: int}|null
     */
    private function parseDayRule(string $rawRule): ?array
    {
        $normalizedRule = strtoupper(trim($rawRule));
        if ($normalizedRule === '') {
            return null;
        }

        if (preg_match('/^(H\+|H-)(\d+)$/', $normalizedRule, $matches) !== 1) {
            return null;
        }

        $days = (int) $matches[2];
        if ($days < 0) {
            return null;
        }

        return [
            'operator' => $matches[1],
            'days' => $days,
        ];
    }

    /**
     * @param  array{operator: string, days: int}|null  $rule
     */
    private function resolveDayRuleNote(?array $rule, string $baseLabel): string
    {
        if ($rule === null) {
            return 'Aturan belum tersedia.';
        }

        $days = (int) ($rule['days'] ?? 0);
        $operator = (string) ($rule['operator'] ?? '');

        if ($operator === 'H+') {
            return sprintf('Maksimal %d hari setelah %s.', $days, $baseLabel);
        }

        return sprintf('Maksimal %d hari sebelum %s.', $days, $baseLabel);
    }

    private function formatRupiah(float $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    private function resolveActivePackageForEstimateOrFail(int $packageId): Package
    {
        $package = $this->packageRepository
            ->query(true)
            ->with([
                'packageType:id,code,description',
                'benefits:id,package_id,name,description',
            ])
            ->whereKey($packageId)
            ->whereHas('status', function (Builder $query): void {
                $query
                    ->where('group_id', 'package_status')
                    ->where('code', self::ACTIVE_PACKAGE_STATUS_CODE);
            })
            ->first(['id', 'name', 'address', 'description', 'price', 'status_id', 'package_type']);

        if (!$package instanceof Package) {
            throw new RuntimeException('Paket yang dipilih tidak tersedia atau tidak aktif.');
        }

        return $package;
    }

    private function resolveActivePackageOrFail(int $packageId): Package
    {
        $package = $this->packageRepository
            ->query(true)
            ->whereKey($packageId)
            ->whereHas('status', function (Builder $query): void {
                $query
                    ->where('group_id', 'package_status')
                    ->where('code', self::ACTIVE_PACKAGE_STATUS_CODE);
            })
            ->first(['id', 'name', 'case_id', 'price', 'status_id', 'package_type', 'created_at']);

        if (!$package instanceof Package) {
            throw new RuntimeException('Paket yang dipilih tidak tersedia atau tidak aktif.');
        }

        return $package;
    }

    private function ensurePackageCaseId(Package $package): Package
    {
        $existingCaseId = strtoupper(trim((string) ($package->case_id ?? '')));
        if ($existingCaseId !== '') {
            return $package;
        }

        $createdAt = $package->created_at instanceof Carbon ? $package->created_at : now();
        $packageId = (int) $package->getKey();
        $baseCaseId = sprintf('PKG-%s-%05d', $createdAt->format('Ymd'), $packageId);

        $candidate = $baseCaseId;
        $suffix = 1;
        while ($this->isPackageCaseIdUsedByAnotherPackage($candidate, $packageId)) {
            $candidate = sprintf('%s-%02d', $baseCaseId, $suffix);
            $suffix++;

            if ($suffix > 99) {
                $candidate = sprintf('PKG-%s-%s', $createdAt->format('Ymd'), strtoupper(substr((string) Str::uuid(), 0, 8)));
                if (!$this->isPackageCaseIdUsedByAnotherPackage($candidate, $packageId)) {
                    break;
                }
            }
        }

        /** @var Package $updatedPackage */
        $updatedPackage = $this->packageRepository->update($package, [
            'case_id' => $candidate,
        ]);

        return $updatedPackage;
    }

    private function isPackageCaseIdUsedByAnotherPackage(string $caseId, int $excludePackageId): bool
    {
        $normalizedCaseId = strtoupper(trim($caseId));
        if ($normalizedCaseId === '') {
            return false;
        }

        return $this->packageRepository
            ->query(true)
            ->whereRaw('UPPER(case_id) = ?', [$normalizedCaseId])
            ->where('id', '<>', $excludePackageId)
            ->exists();
    }

    private function resolveLocationHierarchyOrFail(
        int $provinceId,
        int $cityId,
        int $districtId,
        int $villageId,
        int $selectedLocationId
    ): Location
    {
        $province = $this->resolveLocationByIdAndLevelOrFail(
            $provinceId,
            self::LOCATION_LEVEL_PROVINCE_CODE,
            'Provinsi acara tidak valid.'
        );

        $city = $this->resolveLocationByIdAndLevelOrFail(
            $cityId,
            self::LOCATION_LEVEL_CITY_CODE,
            'Kota acara tidak valid.'
        );
        if ((int) ($city->parent_id ?? 0) !== (int) $province->getKey()) {
            throw new RuntimeException('Kota yang dipilih tidak berada pada provinsi yang dipilih.');
        }

        $district = $this->resolveLocationByIdAndLevelOrFail(
            $districtId,
            self::LOCATION_LEVEL_DISTRICT_CODE,
            'Kecamatan acara tidak valid.'
        );
        if ((int) ($district->parent_id ?? 0) !== (int) $city->getKey()) {
            throw new RuntimeException('Kecamatan yang dipilih tidak berada pada kota yang dipilih.');
        }

        $village = $this->resolveLocationByIdAndLevelOrFail(
            $villageId,
            self::LOCATION_LEVEL_VILLAGE_CODE,
            'Kelurahan acara tidak valid.'
        );
        if ((int) ($village->parent_id ?? 0) !== (int) $district->getKey()) {
            throw new RuntimeException('Kelurahan yang dipilih tidak berada pada kecamatan yang dipilih.');
        }

        if ((int) $village->getKey() !== $selectedLocationId) {
            throw new RuntimeException('Lokasi akhir acara tidak sinkron. Pilih ulang kelurahan.');
        }

        return $village;
    }

    private function resolveLocationByIdAndLevelOrFail(int $locationId, string $levelCode, string $errorMessage): Location
    {
        $location = $this->locationRepository
            ->query(true)
            ->whereKey($locationId)
            ->where('level_id', $this->resolveLocationLevelIdByCodeOrFail($levelCode))
            ->first(['id', 'name', 'level_id', 'parent_id']);

        if (!$location instanceof Location) {
            throw new RuntimeException($errorMessage);
        }

        return $location;
    }

    private function resolveLocationLevelIdByCodeOrFail(string $levelCode): int
    {
        $normalizedCode = strtoupper(trim($levelCode));
        $meta = $this->resolveLocationLevelMeta();

        if (!isset($meta['ids_by_code'][$normalizedCode])) {
            throw new RuntimeException('Reference level lokasi tidak lengkap.');
        }

        return (int) $meta['ids_by_code'][$normalizedCode];
    }

    /**
     * @return array{
     *     ids: array<int, int>,
     *     labels_by_code: array<string, string>,
     *     ids_by_code: array<string, int>
     * }
     */
    private function resolveLocationLevelMeta(): array
    {
        if (is_array($this->locationLevelMeta)) {
            return $this->locationLevelMeta;
        }

        $requiredCodes = [
            self::LOCATION_LEVEL_PROVINCE_CODE => 'Provinsi',
            self::LOCATION_LEVEL_CITY_CODE => 'Kota/Kabupaten',
            self::LOCATION_LEVEL_DISTRICT_CODE => 'Kecamatan',
            self::LOCATION_LEVEL_VILLAGE_CODE => 'Kelurahan',
        ];

        $references = $this->referenceRepository
            ->query(true)
            ->where('group_id', 'location_level')
            ->whereIn('code', array_keys($requiredCodes))
            ->get(['id', 'code', 'description']);

        $meta = [
            'ids' => [],
            'labels_by_code' => [],
            'ids_by_code' => [],
        ];

        foreach ($references as $reference) {
            $code = strtoupper((string) $reference->code);
            if (!isset($requiredCodes[$code])) {
                continue;
            }

            $id = (int) $reference->id;
            $meta['ids'][] = $id;
            $meta['labels_by_code'][$code] = $requiredCodes[$code];
            $meta['ids_by_code'][$code] = $id;
        }

        foreach (array_keys($requiredCodes) as $requiredCode) {
            if (!isset($meta['ids_by_code'][$requiredCode])) {
                throw new RuntimeException('Reference level lokasi tidak lengkap.');
            }
        }

        $this->locationLevelMeta = $meta;

        return $this->locationLevelMeta;
    }

    private function resolveReferenceByIdAndGroup(int $referenceId, string $groupId, string $errorMessage): Reference
    {
        $reference = $this->referenceRepository
            ->query(true)
            ->where('group_id', $groupId)
            ->whereKey($referenceId)
            ->first(['id', 'code', 'description', 'group_id']);

        if (!$reference instanceof Reference) {
            throw new RuntimeException($errorMessage);
        }

        return $reference;
    }

    private function resolveReferenceByGroupAndCode(string $groupId, string $code, string $errorMessage): Reference
    {
        $reference = $this->referenceRepository
            ->query(true)
            ->where('group_id', $groupId)
            ->where('code', $code)
            ->first(['id', 'code', 'description', 'group_id']);

        if (!$reference instanceof Reference) {
            throw new RuntimeException($errorMessage);
        }

        return $reference;
    }

    /**
     * @return array{0:string,1:string|null}
     */
    private function splitCustomerName(string $fullName): array
    {
        $normalized = trim(preg_replace('/\s+/', ' ', $fullName) ?? '');
        if ($normalized === '') {
            throw new RuntimeException('Nama customer wajib diisi.');
        }

        $segments = explode(' ', $normalized);
        $firstName = trim((string) array_shift($segments));
        $lastName = count($segments) > 0 ? trim(implode(' ', $segments)) : null;

        return [$firstName, $lastName !== '' ? $lastName : null];
    }
}
