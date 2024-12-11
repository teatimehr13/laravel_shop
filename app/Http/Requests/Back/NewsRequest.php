<?php

namespace App\Http\Requests\Back;

use Illuminate\Foundation\Http\FormRequest;

class NewsRequest extends FormRequest
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
            'title' => 'required|string|',
            'date' => 'nullable|date',
            'image' => 'image|nullable',
            'description' => 'nullable|string|',
            'is_enabled' => 'required|boolean',
            'news_type' => 'required|integer',
        ];
    }
}
