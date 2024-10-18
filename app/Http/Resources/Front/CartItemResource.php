<?php

namespace App\Http\Resources\Front;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
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
            // 'productOption' => new ProductOptionResource($this->productOption),
            'productOption' => [
                'id' => $this->resource['productOption']['id'],
                'name' => $this->resource['productOption']['name'],
                'price' => $this->resource['productOption']['price'],
                'image' => $this->resource['productOption']['image'],
                'description' => $this->resource['productOption']['description'],

            ], // 存取關聯陣列的 key
            'quantity' => $this->resource['quantity'],
        ];
    }
}
