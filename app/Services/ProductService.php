<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    /**
     * Get paginated products with filtering and sorting.
     */
    public function getFilteredProducts(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $query = Product::with('category');

        if (!empty($filters['category'])) {
            $query->whereHas('category', function (Builder $q) use ($filters) {
                $q->where('slug', $filters['category']);
            });
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('tagline', 'like', "%{$search}%")
                  ->orWhere('materials', 'like', "%{$search}%");
            });
        }

        if (isset($filters['featured']) && filter_var($filters['featured'], FILTER_VALIDATE_BOOLEAN)) {
            $query->where('is_featured', true);
        }

        if (!empty($filters['product_type'])) {
            $query->where('product_type', $filters['product_type']);
        }

        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', (float) $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', (float) $filters['max_price']);
        }

        // Sorting
        $sort = $filters['sort'] ?? null;
        if ($sort) {
            match ($sort) {
                'price_asc' => $query->orderBy('price', 'asc'),
                'price_desc' => $query->orderBy('price', 'desc'),
                'rating' => $query->orderBy('rating', 'desc'),
                'popular' => $query->orderBy('reviews_count', 'desc'),
                default => $query->latest(),
            };
        } else {
            $query->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
        }

        return $query->paginate($perPage);
    }

    /**
     * Get featured products.
     */
    public function getFeaturedProducts(int $limit = 6): Collection
    {
        return Product::with('category')
            ->where('is_featured', true)
            ->take($limit)
            ->get();
    }

    /**
     * Find product by slug or ID with category.
     */
    public function findBySlug(string $slug): Product
    {
        $query = Product::with('category');
        if (is_numeric($slug)) {
            $product = $query->where('id', (int) $slug)->orWhere('slug', $slug)->first();
        } else {
            $product = $query->where('slug', $slug)->first();
        }

        if (!$product) {
            abort(404, "Product with identifier '{$slug}' not found.");
        }

        return $product;
    }
}
