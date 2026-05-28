<?php

namespace App\Http\Requests\Admin\PackageDateRule;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePackageDateRuleRequest extends FormRequest
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
        $rules = [
            'description' => ['required', 'string', 'max:255'],
        ];

        $isQuota = $this->boolean('is_quota') || $this->filled('value_number');

        if ($isQuota) {
            $rules['value_number'] = ['required', 'integer', 'min:1', 'max:100'];
        } else {
            $rules['value_type'] = ['required', 'string', 'in:H+,H-'];
            $rules['value_days'] = ['required', 'integer', 'min:1', 'max:365'];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'value_type.in' => 'Tipe perhitungan harus H+ atau H-.',
            'value_days.min' => 'Jumlah hari minimal 1.',
            'value_days.max' => 'Jumlah hari maksimal 365.',
            'value_number.min' => 'Jumlah kuota minimal 1.',
            'value_number.max' => 'Jumlah kuota maksimal 100.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        $isQuota = $this->boolean('is_quota') || $this->filled('value_number');
        $value = '';

        if ($isQuota) {
            $value = (string) ((int) $this->input('value_number'));
        } else {
            $type = $this->input('value_type');
            $days = (int) $this->input('value_days');
            $value = $type . $days;
        }

        return [
            'description' => trim((string) $this->input('description')),
            'value' => $value,
        ];
    }
}
