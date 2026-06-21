<?php

namespace App\Http\Requests\Admin\OperationalConfig;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOperationalConfigRequest extends FormRequest
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
                    $query->where('group_id', 'operational_config')->where('delete_status', false);
                }),
            ],
            'description' => ['required', 'string', 'max:255'],
            'value' => ['required', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'code.unique' => 'Kode konfigurasi sudah digunakan.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return [
            'code' => trim((string) $this->input('code')),
            'description' => trim((string) $this->input('description')),
            'value' => trim((string) $this->input('value')),
        ];
    }
}
