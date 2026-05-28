<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookingRefundProofRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method_code' => ['required', 'string', Rule::in([
                'PYM_BANK_TRANSFER', 'PYM_QRIS', 'PYM_CASH', 'PYM_E_WALLET',
            ])],
            'transfer_receipt' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,pdf,webp'],
        ];
    }

    public function payload(): array
    {
        return [
            'payment_method_code' => strtoupper(trim((string) ($this->validated()['payment_method_code'] ?? 'PYM_BANK_TRANSFER'))),
            'transfer_receipt' => $this->hasFile('transfer_receipt') ? $this->file('transfer_receipt') : null,
        ];
    }
}
