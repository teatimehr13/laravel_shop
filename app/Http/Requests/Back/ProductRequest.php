<?php

namespace App\Http\Requests\Back;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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
            'image' => 'image|nullable',
            'description' => 'string|nullable',
            'published_status' => [
                'required',
                Rule::in(array_keys(Product::published_statuses)) //限定值在自定義範圍內
            ],
            'subcategory_id' => 'required|integer|exists:subcategories,id',
            'product_options' => 'nullable|array' 
        ];
    }
}
