<?php

namespace App\Http\Requests;

use App\Enums\TodoStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TodoRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'min:3', 'max:50'],
            'description' => ['required', 'min:3', 'max:150'],
            'status' => ['required', 'lowercase', Rule::in(TodoStatusEnum::getValues())],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
