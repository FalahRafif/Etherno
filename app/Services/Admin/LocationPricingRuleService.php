<?php

namespace App\Services\Admin;

use App\Models\Location;
use App\Models\LocationPricingRule;
use App\Models\Reference;
use App\Repositories\Contracts\LocationPricingRuleRepositoryInterface;
use App\Repositories\Contracts\LocationRepositoryInterface;
use App\Repositories\Contracts\ReferenceRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use RuntimeException;

class LocationPricingRuleService
{
    /**
     * @var array{ids: array<int, int>, labels: array<int, string>}|null
     */
    private ?array $locationLevelMeta = null;

    /**
     * @var Collection<int, Reference>|null
     */
    private ?Collection $priceTypeOptionsCache = null;

    /**
     * @var Collection<int, array<string, mixed>>|null
     */
    private ?Collection $locationOptionsCache = null;

    public function __construct(
        private LocationPricingRuleRepositoryInterface $locationPricingRuleRepository,
        private LocationRepositoryInterface $locationRepository,
        private ReferenceRepositoryInterface $referenceRepository
    ) {
    }

    /**
     * @return array{
     *   rules: Collection<int, LocationPricingRule>,
     *   stats: array<string, int>
     * }
     */
    public function getPagePayload(?string $search = null): array
    {
        $rules = $this->buildRulesQuery($search)->get();

        return [
            'rules' => $rules,
            'stats' => $this->buildStats($rules),
        ];
    }

    /**
     * @return array{
     *   locationOptions: Collection<int, array<string, mixed>>,
     *   priceTypeOptions: Collection<int, Reference>
     * }
     */
    public function getFormPayload(): array
    {
        return [
            'locationOptions' => $this->locationOptions(),
            'priceTypeOptions' => $this->priceTypeOptions(),
        ];
    }

    public function createRule(array $payload): LocationPricingRule
    {
        $locationId = (int) ($payload['location_id'] ?? 0);
        $priceTypeId = (int) ($payload['price_type'] ?? 0);

        $this->resolveAllowedLocationOrFail($locationId);
        $this->resolveAllowedPriceTypeOrFail($priceTypeId);
        $this->assertLocationRuleUniqueness($locationId, null);

        /** @var LocationPricingRule $rule */
        $rule = $this->locationPricingRuleRepository->create([
            'uuid' => (string) Str::uuid(),
            'location_id' => $locationId,
            'price_type' => $priceTypeId,
        ]);

        return $rule->loadMissing($this->rulesRelations());
    }

    public function updateRule(LocationPricingRule $rule, array $payload): LocationPricingRule
    {
        $managedRule = $this->resolveEditableRule($rule);
        $locationId = (int) ($payload['location_id'] ?? 0);
        $priceTypeId = (int) ($payload['price_type'] ?? 0);

        $this->resolveAllowedLocationOrFail($locationId);
        $this->resolveAllowedPriceTypeOrFail($priceTypeId);
        $this->assertLocationRuleUniqueness($locationId, (int) $managedRule->getKey());

        /** @var LocationPricingRule $updatedRule */
        $updatedRule = $this->locationPricingRuleRepository->update($managedRule, [
            'location_id' => $locationId,
            'price_type' => $priceTypeId,
        ]);

        return $updatedRule->loadMissing($this->rulesRelations());
    }

    public function deleteRule(LocationPricingRule $rule): bool
    {
        $managedRule = $this->resolveEditableRule($rule);

        return $this->locationPricingRuleRepository->delete($managedRule, $this->resolveCurrentUserId());
    }

    public function resolveEditableRule(LocationPricingRule $rule): LocationPricingRule
    {
        $rule->loadMissing($this->rulesRelations());

        $location = $rule->location;
        if (!$location instanceof Location) {
            throw new RuntimeException('Lokasi aturan harga tidak valid.');
        }

        if (!in_array((int) $location->level_id, $this->allowedLocationLevelIds(), true)) {
            throw new RuntimeException('Aturan harga ini berada di level lokasi yang tidak dikelola modul ini.');
        }

        return $rule;
    }

    /**
     * @param  Collection<int, LocationPricingRule>  $rules
     * @return array<string, int>
     */
    private function buildStats(Collection $rules): array
    {
        $provinceCount = 0;
        $cityCount = 0;

        foreach ($rules as $rule) {
            $levelCode = strtoupper((string) ($rule->location?->level?->code ?? ''));

            if ($levelCode === 'LL_PV') {
                $provinceCount++;
                continue;
            }

            if ($levelCode === 'LL_CT') {
                $cityCount++;
            }
        }

        return [
            'total' => $rules->count(),
            'province' => $provinceCount,
            'city' => $cityCount,
        ];
    }

    private function buildRulesQuery(?string $search = null): Builder
    {
        $query = $this->locationPricingRuleRepository
            ->query(true)
            ->with($this->rulesRelations())
            ->orderByDesc('id');

        $keyword = trim((string) $search);
        if ($keyword === '') {
            return $query;
        }

        $query->where(function (Builder $builder) use ($keyword): void {
            $builder
                ->whereHas('location', function (Builder $locationQuery) use ($keyword): void {
                    $locationQuery
                        ->where('name', 'like', '%' . $keyword . '%')
                        ->orWhereHas('parent', function (Builder $parentQuery) use ($keyword): void {
                            $parentQuery->where('name', 'like', '%' . $keyword . '%');
                        });
                })
                ->orWhereHas('priceType', function (Builder $priceTypeQuery) use ($keyword): void {
                    $priceTypeQuery
                        ->where('code', 'like', '%' . $keyword . '%')
                        ->orWhere('description', 'like', '%' . $keyword . '%');
                });
        });

        return $query;
    }

    /**
     * @return Collection<int, Reference>
     */
    private function priceTypeOptions(): Collection
    {
        if ($this->priceTypeOptionsCache instanceof Collection) {
            return $this->priceTypeOptionsCache;
        }

        $this->priceTypeOptionsCache = $this->referenceRepository
            ->query(true)
            ->where('group_id', 'price_type')
            ->orderByRaw("CASE WHEN code = ? THEN 0 WHEN code = ? THEN 1 WHEN code = ? THEN 2 ELSE 99 END", [
                'PT_RG',
                'PT_SD',
                'PT_CS',
            ])
            ->orderBy('description')
            ->get(['id', 'code', 'description']);

        return $this->priceTypeOptionsCache;
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function locationOptions(): Collection
    {
        if ($this->locationOptionsCache instanceof Collection) {
            return $this->locationOptionsCache;
        }

        $allowedLevelIds = $this->allowedLocationLevelIds();

        /** @var Collection<int, Location> $locations */
        $locations = $this->locationRepository
            ->query(true)
            ->with([
                'parent:id,name',
                'level:id,code,description',
            ])
            ->whereIn('level_id', $allowedLevelIds)
            ->orderByRaw("CASE WHEN level_id = ? THEN 0 WHEN level_id = ? THEN 1 ELSE 99 END", [
                $allowedLevelIds[0] ?? 0,
                $allowedLevelIds[1] ?? 0,
            ])
            ->orderBy('name')
            ->get(['id', 'name', 'level_id', 'parent_id']);

        $this->locationOptionsCache = $locations
            ->map(function (Location $location): array {
                $levelId = (int) $location->level_id;
                $levelLabel = $this->locationLevelLabelsById()[$levelId] ?? 'Lokasi';
                $levelCode = strtoupper((string) ($location->level?->code ?? ''));
                $parentName = trim((string) ($location->parent?->name ?? ''));
                $displayName = $levelCode === 'LL_PV'
                    ? $location->name
                    : ($location->name . ($parentName !== '' ? ' - ' . $parentName : ''));

                return [
                    'id' => (int) $location->getKey(),
                    'name' => $location->name,
                    'level_id' => $levelId,
                    'level_code' => $levelCode,
                    'level_label' => $levelLabel,
                    'parent_name' => $parentName,
                    'display_name' => $displayName,
                ];
            })
            ->values();

        return $this->locationOptionsCache;
    }

    /**
     * @return array<int, string>
     */
    private function locationLevelLabelsById(): array
    {
        return $this->resolveLocationLevelMeta()['labels'];
    }

    /**
     * @return array<int, int>
     */
    private function allowedLocationLevelIds(): array
    {
        return $this->resolveLocationLevelMeta()['ids'];
    }

    /**
     * @return array{ids: array<int, int>, labels: array<int, string>}
     */
    private function resolveLocationLevelMeta(): array
    {
        if (is_array($this->locationLevelMeta)) {
            return $this->locationLevelMeta;
        }

        $references = $this->referenceRepository
            ->query(true)
            ->where('group_id', 'location_level')
            ->whereIn('code', ['LL_PV', 'LL_CT'])
            ->get(['id', 'code', 'description']);

        $provinceId = null;
        $cityId = null;

        foreach ($references as $reference) {
            $id = (int) $reference->id;
            $code = strtoupper((string) $reference->code);

            if ($code === 'LL_PV') {
                $provinceId = $id;
                continue;
            }

            if ($code === 'LL_CT') {
                $cityId = $id;
            }
        }

        if (!is_int($provinceId) || !is_int($cityId)) {
            throw new RuntimeException('Reference level lokasi Provinsi/Kota tidak lengkap.');
        }

        $this->locationLevelMeta = [
            'ids' => [$provinceId, $cityId],
            'labels' => [
                $provinceId => 'Provinsi',
                $cityId => 'Kota/Kabupaten',
            ],
        ];

        return $this->locationLevelMeta;
    }

    private function resolveAllowedLocationOrFail(int $locationId): Location
    {
        $location = $this->locationRepository
            ->query(true)
            ->whereIn('level_id', $this->allowedLocationLevelIds())
            ->find($locationId, ['id', 'name', 'level_id', 'parent_id']);

        if (!$location instanceof Location) {
            throw new RuntimeException('Lokasi yang dipilih tidak valid untuk aturan harga.');
        }

        return $location;
    }

    private function resolveAllowedPriceTypeOrFail(int $priceTypeId): Reference
    {
        $priceType = $this->referenceRepository
            ->query(true)
            ->where('group_id', 'price_type')
            ->find($priceTypeId, ['id', 'code', 'description']);

        if (!$priceType instanceof Reference) {
            throw new RuntimeException('Tipe harga yang dipilih tidak valid.');
        }

        return $priceType;
    }

    private function assertLocationRuleUniqueness(int $locationId, ?int $ignoreRuleId): void
    {
        $query = $this->locationPricingRuleRepository
            ->query(true)
            ->where('location_id', $locationId);

        if (is_int($ignoreRuleId) && $ignoreRuleId > 0) {
            $query->whereKeyNot($ignoreRuleId);
        }

        if ($query->exists()) {
            throw new RuntimeException('Lokasi tersebut sudah memiliki aturan harga aktif.');
        }
    }

    /**
     * @return array<int, string>
     */
    private function rulesRelations(): array
    {
        return [
            'location:id,name,level_id,parent_id',
            'location.level:id,code,description',
            'location.parent:id,name',
            'priceType:id,code,description',
        ];
    }

    private function resolveCurrentUserId(): ?int
    {
        $authId = auth()->id();

        return is_int($authId) ? $authId : null;
    }
}
