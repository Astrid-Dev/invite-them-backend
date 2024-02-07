<?php

namespace App\Http\Requests;

use App\Models\Guest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateGuestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $guest = Guest::query()
            ->where('event_id', $this->route('eventId'))
            ->findOrFail($this->route('guestId'));

        Gate::authorize('update', $guest);

        $this->request->set('guest', $guest);
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
            'name' => ['sometimes', 'string', 'max:30'],
            'hint' => ['sometimes', 'nullable', 'string', 'max:20'],
            'seats' => ['sometimes', 'integer', 'between:1,2'],
            'email' => ['sometimes', 'email', 'nullable', 'max:100'],
            'whatsapp' => ['sometimes', 'string', 'nullable'],
            'table_id' => [
                'sometimes',
                'nullable',
                'string',
                Rule::exists('tables', 'id')->where('event_id', $this->route('eventId'))
            ],
        ];
    }
}
