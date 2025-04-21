<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users'],
            'first_name' => ['required'],
            'last_name' => ['required'],
            'gender' => ['required'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
