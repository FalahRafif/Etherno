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
        return [
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
            'description' => trim((string) $this->input('description')),
            'value' => $type . $days,
        ];
    }
}
