<?php

namespace App\Http\Resources\Back;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'name' => $this->name,
            'image' => $this->image,
            'description' => $this->description,
            'published_status' => $this->published_status,
            'published_status_name' => $this->published_status_name(),
            'subcategory_id' => $this->subcategory_id,
            'subcategory_name' => $this->subcategory->name,
            'product_options' => 'nullable|array'
        ];
    }
}
