<?php

namespace App\Http\Resources\Back;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class ProductOptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        // Log::info($this->color_name);
        return [
            'color_name' => $this->color_name,
            'color_code' => $this->color_code,
            'image' => $this->image,
            'price' => $this->price,
            'enable' => $this->enable,
            'published_status' => $this->published_status
        ];
    }
}
