<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookingInstallmentStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'installment_type_code' => ['required', 'string', Rule::in(['INS_DP', 'INS_PARTIAL', 'INS_FINAL'])],
            'amount' => ['required', 'numeric', 'gt:0'],
            'due_date' => ['required', 'date'],
        ];
    }

    public function payload(): array
    {
        $validated = $this->validated();

        return [
            'installment_type_code' => strtoupper(trim((string) ($validated['installment_type_code'] ?? 'INS_PARTIAL'))),
            'amount' => (float) ($validated['amount'] ?? 0),
            'due_date' => trim((string) ($validated['due_date'] ?? '')),
        ];
    }
}

