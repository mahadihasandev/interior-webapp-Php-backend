<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TaxonomyController extends Controller
{
    // CATEGORIES & SUBCATEGORIES
    public function categories()
    {
        $categories = Category::withCount('products')->with(['subcategories' => function ($q) {
            $q->withCount('products');
        }])->get();

        return view('seller.categories.index', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'description' => 'nullable|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:8192',
            'image_url' => 'nullable|url',
            'icon' => 'nullable|string|max:50',
        ]);

        $imageUrl = $validated['image_url'] ?? null;
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('categories', 'public');
            $imageUrl = \Illuminate\Support\Facades\Storage::url($path);
        }

        $slug = Str::slug($validated['name']);

        Category::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'image_url' => $imageUrl ?: 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&w=1000&q=80',
            'icon' => $validated['icon'] ?? 'Layers',
        ]);

        return redirect()->back()->with('success', "Category '{$validated['name']}' created successfully.");
    }

    public function storeSubcategory(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        $slug = Str::slug($validated['name']);

        Subcategory::updateOrCreate(
            ['category_id' => $validated['category_id'], 'slug' => $slug],
            [
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]
        );

        return redirect()->back()->with('success', "Subcategory '{$validated['name']}' added successfully.");
    }

    // BRANDS / STUDIOS
    public function brands()
    {
        $brands = Brand::withCount('products')->latest()->get();
        return view('seller.brands.index', compact('brands'));
    }

    public function storeBrand(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120|unique:brands,name',
            'origin_country' => 'nullable|string|max:100',
            'logo_url' => 'nullable|url',
            'description' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
        ]);

        $slug = Str::slug($validated['name']);

        Brand::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'origin_country' => $validated['origin_country'] ?? 'Global Studio',
            'logo_url' => $validated['logo_url'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_featured' => $request->has('is_featured'),
        ]);

        return redirect()->back()->with('success', "Brand / Studio '{$validated['name']}' registered successfully.");
    }
}
