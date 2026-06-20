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
        $typeCode = strtoupper(trim((string) ($this->input('installment_type_code') ?? '')));
        $isManual = !in_array($typeCode, ['INS_DP', 'INS_FINAL'], true);

        return [
            'installment_type_code' => ['required', 'string', Rule::in(['INS_DP', 'INS_PARTIAL', 'INS_FINAL'])],
            'amount' => [$isManual ? 'required' : 'sometimes', 'numeric', 'gt:0'],
            'due_date' => [$isManual ? 'required' : 'sometimes', 'date'],
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

