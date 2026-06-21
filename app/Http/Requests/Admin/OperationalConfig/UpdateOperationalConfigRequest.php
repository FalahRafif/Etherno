<?php

namespace App\Http\Requests\Admin\OperationalConfig;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOperationalConfigRequest extends FormRequest
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
            'value' => ['required', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return [
            'description' => trim((string) $this->input('description')),
            'value' => trim((string) $this->input('value')),
        ];
    }
}
