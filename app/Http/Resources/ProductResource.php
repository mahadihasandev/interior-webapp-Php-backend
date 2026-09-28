<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'name' => $this->name,
            'slug' => $this->slug,
            'tagline' => $this->tagline,
            'description' => $this->description,
            'price' => (float) $this->price,
            'compare_at_price' => $this->compare_at_price ? (float) $this->compare_at_price : null,
            'dimensions' => $this->dimensions,
            'materials' => $this->materials,
            'color' => $this->color,
            'stock' => (int) $this->stock,
            'in_stock' => $this->stock > 0,
            'image_url' => $this->image_url,
            'gallery' => $this->gallery ?? [],
            'is_featured' => (bool) $this->is_featured,
            'rating' => (float) $this->rating,
            'reviews_count' => (int) $this->reviews_count,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
