<?php

namespace App\Http\Requests;

use App\Models\Scanner;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreScannerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        Gate::authorize('create', [Scanner::class, $this->route('eventId')]);
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $excludeIds = Scanner::query()
            ->where('event_id', $this->route('eventId'))
            ->pluck('user_id')
            ->toArray();
        $excludeIds[] = auth()->id();
        return [
            'ids' => ['required_without:pseudo', 'array', 'min:1'],
            'ids.*' => [
                'required',
                'string',
                Rule::exists('users', 'id')
                    ->whereNotIn('id', $excludeIds),
            ],
            'pseudo' => ['required_without:ids', 'string', 'between:3,20', 'unique:users'],
            'email' => ['required_without:ids', 'email', 'max:100', 'unique:users'],
        ];
    }
}
