<?php

namespace App\Http\Requests\Admin\Package;

use App\Models\Package;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePackageRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'price' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'status_id' => [
                'required',
                'integer',
                Rule::exists('references', 'id')->where(function ($query): void {
                    $query
                        ->where('delete_status', false)
                        ->where('group_id', 'package_status');
                }),
            ],
            'package_type' => [
                'required',
                'integer',
                Rule::exists('references', 'id')->where(function ($query): void {
                    $query
                        ->where('delete_status', false)
                        ->where('group_id', 'package_type');
                }),
            ],
            'thumbnail' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'remove_thumbnail' => ['nullable', 'boolean'],
            'benefits' => ['nullable', 'array'],
            'benefits.*' => ['string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'benefits.*.max' => 'Setiap benefit maksimal 500 karakter.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return [
            'name' => trim((string) $this->input('name')),
            'description' => trim((string) $this->input('description')) ?: null,
            'price' => (float) $this->input('price'),
            'status_id' => (int) $this->input('status_id'),
            'package_type' => (int) $this->input('package_type'),
            'benefits' => array_values(array_filter(
                array_map(static fn (string $b): string => trim($b), $this->input('benefits', [])),
                static fn (string $b): bool => $b !== ''
            )),
            'has_thumbnail' => $this->hasFile('thumbnail'),
            'remove_thumbnail' => (bool) $this->input('remove_thumbnail', false),
        ];
    }
}
