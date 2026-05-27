<?php

namespace App\Http\Requests\Admin\DpPercentageRule;

use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDpPercentageRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'description' => ['required', 'string', 'max:255'],
            'type_id' => [
                'required',
                'integer',
                Rule::exists('references', 'id')->where(function ($query): void {
                    $query->where('delete_status', false)->where('group_id', 'package_type');
                }),
            ],
            'value' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'type_id.exists' => 'Tipe paket yang dipilih tidak valid.',
            'value.min' => 'Persentase minimal 0%.',
            'value.max' => 'Persentase maksimal 100%.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return [
            'description' => trim((string) $this->input('description')),
            'type_id' => (int) $this->input('type_id'),
            'value' => (string) $this->input('value'),
        ];
    }
}
