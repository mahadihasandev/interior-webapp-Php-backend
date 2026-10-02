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
        $fallback = 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80';

        if (empty($value) || !is_string($value) || trim($value) === '') {
            return $fallback;
        }

        $value = trim($value);

        // Normalize full URLs pointing to storage back to clean relative paths
        if (preg_match('#https?://[^/]+(/storage/.+)#i', $value, $matches)) {
            $value = $matches[1];
        }

        // Fix legacy dead domain from initial deployment for any other assets
        if (str_contains($value, 'interior-webapp-php.onrender.com')) {
            $value = str_replace(
                'interior-webapp-php.onrender.com',
                'interior-webapp-php-backend.onrender.com',
                $value
            );
        }

        // Handle local storage paths
        if (str_starts_with($value, '/storage/') || str_starts_with($value, 'storage/')) {
            $relPath = '/' . ltrim($value, '/');
            $diskSubPath = preg_replace('#^/storage/#', '', $relPath);
            $fullDiskPath = storage_path('app/public/' . $diskSubPath);

            // If file does not exist on disk, return high-quality architectural fallback
            if (!file_exists($fullDiskPath)) {
                return $fallback;
            }

            // In web dashboard requests, root-relative URL (/storage/...) avoids HTTPS/port issues
            if (!request()->is('api/*')) {
                return $relPath;
            }

            return url($relPath);
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

        $fallback = 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80';

        return array_values(array_map(function ($url) use ($fallback) {
            if (empty($url) || !is_string($url) || trim($url) === '') return $fallback;
            $url = trim($url);

            if (preg_match('#https?://[^/]+(/storage/.+)#i', $url, $matches)) {
                $url = $matches[1];
            }

            if (str_contains($url, 'interior-webapp-php.onrender.com')) {
                $url = str_replace(
                    'interior-webapp-php.onrender.com',
                    'interior-webapp-php-backend.onrender.com',
                    $url
                );
            }

            if (str_starts_with($url, '/storage/') || str_starts_with($url, 'storage/')) {
                $relPath = '/' . ltrim($url, '/');
                $diskSubPath = preg_replace('#^/storage/#', '', $relPath);
                $fullDiskPath = storage_path('app/public/' . $diskSubPath);

                if (!file_exists($fullDiskPath)) {
                    return $fallback;
                }

                if (!request()->is('api/*')) {
                    return $relPath;
                }

                return url($relPath);
            }

            return $url;
        }, $gallery));
    }
}

