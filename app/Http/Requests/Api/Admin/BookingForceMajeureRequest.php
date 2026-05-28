<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BookingForceMajeureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action' => ['required', 'string', 'in:reschedule,refund'],
            'reason' => ['required', 'string', 'max:600'],
            'new_date' => ['required_if:action,reschedule', 'nullable', 'date'],
        ];
    }

    public function isReschedule(): bool
    {
        return ($this->validated()['action'] ?? '') === 'reschedule';
    }

    public function reason(): string
    {
        return trim((string) ($this->validated()['reason'] ?? ''));
    }

    public function newDate(): string
    {
        return trim((string) ($this->validated()['new_date'] ?? ''));
    }
}
