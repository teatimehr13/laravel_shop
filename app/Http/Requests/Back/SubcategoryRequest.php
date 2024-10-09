<?php

namespace App\Http\Requests\Back;

use Illuminate\Foundation\Http\FormRequest;

class SubcategoryRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'search_key' => 'required|string|regex:/^[a-zA-Z0-9_]+$/i|unique:subcategories,search_key,'.$this->subcategory,
            'order_index' => 'required|integer|min:1|max:9999',
            'show_in_list' => 'required|boolean'
        ];
    }
}
