<?php

namespace App\Http\Resources\Back;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'order_index' => $this->order_index,
            'show_in_list' => $this->show_in_list,
            'search_key' => $this->search_key,
            'subcategory' => SubcategoryResource::collection($this->whenLoaded('subcategories'))
        ];
    }
}
