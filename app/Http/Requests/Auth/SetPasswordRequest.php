<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class SetPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $user = auth()->user();

        $rules = [
            'password' => [
                'required',
                'confirmed',
                'required_with:password_confirmation',
                'same:password_confirmation',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'password_confirmation' => [
                'required',
                'same:password',
            ],
        ];

        if (! is_null($user->password)) {
            $rules = array_merge($rules, ['current_password' => ['required', 'current_password']]);
        }

        return $rules;
    }
}
