<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'subcategory_id',
        'brand_id',
        'name',
        'slug',
        'tagline',
        'description',
        'price',
        'compare_at_price',
        'dimensions',
        'materials',
        'color',
        'stock',
        'image_url',
        'gallery',
        'is_featured',
        'rating',
        'reviews_count',
    ];

    protected $casts = [
        'price' => 'float',
        'compare_at_price' => 'float',
        'gallery' => 'array',
        'is_featured' => 'boolean',
        'rating' => 'float',
        'stock' => 'integer',
        'reviews_count' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
