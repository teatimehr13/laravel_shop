<?php

namespace App\Http\Requests\Back;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'store_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'opening_hours' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string',
            'image' => 'image|nullable',
            'is_enabled' => 'required|boolean',
            'store_type' => 'required|integer',
        ];
    }
}