<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class InviteCollaboratorRequest extends FormRequest
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
        return [
            'email' => ['required', 'string', 'lowercase', 'email', 'max:50', 'min:5', 'unique:todo_invites,email'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email is required',
            'email.string' => 'Email must be a string',
            'email.lowercase' => 'Email must be lowercase',
            'email.email' => 'Email must be a valid email address',
            'email.max' => 'Email must not exceed 50 characters',
            'email.min' => 'Email must be at least 5 characters',
            'email.unique' => 'User invited to this todo, pending confirmation',
        ];
    }
}
