<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookingUploadPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'billing_installment_id' => ['required', 'integer'],
            'is_partial' => ['nullable', 'string', 'in:true,false,1,0'],
            'amount' => ['nullable', 'numeric', 'gt:0'],
            'payment_method_code' => ['required', 'string', Rule::in([
                'PYM_BANK_TRANSFER', 'PYM_QRIS', 'PYM_CASH', 'PYM_E_WALLET',
            ])],
            'transfer_receipt' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,pdf,webp'],
        ];
    }

    public function payload(): array
    {
        $validated = $this->validated();
        $isPartial = in_array(strtolower((string) ($validated['is_partial'] ?? '')), ['true', '1'], true);

        return [
            'billing_installment_id' => (int) ($validated['billing_installment_id'] ?? 0),
            'is_partial' => $isPartial,
            'amount' => $isPartial ? (float) ($validated['amount'] ?? 0) : 0,
            'payment_method_code' => strtoupper(trim((string) ($validated['payment_method_code'] ?? 'PYM_BANK_TRANSFER'))),
            'transfer_receipt' => $this->hasFile('transfer_receipt') ? $this->file('transfer_receipt') : null,
        ];
    }
}
