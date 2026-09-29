<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {}

    /**
     * Get paginated products with filters (category, search, sort, price, featured).
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'category',
            'search',
            'featured',
            'product_type',
            'min_price',
            'max_price',
            'sort',
        ]);

        $perPage = (int) $request->input('per_page', 12);
        $products = $this->productService->getFilteredProducts($filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($products->items()),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    /**
     * Get featured products for homepage.
     */
    public function featured(Request $request): JsonResponse
    {
        $limit = (int) $request->input('limit', 6);
        $products = $this->productService->getFeaturedProducts($limit);

        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($products),
        ]);
    }

    /**
     * Get single product details by slug.
     */
    public function show(string $slug): JsonResponse
    {
        $product = $this->productService->findBySlug($slug);

        return response()->json([
            'success' => true,
            'data' => new ProductResource($product),
        ]);
    }
}
