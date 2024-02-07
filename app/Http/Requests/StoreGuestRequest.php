<?php

namespace App\Http\Requests;

use App\Models\Guest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreGuestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        Gate::authorize('create', [Guest::class, $this->route('eventId')]);
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'guests' => ['required', 'array'],
            'guests.*.name' => ['required', 'string', 'max:30'],
            'guests.*.hint' => ['sometimes', 'nullable', 'string', 'max:20'],
            'guests.*.seats' => ['required', 'integer', 'between:1,2'],
            'guests.*.email' => ['sometimes', 'email', 'nullable', 'max:100'],
            'guests.*.whatsapp' => [
                'sometimes',
                'string',
//                'regex:/(^(?:\+|0{0,2})\d{1,4}[-\s./]?\d{1,14}$)/u',
                'nullable',
            ],
            'guests.*.table_id' => [
                'sometimes',
                'nullable',
                'string',
                Rule::exists('tables', 'id')->where('event_id', $this->route('eventId'))
            ],
        ];
    }
}
