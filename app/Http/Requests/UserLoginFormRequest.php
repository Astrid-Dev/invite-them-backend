<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserLoginFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return !auth('api')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'pseudo' => ['required', 'string', 'between:3,20'],
            'password' => ['required', 'string', 'between:6,20'],
            'event_code' => ['sometimes', 'nullable', 'string', 'between:6,6', 'exists:events,code']
        ];
    }
}
