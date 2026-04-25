<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SentoReviewStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'visited_at' => ['required', 'date'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'body' => ['nullable', 'string', 'max:5000'],
            'has_sauna' => ['boolean'],
            'sauna_temp' => ['nullable', 'integer', 'between:30,150'],
            'has_mizuburo' => ['boolean'],
            'mizuburo_temp' => ['nullable', 'integer', 'between:0,40'],
            'bath_types' => ['nullable', 'array'],
            'bath_types.*' => ['string', 'max:30'],
            'want_revisit' => ['boolean'],
            'crowding' => ['nullable', 'integer', 'between:1,5'],
            'best_time' => ['nullable', 'string', 'max:50'],
        ];
    }
}
