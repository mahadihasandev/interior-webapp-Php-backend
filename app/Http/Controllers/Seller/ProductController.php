<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $categoryId = $request->query('category_id', '');
        $brandId = $request->query('brand_id', '');

        $query = Product::with(['category', 'subcategory', 'brand'])->latest();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('tagline', 'like', "%{$search}%")
                  ->orWhere('materials', 'like', "%{$search}%")
                  ->orWhere('color', 'like', "%{$search}%");
            });
        }

        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        if (!empty($brandId)) {
            $query->where('brand_id', $brandId);
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::with('subcategories')->get();
        $brands = Brand::all();

        return view('seller.products.index', compact('products', 'categories', 'brands', 'search', 'categoryId', 'brandId'));
    }

    public function create()
    {
        $categories = Category::with('subcategories')->get();
        $brands = Brand::all();
        $subcategories = Subcategory::all();

        return view('seller.products.create', compact('categories', 'brands', 'subcategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'category_id'     => 'required|exists:categories,id',
            'subcategory_id'  => 'nullable|exists:subcategories,id',
            'brand_id'        => 'nullable|exists:brands,id',
            'tagline'         => 'nullable|string|max:255',
            'description'     => 'required|string',
            'price'           => 'required|numeric|min:0',
            'compare_at_price'=> 'nullable|numeric|min:0',
            'dimensions'      => 'nullable|string|max:255',
            'materials'       => 'nullable|string|max:255',
            'color'           => 'nullable|string|max:255',
            'stock'           => 'required|integer|min:0',
            // Accept EITHER an uploaded file OR a fallback URL
            'image_file'      => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:8192',
            'image_url'       => 'nullable|url|max:2048',
            'gallery_files'   => 'nullable|array|max:10',
            'gallery_files.*' => 'image|mimes:jpeg,png,jpg,webp,gif|max:8192',
            'is_featured'     => 'nullable|boolean',
        ]);

        // ── Primary Image ─────────────────────────────────────────────────
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('products', 'public');
            $imageUrl = Storage::url($path);
        } elseif (!empty($validated['image_url'])) {
            $imageUrl = $validated['image_url'];
        } else {
            return back()->withErrors(['image_file' => 'Please upload a product photo or provide an image URL.'])->withInput();
        }

        // ── Gallery Images ────────────────────────────────────────────────
        $gallery = [];
        if ($request->hasFile('gallery_files')) {
            foreach ($request->file('gallery_files') as $file) {
                $gPath = $file->store('products/gallery', 'public');
                $gallery[] = Storage::url($gPath);
            }
        }

        // ── Slug ──────────────────────────────────────────────────────────
        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $counter = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        $product = Product::create([
            'category_id'     => $validated['category_id'],
            'subcategory_id'  => $validated['subcategory_id'] ?? null,
            'brand_id'        => $validated['brand_id'] ?? null,
            'name'            => $validated['name'],
            'slug'            => $slug,
            'tagline'         => $validated['tagline'] ?? null,
            'description'     => $validated['description'],
            'price'           => $validated['price'],
            'compare_at_price'=> $validated['compare_at_price'] ?? null,
            'dimensions'      => $validated['dimensions'] ?? null,
            'materials'       => $validated['materials'] ?? null,
            'color'           => $validated['color'] ?? null,
            'stock'           => $validated['stock'],
            'image_url'       => $imageUrl,
            'gallery'         => $gallery,
            'is_featured'     => $request->has('is_featured'),
            'rating'          => 5.0,
            'reviews_count'   => 0,
        ]);

        return redirect()->route('seller.products.index')
            ->with('success', "Product '{$product->name}' was successfully added to the ready-made catalog!");
    }

    public function edit(Product $product)
    {
        $categories = Category::with('subcategories')->get();
        $brands = Brand::all();
        $subcategories = Subcategory::where('category_id', $product->category_id)->get();

        return view('seller.products.edit', compact('product', 'categories', 'brands', 'subcategories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'category_id'     => 'required|exists:categories,id',
            'subcategory_id'  => 'nullable|exists:subcategories,id',
            'brand_id'        => 'nullable|exists:brands,id',
            'tagline'         => 'nullable|string|max:255',
            'description'     => 'required|string',
            'price'           => 'required|numeric|min:0',
            'compare_at_price'=> 'nullable|numeric|min:0',
            'dimensions'      => 'nullable|string|max:255',
            'materials'       => 'nullable|string|max:255',
            'color'           => 'nullable|string|max:255',
            'stock'           => 'required|integer|min:0',
            'image_file'      => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:8192',
            'image_url'       => 'nullable|url|max:2048',
            'gallery_files'   => 'nullable|array|max:10',
            'gallery_files.*' => 'image|mimes:jpeg,png,jpg,webp,gif|max:8192',
            'is_featured'     => 'nullable|boolean',
        ]);

        // ── Primary Image ─────────────────────────────────────────────────
        if ($request->hasFile('image_file')) {
            // Delete old uploaded image if it's a local storage path
            if ($product->image_url && str_contains($product->image_url, '/storage/')) {
                $oldPath = str_replace('/storage/', '', parse_url($product->image_url, PHP_URL_PATH));
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image_file')->store('products', 'public');
            $imageUrl = Storage::url($path);
        } elseif (!empty($validated['image_url'])) {
            $imageUrl = $validated['image_url'];
        } else {
            // Keep existing image
            $imageUrl = $product->image_url;
        }

        // ── Gallery Images ────────────────────────────────────────────────
        $gallery = $product->gallery ?? [];
        if ($request->hasFile('gallery_files')) {
            foreach ($request->file('gallery_files') as $file) {
                $gPath = $file->store('products/gallery', 'public');
                $gallery[] = Storage::url($gPath);
            }
        }

        $product->update([
            'category_id'     => $validated['category_id'],
            'subcategory_id'  => $validated['subcategory_id'] ?? null,
            'brand_id'        => $validated['brand_id'] ?? null,
            'name'            => $validated['name'],
            'tagline'         => $validated['tagline'] ?? null,
            'description'     => $validated['description'],
            'price'           => $validated['price'],
            'compare_at_price'=> $validated['compare_at_price'] ?? null,
            'dimensions'      => $validated['dimensions'] ?? null,
            'materials'       => $validated['materials'] ?? null,
            'color'           => $validated['color'] ?? null,
            'stock'           => $validated['stock'],
            'image_url'       => $imageUrl,
            'gallery'         => $gallery,
            'is_featured'     => $request->has('is_featured'),
        ]);

        return redirect()->route('seller.products.index')
            ->with('success', "Product '{$product->name}' updated successfully.");
    }

    public function destroy(Product $product)
    {
        // Clean up uploaded files
        if ($product->image_url && str_contains($product->image_url, '/storage/')) {
            $oldPath = str_replace('/storage/', '', parse_url($product->image_url, PHP_URL_PATH));
            Storage::disk('public')->delete($oldPath);
        }
        if (!empty($product->gallery)) {
            foreach ($product->gallery as $gUrl) {
                if (str_contains($gUrl, '/storage/')) {
                    $gPath = str_replace('/storage/', '', parse_url($gUrl, PHP_URL_PATH));
                    Storage::disk('public')->delete($gPath);
                }
            }
        }

        $name = $product->name;
        $product->delete();

        return redirect()->route('seller.products.index')
            ->with('success', "Product '{$name}' has been deleted.");
    }
}
