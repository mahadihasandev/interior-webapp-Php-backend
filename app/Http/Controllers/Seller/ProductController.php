<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\Vendor;
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
        $productType = $request->query('product_type', '');

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

        if (!empty($productType)) {
            $query->where('product_type', $productType);
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::with('subcategories')->get();
        $brands = Brand::all();

        return view('seller.products.index', compact('products', 'categories', 'brands', 'search', 'categoryId', 'brandId', 'productType'));
    }

    public function create()
    {
        $categories = Category::with('subcategories')->get();
        $brands = Brand::all();
        $subcategories = Subcategory::all();

        return view('seller.products.create', compact('categories', 'brands', 'subcategories'));
    }

    public function createCustom()
    {
        $categories = Category::with('subcategories')->get();
        $brands = Brand::all();
        $subcategories = Subcategory::all();

        return view('seller.products.create_custom', compact('categories', 'brands', 'subcategories'));
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
            'product_type'    => 'nullable|in:ready_made,custom_fit',
            'default_height'  => 'nullable|numeric|min:1',
            'default_width'   => 'nullable|numeric|min:1',
            'measurement_unit'=> 'nullable|string|max:20',
            'min_price'       => 'nullable|numeric|min:0',
            'max_price'       => 'nullable|numeric|min:0',
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

        // ── Custom Fit & Made-to-Measure Options ───────────────────────────
        $productType = $request->input('product_type', 'ready_made');
        $customizationOptions = null;
        if ($productType === 'custom_fit' || $request->has('is_custom_fit')) {
            $productType = 'custom_fit';
            $customizationOptions = $this->buildCustomizationOptions($request, $validated);
        }

        $product = Product::create([
            'vendor_id'            => $request->user()?->vendor_id ?? Vendor::first()?->id,
            'category_id'          => $validated['category_id'],
            'subcategory_id'       => $validated['subcategory_id'] ?? null,
            'brand_id'             => $validated['brand_id'] ?? null,
            'name'                 => $validated['name'],
            'slug'                 => $slug,
            'tagline'              => $validated['tagline'] ?? null,
            'description'          => $validated['description'],
            'product_type'         => $productType,
            'customization_options'=> $customizationOptions,
            'price'                => $validated['price'],
            'compare_at_price'     => $validated['compare_at_price'] ?? null,
            'dimensions'           => $validated['dimensions'] ?? null,
            'materials'            => $validated['materials'] ?? null,
            'color'                => $validated['color'] ?? null,
            'stock'                => $validated['stock'],
            'image_url'            => $imageUrl,
            'gallery'              => $gallery,
            'is_featured'          => $request->has('is_featured'),
            'rating'               => 5.0,
            'reviews_count'        => 0,
        ]);

        return redirect()->route('seller.products.index')
            ->with('success', "Product '{$product->name}' was successfully added!");
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
            'product_type'    => 'nullable|in:ready_made,custom_fit',
            'default_height'  => 'nullable|numeric|min:1',
            'default_width'   => 'nullable|numeric|min:1',
            'measurement_unit'=> 'nullable|string|max:20',
            'min_price'       => 'nullable|numeric|min:0',
            'max_price'       => 'nullable|numeric|min:0',
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

        // ── Custom Fit & Made-to-Measure Options ───────────────────────────
        $productType = $request->input('product_type', $product->product_type ?? 'ready_made');
        $customizationOptions = $product->customization_options;
        if ($productType === 'custom_fit' || $request->has('is_custom_fit')) {
            $productType = 'custom_fit';
            $customizationOptions = $this->buildCustomizationOptions($request, $validated);
        } elseif ($request->input('product_type') === 'ready_made') {
            $productType = 'ready_made';
            $customizationOptions = null;
        }

        $product->update([
            'category_id'          => $validated['category_id'],
            'subcategory_id'       => $validated['subcategory_id'] ?? null,
            'brand_id'             => $validated['brand_id'] ?? null,
            'name'                 => $validated['name'],
            'tagline'              => $validated['tagline'] ?? null,
            'description'          => $validated['description'],
            'product_type'         => $productType,
            'customization_options'=> $customizationOptions,
            'price'                => $validated['price'],
            'compare_at_price'     => $validated['compare_at_price'] ?? null,
            'dimensions'           => $validated['dimensions'] ?? null,
            'materials'            => $validated['materials'] ?? null,
            'color'                => $validated['color'] ?? null,
            'stock'                => $validated['stock'],
            'image_url'            => $imageUrl,
            'gallery'              => $gallery,
            'is_featured'          => $request->has('is_featured'),
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

    /**
     * Build rich customization options for made-to-measure products.
     */
    protected function buildCustomizationOptions(Request $request, array $validated): array
    {
        $minPrice = (float) ($request->input('min_price') ?: $validated['price']);
        $maxPrice = (float) ($request->input('max_price') ?: ($validated['compare_at_price'] ?? ($minPrice * 1.25)));

        // Shutters options
        $rawShutters = $request->input('shutters_options');
        $shutters = is_array($rawShutters) ? $rawShutters : [
            ['id' => '1_fixed', 'name' => '1 Fixed Pane', 'description' => 'Panoramic view', 'price_delta' => 0],
            ['id' => '2_sliding', 'name' => '2 Shutters (Sliding / Casement)', 'description' => 'Standard dual track', 'price_delta' => 80],
            ['id' => '3_sliding', 'name' => '3 Shutters (Tri-Rail Panoramic)', 'description' => 'Wide sliding opening', 'price_delta' => 150],
            ['id' => '4_bifold', 'name' => '4 Shutters (Quad Multi-Slide / Bi-Fold)', 'description' => 'Maximum clearance', 'price_delta' => 220],
        ];

        // Aluminum profile options
        $rawAluminum = $request->input('aluminum_options');
        $aluminum = is_array($rawAluminum) ? $rawAluminum : [
            ['id' => 'alupco_2_0', 'name' => 'Alupco Thermal Break 2.0mm', 'badge' => 'SASO 50°C Rated', 'thickness' => '2.0mm', 'price_delta' => 0],
            ['id' => 'royal_2_5', 'name' => 'Royal Gulf Heavy Duty 2.5mm', 'badge' => 'High Wind Resistance', 'thickness' => '2.5mm', 'price_delta' => 100],
            ['id' => 'slim_1_8', 'name' => 'Ultra-Slim Minimalist Line 1.8mm', 'badge' => 'Architectural Aesthetic', 'thickness' => '1.8mm', 'price_delta' => 60],
            ['id' => 'std_1_5', 'name' => 'Standard Extruded Alloy 1.5mm', 'badge' => 'Interior Grade', 'thickness' => '1.5mm', 'price_delta' => -50],
        ];

        // Glass color / type options
        $rawGlass = $request->input('glass_options');
        $glass = is_array($rawGlass) ? $rawGlass : [
            ['id' => 'bronze_refl', 'name' => 'Double Glazed Reflective Bronze', 'tint' => '#8c6239', 'specs' => '24mm (6+12A+6)', 'price_delta' => 0],
            ['id' => 'low_e_clear', 'name' => 'Clear Low-E Acoustic Double Glass', 'tint' => '#d6eaf8', 'specs' => '38dB soundproof', 'price_delta' => 50],
            ['id' => 'tinted_grey', 'name' => 'Smoky Tinted Grey Sun-Shield', 'tint' => '#4a4a4a', 'specs' => 'Anti-glare 85% heat block', 'price_delta' => 40],
            ['id' => 'frosted_privacy', 'name' => 'Frosted Acid-Etched Privacy', 'tint' => '#e5e7eb', 'specs' => '100% privacy diffused', 'price_delta' => 30],
            ['id' => 'triple_acoustic', 'name' => 'Triple-Glazed Extreme Acoustic', 'tint' => '#aed6f1', 'specs' => '42dB Royal Majlis rating', 'price_delta' => 140],
        ];

        // Frame finish colors
        $rawColors = $request->input('color_options');
        $colors = is_array($rawColors) ? $rawColors : [
            ['id' => 'black', 'name' => 'Matte Architectural Black', 'hex' => '#1e1e1e'],
            ['id' => 'gold', 'name' => 'Champagne Gold / Bronze', 'hex' => '#c5a059'],
            ['id' => 'anthracite', 'name' => 'Metallic Anthracite Charcoal', 'hex' => '#3b3e40'],
            ['id' => 'sand_white', 'name' => 'Desert Sand Warm White', 'hex' => '#f4ede2'],
        ];

        // Addons
        $rawAddons = $request->input('addons');
        $addons = is_array($rawAddons) ? $rawAddons : [
            ['id' => 'fly_screen', 'name' => 'Stainless Steel Insect / Fly Screen', 'price' => 120, 'selected' => true],
            ['id' => 'german_lock', 'name' => 'German Multi-Point Security Lock', 'price' => 180, 'selected' => false],
            ['id' => 'motorized', 'name' => 'Motorized Shutter Automation Ready', 'price' => 450, 'selected' => false],
            ['id' => 'dust_seal', 'name' => 'Hermetic Sandstorm Dust Weatherseal', 'price' => 0, 'selected' => true],
        ];

        return [
            'min_price'        => $minPrice,
            'max_price'        => $maxPrice,
            'default_height'   => (float) ($request->input('default_height') ?: 180),
            'default_width'    => (float) ($request->input('default_width') ?: 140),
            'min_height'       => (float) ($request->input('min_height') ?: 60),
            'max_height'       => (float) ($request->input('max_height') ?: 320),
            'min_width'        => (float) ($request->input('min_width') ?: 60),
            'max_width'        => (float) ($request->input('max_width') ?: 450),
            'measurement_unit' => $request->input('measurement_unit', 'cm'),
            'shutters_options' => $shutters,
            'aluminum_options' => $aluminum,
            'glass_options'    => $glass,
            'color_options'    => $colors,
            'addons'           => $addons,
        ];
    }
}
