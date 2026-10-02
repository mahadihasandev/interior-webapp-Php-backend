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
        'vendor_id',
        'sku',
        'product_type',
        'customization_options',
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
        'sort_order',
        'rating',
        'reviews_count',
    ];

    protected $casts = [
        'price' => 'float',
        'compare_at_price' => 'float',
        'gallery' => 'array',
        'customization_options' => 'array',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
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

    /**
     * Dynamically normalize the primary product image URL across environments.
     */
    public function getImageUrlAttribute(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        // Fix legacy dead domain from initial deployment
        if (str_contains($value, 'interior-webapp-php.onrender.com')) {
            $value = str_replace(
                'interior-webapp-php.onrender.com',
                'interior-webapp-php-backend.onrender.com',
                $value
            );
        }

        // If stored as relative local storage path, generate full URL
        if (str_starts_with($value, '/storage/')) {
            return url($value);
        }
        if (str_starts_with($value, 'storage/')) {
            return url('/' . $value);
        }

        return $value;
    }

    /**
     * Dynamically normalize gallery image URLs.
     */
    public function getGalleryAttribute($value): array
    {
        $gallery = is_string($value) ? json_decode($value, true) : $value;
        if (!is_array($gallery)) {
            return [];
        }

        return array_values(array_map(function ($url) {
            if (empty($url) || !is_string($url)) return $url;
            if (str_contains($url, 'interior-webapp-php.onrender.com')) {
                $url = str_replace(
                    'interior-webapp-php.onrender.com',
                    'interior-webapp-php-backend.onrender.com',
                    $url
                );
            }
            if (str_starts_with($url, '/storage/')) {
                return url($url);
            }
            if (str_starts_with($url, 'storage/')) {
                return url('/' . $url);
            }
            return $url;
        }, $gallery));
    }
}

