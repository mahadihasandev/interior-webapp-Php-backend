<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $minPrice = (float) ($this->customization_options['min_price'] ?? $this->price);
        $maxPrice = (float) ($this->compare_at_price ?: ($this->customization_options['max_price'] ?? $minPrice));

        return [
            'id' => $this->id,
            'vendor_id' => $this->vendor_id,
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
            'product_type' => $this->product_type ?? 'ready_made',
            'customization_options' => $this->customization_options,
            'price_min' => $minPrice,
            'price_max' => $maxPrice,
            'price_range_formatted' => $maxPrice > $minPrice
                ? number_format($minPrice, 0) . ' – ' . number_format($maxPrice, 0) . ' SAR'
                : number_format($minPrice, 0) . ' SAR',
            'rating' => (float) $this->rating,
            'reviews_count' => (int) $this->reviews_count,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
