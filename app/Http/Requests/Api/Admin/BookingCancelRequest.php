<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BookingCancelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reason' => ['nullable', 'string', 'max:600'],
        ];
    }

    public function reason(): string
    {
        return trim((string) ($this->validated()['reason'] ?? ''));
    }
}
