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
            'title' => 'string|nullable',
            'price' => 'string',
            'published_status' => [
                'required',
                Rule::in(array_keys(Product::published_statuses)) //限定值在自定義範圍內
            ],
            'subcategory_id' => 'required|integer|exists:subcategories,id',
            'description' => 'nullable|string',
            'special_message' => 'nullable|string',
            'special_start_at' => 'nullable|date',
            'special_end_at' => 'nullable|date|after_or_equal:special_start_at'
            // 'product_options' => 'nullable|array' //放productOptionsRequest那
        ];
    }

    protected function prepareForValidation()
    {
        // logger()->info('Before:', $this->all());
        //把傳來的'null' 改成null
        $this->merge([
            'special_start_at' => $this->special_start_at == 'null' ? null : $this->special_start_at,
            'special_end_at' => $this->special_end_at == 'null' ? null : $this->special_end_at,
        ]);
    }
}
