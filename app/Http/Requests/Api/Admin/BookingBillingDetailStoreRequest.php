<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookingBillingDetailStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'billing_type_code' => ['nullable', 'string', Rule::in(['BLT_ADDON'])],
            'name' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:600'],
            'amount' => ['required', 'numeric', 'gt:0'],
        ];
    }

    public function payload(): array
    {
        $validated = $this->validated();

        return [
            'billing_type_code' => strtoupper(trim((string) ($validated['billing_type_code'] ?? 'BLT_ADDON'))),
            'name' => trim((string) ($validated['name'] ?? '')),
            'description' => trim((string) ($validated['description'] ?? '')),
            'amount' => (float) ($validated['amount'] ?? 0),
        ];
    }
}
