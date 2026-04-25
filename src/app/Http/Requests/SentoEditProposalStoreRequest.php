<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SentoEditProposalStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'changes' => ['required', 'array', 'min:1'],
            'changes.name' => ['nullable', 'string', 'max:255'],
            'changes.address' => ['nullable', 'string', 'max:255'],
            'changes.phone' => ['nullable', 'string', 'max:50'],
            'changes.hours' => ['nullable', 'string', 'max:255'],
            'changes.closed_days' => ['nullable', 'string', 'max:255'],
            'changes.price' => ['nullable', 'integer', 'min:0'],
            'changes.nearest_station' => ['nullable', 'string', 'max:100'],
            'changes.walk_minutes' => ['nullable', 'integer', 'min:0'],
            'changes.has_shampoo' => ['nullable', 'boolean'],
            'changes.has_soap' => ['nullable', 'boolean'],
            'reason' => ['nullable', 'string', 'max:500'],
        ];
    }
}
