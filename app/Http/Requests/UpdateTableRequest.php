<?php

namespace App\Http\Requests;

use App\Models\Table;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateTableRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $table = Table::query()
            ->where('event_id', $this->route('eventId'))
            ->findOrFail($this->route('tableId'));

        Gate::authorize('update', $table);

        $this->request->set('table', $table);
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
                'sometimes',
                'string',
                'between:1,20',
                Rule::unique('tables', 'name')
                    ->ignore($this->route('tableId'))
                    ->where('event_id', $this->route('eventId'))
            ],
            'capacity' => ['sometimes', 'integer', 'between:1,20'],
        ];
    }
}
