<?php

namespace App\Http\Requests\Admin\LocationPricingRule;

use App\Models\LocationPricingRule;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UpdateLocationPricingRuleRequest extends FormRequest
{
    /**
     * @var array<int, int>|null
     */
    private ?array $allowedLocationLevelIds = null;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $rule = $this->route('locationPricingRule');
        $ruleId = $rule instanceof LocationPricingRule ? $rule->getKey() : $rule;

        return [
            'location_id' => [
                'required',
                'integer',
                Rule::exists('locations', 'id')->where(function (Builder $query): void {
                    $query
                        ->where('delete_status', false)
                        ->whereIn('level_id', $this->resolveAllowedLocationLevelIds());
                }),
                Rule::unique('location_pricing_rules', 'location_id')
                    ->ignore($ruleId)
                    ->where(function (Builder $query): void {
                        $query->where('delete_status', false);
                    }),
            ],
            'price_type' => [
                'required',
                'integer',
                Rule::exists('references', 'id')->where(function (Builder $query): void {
                    $query
                        ->where('delete_status', false)
                        ->where('group_id', 'price_type');
                }),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'location_id.unique' => 'Lokasi tersebut sudah memiliki aturan harga aktif.',
        ];
    }

    /**
     * @return array<string, int>
     */
    public function payload(): array
    {
        return [
            'location_id' => (int) $this->input('location_id'),
            'price_type' => (int) $this->input('price_type'),
        ];
    }

    /**
     * @return array<int, int>
     */
    private function resolveAllowedLocationLevelIds(): array
    {
        if (is_array($this->allowedLocationLevelIds)) {
            return $this->allowedLocationLevelIds;
        }

        $this->allowedLocationLevelIds = DB::table('references')
            ->where('group_id', 'location_level')
            ->whereIn('code', ['LL_PV', 'LL_CT'])
            ->where('delete_status', false)
            ->pluck('id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->all();

        return $this->allowedLocationLevelIds;
    }
}
