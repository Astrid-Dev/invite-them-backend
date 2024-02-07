<?php

namespace App\Http\Requests;

use App\Models\Table;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreTableRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        Gate::authorize('create', [Table::class, $this->route('eventId')]);
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
            'tables' => ['required', 'array', 'min:1'],
            'tables.*.name' => [
                'required',
                'string',
                'between:1,20',
                Rule::unique('tables', 'name')->where('event_id', $this->route('eventId'))
            ],
            'tables.*.capacity' => ['required', 'integer', 'between:1,20'],
        ];
    }
}
