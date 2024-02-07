<?php

namespace App\Http\Requests;

use App\Models\Event;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $event = Event::query()
            ->where('user_id', auth('api')->id())
            ->findOrFail($this->route('eventId'));
        Gate::authorize('update', $event);

        $this->request->set('event', $event);
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
            'name' => [
                'required',
                'string',
                'between:3,155',
                Rule::unique('events', 'name')
                    ->ignore($this->route('eventId'))
                    ->where('user_id', auth('api')->id())
            ],
            'date' => ['sometimes', 'date', 'after_or_equal:today'],
        ];
    }
}
