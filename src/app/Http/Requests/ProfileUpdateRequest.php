<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $routeUser = $this->route('user');
        $authUser = $this->user();

        return $routeUser instanceof User
            && $authUser instanceof User
            && $routeUser->id === $authUser->id;
    }

    /**
     * @return array<string, list<\Illuminate\Contracts\Validation\Rule|string>>
     */
    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user instanceof User ? $user->id : null),
            ],
            'tel' => ['nullable', 'string', 'max:30'],
        ];
    }
}
