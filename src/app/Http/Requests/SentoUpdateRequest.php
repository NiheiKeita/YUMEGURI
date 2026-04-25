<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Domain\Enum\SentoStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SentoUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'name_kana' => ['nullable', 'string', 'max:255'],
            'prefecture' => ['sometimes', 'string', 'max:50'],
            'city' => ['nullable', 'string', 'max:100'],
            'address' => ['sometimes', 'string', 'max:255'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'phone' => ['nullable', 'string', 'max:50'],
            'hours' => ['nullable', 'string', 'max:255'],
            'closed_days' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'integer', 'min:0'],
            'nearest_station' => ['nullable', 'string', 'max:100'],
            'walk_minutes' => ['nullable', 'integer', 'min:0'],
            'has_shampoo' => ['boolean'],
            'has_soap' => ['boolean'],
            'status' => ['nullable', Rule::enum(SentoStatus::class)],
        ];
    }
}
