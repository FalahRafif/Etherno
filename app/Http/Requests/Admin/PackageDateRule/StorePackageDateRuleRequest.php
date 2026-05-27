<?php

namespace App\Http\Requests\Admin\PackageDateRule;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePackageDateRuleRequest extends FormRequest
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
            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('settings', 'code')->where(function ($query): void {
                    $query->where('group_id', 'package_date_rule')->where('delete_status', false);
                }),
            ],
            'description' => ['required', 'string', 'max:255'],
            'value_type' => ['required', 'string', 'in:H+,H-'],
            'value_days' => ['required', 'integer', 'min:1', 'max:365'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'code.unique' => 'Kode aturan sudah digunakan.',
            'value_type.in' => 'Tipe perhitungan harus H+ atau H-.',
            'value_days.min' => 'Jumlah hari minimal 1.',
            'value_days.max' => 'Jumlah hari maksimal 365.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        $type = $this->input('value_type');
        $days = (int) $this->input('value_days');

        return [
            'code' => trim((string) $this->input('code')),
            'description' => trim((string) $this->input('description')),
            'value' => $type . $days,
        ];
    }
}
