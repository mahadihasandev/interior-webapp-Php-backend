<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomArchitecturalProductController extends Controller
{
    /**
     * Display all Custom Architectural Products with serial management, edit, and delete.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search', '');

        $query = Product::with(['category', 'brand'])
            ->where('product_type', 'custom_fit')
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('tagline', 'like', "%{$search}%")
                  ->orWhere('materials', 'like', "%{$search}%")
                  ->orWhere('color', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => Product::where('product_type', 'custom_fit')->count(),
            'featured' => Product::where('product_type', 'custom_fit')->where('is_featured', true)->count(),
            'in_stock' => Product::where('product_type', 'custom_fit')->where('stock', '>', 0)->count(),
        ];

        return view('seller.custom_products.index', compact('products', 'search', 'stats'));
    }

    /**
     * Update individual product serial / sort_order.
     */
    public function updateSerial(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $request->validate([
            'sort_order' => 'required|integer|min:1|max:9999',
        ]);

        $product->update([
            'sort_order' => (int) $request->input('sort_order'),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Serial updated to #{$product->sort_order} for {$product->name}",
                'product_id' => $product->id,
                'sort_order' => $product->sort_order,
            ]);
        }

        return redirect()->back()->with('success', "Serial updated to #{$product->sort_order} for '{$product->name}'");
    }

    /**
     * Bulk save all serial numbers at once.
     */
    public function reorder(Request $request): RedirectResponse
    {
        $serials = $request->input('serials', []);

        if (is_array($serials)) {
            foreach ($serials as $id => $order) {
                Product::where('id', $id)
                    ->where('product_type', 'custom_fit')
                    ->update(['sort_order' => (int) $order]);
            }
        }

        return redirect()->back()->with('success', 'Custom Architectural Products serial display order saved successfully.');
    }

    /**
     * Delete a custom architectural product.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $name = $product->name;
        $product->delete();

        return redirect()->route('seller.custom_products.index')
            ->with('success', "Custom Architectural Product '{$name}' deleted successfully.");
    }
}
