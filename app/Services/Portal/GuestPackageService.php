<?php

namespace App\Services\Portal;

use App\Models\Package;
use App\Repositories\Contracts\PackageRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class GuestPackageService
{
    private const ACTIVE_STATUS_CODE = 'PS_ACTIVE';

    private const WEDDING_PACKAGE_CODE = 'PKT_WEDDING';

    private const NON_WEDDING_PACKAGE_CODE = 'PKT_NON_WEDDING';

    public function __construct(private PackageRepositoryInterface $packageRepository)
    {
    }

    /**
     * @return array{
     *   weddingPackages: Collection<int, Package>,
     *   nonWeddingPackages: Collection<int, Package>
     * }
     */
    public function getLandingPayload(): array
    {
        $packages = $this->buildActivePackagesQuery()->get();

        return [
            'weddingPackages' => $this->filterPackagesByType($packages, self::WEDDING_PACKAGE_CODE)->take(3)->values(),
            'nonWeddingPackages' => $this->filterPackagesByType($packages, self::NON_WEDDING_PACKAGE_CODE)->take(3)->values(),
        ];
    }

    /**
     * @return array{
     *   weddingPackages: Collection<int, Package>,
     *   nonWeddingPackages: Collection<int, Package>
     * }
     */
    public function getAllPackagesPayload(): array
    {
        $packages = $this->buildActivePackagesQuery()->get();

        return [
            'weddingPackages' => $this->filterPackagesByType($packages, self::WEDDING_PACKAGE_CODE)->values(),
            'nonWeddingPackages' => $this->filterPackagesByType($packages, self::NON_WEDDING_PACKAGE_CODE)->values(),
        ];
    }

    /**
     * @return array{items: array<int, mixed>, total: int, per_page: int, current_page: int, last_page: int}
     */
    public function getPaginatedPackages(string $type, int $page, int $perPage): array
    {
        $typeCode = strtolower($type) === 'non_wedding' || strtolower($type) === 'non-wedding'
            ? self::NON_WEDDING_PACKAGE_CODE
            : self::WEDDING_PACKAGE_CODE;

        $all = $this->buildActivePackagesQuery()->get();
        $filtered = $this->filterPackagesByType($all, $typeCode);
        $total = $filtered->count();
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = min($page, $lastPage);
        $items = $filtered->slice(($page - 1) * $perPage, $perPage)->values();

        $ttl = now()->addMinutes((int) config('app.attachments.temp_url_ttl_minutes', 30));

        $mapped = $items->map(function (Package $package) use ($ttl): array {
            $thumbnailUrl = null;
            if ($package->thumbnailAttachment) {
                $thumbnailUrl = \Illuminate\Support\Facades\URL::signedRoute(
                    'api.public.attachments.package-thumbnail',
                    ['attachmentUuid' => $package->thumbnailAttachment->uuid],
                    $ttl
                );
            }

            return [
                'id' => $package->id,
                'name' => $package->name,
                'price' => (float) $package->price,
                'price_formatted' => 'Rp ' . number_format((float) $package->price, 0, ',', '.'),
                'description' => $package->description,
                'thumbnail_url' => $thumbnailUrl,
                'benefits' => $package->benefits->pluck('name')->filter()->values()->all(),
            ];
        })->values()->all();

        return [
            'items' => $mapped,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => $lastPage,
        ];
    }

    private function buildActivePackagesQuery(): Builder
    {
        return $this->packageRepository
            ->query(true)
            ->with($this->packageRelations())
            ->whereHas('status', function (Builder $statusQuery): void {
                $statusQuery
                    ->where('group_id', 'package_status')
                    ->where('code', self::ACTIVE_STATUS_CODE);
            })
            ->whereHas('packageType', function (Builder $typeQuery): void {
                $typeQuery
                    ->where('group_id', 'package_type')
                    ->whereIn('code', [self::WEDDING_PACKAGE_CODE, self::NON_WEDDING_PACKAGE_CODE]);
            })
            ->orderBy('price')
            ->orderBy('id');
    }

    /**
     * @param  Collection<int, Package>  $packages
     * @return Collection<int, Package>
     */
    private function filterPackagesByType(Collection $packages, string $typeCode): Collection
    {
        return $packages
            ->filter(function (Package $package) use ($typeCode): bool {
                $currentTypeCode = strtoupper((string) ($package->packageType?->code ?? ''));

                return $currentTypeCode === $typeCode;
            })
            ->values();
    }

    /**
     * @return array<int, string>
     */
    private function packageRelations(): array
    {
        return [
            'status:id,code,description',
            'packageType:id,code,description',
            'thumbnailAttachment:id,uuid,name,path',
            'benefits' => function (HasMany $query): void {
                $query->orderBy('id');
            },
        ];
    }
}
