<?php

namespace App\Http\Requests\Back;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\ProductOption;

class ProductOptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return $this->authorize('store', ProductOption::class);
        // return auth()->user()->hasRole('admin');
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
            'color_name' => 'required|string',
            'color_code' => 'required|string',
            'image' => 'image|nullable',
            'description' => 'string|nullable',
            'price' => 'string',
            'published_status' => [
                'required',
                // Rule::in(array_keys(Product::published_statuses)) //限定值在自定義範圍內
            ],
            'product_id' => 'required|integer|exists:products,id',
            'enable' => 'required|integer'
            // 'product_options' => 'nullable|array' //放productOptionsRequest那
        ];
    }
}
