<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\VillaDesign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VillaDesignController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $categoryKey = $request->query('category', '');
        $type = $request->query('type', '');

        $query = VillaDesign::query()->orderBy('sort_order')->latest('id');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title_en', 'like', "%{$search}%")
                  ->orWhere('title_ar', 'like', "%{$search}%")
                  ->orWhere('tagline', 'like', "%{$search}%")
                  ->orWhere('location_tag', 'like', "%{$search}%");
            });
        }

        if (!empty($categoryKey)) {
            $query->where('category_key', $categoryKey);
        }

        if (!empty($type)) {
            $query->where('type', $type);
        }

        $designs = $query->paginate(12)->withQueryString();

        // Distinct categories for filter pills
        $availableCategories = VillaDesign::select('category_key', 'category_name_en', 'category_name_ar')
            ->distinct()
            ->get();

        $stats = [
            'total' => VillaDesign::count(),
            'fittings' => VillaDesign::where('type', 'fitting')->count(),
            'sofas' => VillaDesign::where('type', 'sofa')->count(),
            'active' => VillaDesign::where('is_active', true)->count(),
        ];

        return view('seller.villa_designs.index', compact('designs', 'availableCategories', 'stats', 'search', 'categoryKey', 'type'));
    }

    public function create()
    {
        $defaultCategories = VillaDesign::defaultCategories();
        $existingCategories = VillaDesign::select('category_key', 'category_name_en', 'category_name_ar')
            ->distinct()
            ->get();

        return response()
            ->view('seller.villa_designs.create', compact('defaultCategories', 'existingCategories'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title_en'           => 'required|string|max:255',
            'title_ar'           => 'required|string|max:255',
            'type'               => 'required|in:fitting,sofa',
            'category_key'       => 'required|string|max:100',
            'category_name_en'   => 'nullable|string|max:255',
            'category_name_ar'   => 'nullable|string|max:255',
            'location_tag'       => 'required|string|max:255',
            'tagline'            => 'required|string',
            'image_file'         => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,avif|max:12288',
            'demo_photo_url'     => 'nullable|string',
            'detail_image_file'  => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,avif|max:12288',
            'price_sar'          => 'required|numeric|min:0',
            'price_usd'          => 'nullable|numeric|min:0',
            'advance_deposit_sar'=> 'nullable|numeric|min:0',
            'advance_deposit_usd'=> 'nullable|numeric|min:0',
            'features'           => 'nullable|array',
            'features.*'         => 'nullable|string|max:255',
            'dimensions'         => 'nullable|string|max:255',
            'finish_or_fabric'   => 'nullable|string|max:255',
            'core_material'      => 'nullable|string|max:255',
            'hardware'           => 'nullable|string|max:255',
            // Studio config preset attributes
            'preset_finish'      => 'nullable|string|max:100',
            'preset_glass'       => 'nullable|string|max:100',
            'preset_layout'      => 'nullable|string|max:100',
            'preset_fabric'      => 'nullable|string|max:100',
            'preset_leg'         => 'nullable|string|max:100',
        ]);

        // ── Primary Photo (File Upload from Computer) ───────────────────────
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('villa-designs', 'public');
            $photoUrl = Storage::url($path);
        } elseif (!empty($request->input('demo_photo_url'))) {
            $photoUrl = $request->input('demo_photo_url');
        } else {
            return back()->withErrors(['image_file' => 'Please select and upload a Showcase Photo from your computer.'])->withInput();
        }

        // ── Detail Photo (Optional File Upload) ─────────────────────────────
        $detailPhotoUrl = null;
        if ($request->hasFile('detail_image_file')) {
            $dPath = $request->file('detail_image_file')->store('villa-designs/details', 'public');
            $detailPhotoUrl = Storage::url($dPath);
        }

        // ── Category Names ──────────────────────────────────────────────────
        $defaults = VillaDesign::defaultCategories();
        $catKey = Str::slug($validated['category_key'], '_');
        $catEn = $validated['category_name_en'] ?? ($defaults[$catKey]['name_en'] ?? ucwords(str_replace('_', ' ', $catKey)));
        $catAr = $validated['category_name_ar'] ?? ($defaults[$catKey]['name_ar'] ?? $catEn);

        // ── Prices & 40% Deposit ────────────────────────────────────────────
        $priceSar = (float)$validated['price_sar'];
        $priceUsd = !empty($validated['price_usd']) ? (float)$validated['price_usd'] : round($priceSar / 3.75, 2);
        $depositSar = !empty($validated['advance_deposit_sar']) ? (float)$validated['advance_deposit_sar'] : round($priceSar * 0.40, 2);
        $depositUsd = !empty($validated['advance_deposit_usd']) ? (float)$validated['advance_deposit_usd'] : round($priceUsd * 0.40, 2);

        // ── Features Array (clean empty rows) ────────────────────────────────
        $features = array_values(array_filter($request->input('features', []), function ($f) {
            return !empty(trim($f ?? ''));
        }));

        if (empty($features)) {
            $features = [
                'Engineered to SASO Climate Standards',
                'Handcrafted Saudi Villa Architectural Finishing',
                'Custom Fabricated with 10-Year Craftsmanship Guarantee',
            ];
        }

        // ── Specs Object ────────────────────────────────────────────────────
        $specs = [
            'dimensions'      => $validated['dimensions'] ?? 'Custom Dimensions Available',
            'finishOrFabric'  => $validated['finish_or_fabric'] ?? 'Architectural Custom Grade',
            'coreMaterial'    => $validated['core_material'] ?? 'Structural Grade Alloy / Hardwood',
            'hardware'        => $validated['hardware'] ?? 'Concealed Heavy-Duty Hardware',
        ];

        // ── Config Preset Data for Simulator ────────────────────────────────
        if ($validated['type'] === 'fitting') {
            $configData = [
                'finishId'     => $request->input('preset_finish', 'champagne_gold'),
                'glassId'      => $request->input('preset_glass', 'fluted_ribbed'),
                'heightInches' => 96,
                'widthInches'  => 72,
                'gauge'        => '2.0mm',
                'mullionStyle' => 'grid_3x2',
                'addons'       => ['acoustic_seal' => true, 'hydraulic_damper' => true, 'thermal_barrier' => false],
            ];
        } else {
            $configData = [
                'layoutId'    => $request->input('preset_layout', 'grand_modular'),
                'fabricId'    => $request->input('preset_fabric', 'cognac_leather'),
                'legId'       => $request->input('preset_leg', 'black'),
                'seatDepth'   => 'deep_lounge',
                'cushionCore' => 'down_blend',
            ];
        }

        // ── Generate Unique Slug ────────────────────────────────────────────
        $slug = Str::slug($validated['title_en']);
        $originalSlug = $slug;
        $counter = 1;
        while (VillaDesign::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        $design = VillaDesign::create([
            'slug'                => $slug,
            'type'                => $validated['type'],
            'category_key'        => $catKey,
            'category_name_en'    => $catEn,
            'category_name_ar'    => $catAr,
            'title_en'            => $validated['title_en'],
            'title_ar'            => $validated['title_ar'],
            'tagline'             => $validated['tagline'],
            'location_tag'        => $validated['location_tag'],
            'photo_url'           => $photoUrl,
            'detail_photo_url'    => $detailPhotoUrl,
            'price_sar'           => $priceSar,
            'price_usd'           => $priceUsd,
            'advance_deposit_sar' => $depositSar,
            'advance_deposit_usd' => $depositUsd,
            'features'            => $features,
            'specs'               => $specs,
            'config_data'         => $configData,
            'sort_order'          => 0,
            'is_active'           => true,
        ]);

        return redirect()->route('seller.villa-designs.index')
            ->with('success', "Villa Design '{$design->title_en}' successfully published to the storefront showcase!");
    }

    public function edit(VillaDesign $villaDesign)
    {
        $defaultCategories = VillaDesign::defaultCategories();
        $existingCategories = VillaDesign::select('category_key', 'category_name_en', 'category_name_ar')
            ->distinct()
            ->get();

        return response()
            ->view('seller.villa_designs.edit', compact('villaDesign', 'defaultCategories', 'existingCategories'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }

    public function update(Request $request, VillaDesign $villaDesign)
    {
        $validated = $request->validate([
            'title_en'           => 'required|string|max:255',
            'title_ar'           => 'required|string|max:255',
            'type'               => 'required|in:fitting,sofa',
            'category_key'       => 'required|string|max:100',
            'category_name_en'   => 'nullable|string|max:255',
            'category_name_ar'   => 'nullable|string|max:255',
            'location_tag'       => 'required|string|max:255',
            'tagline'            => 'required|string',
            'image_file'         => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,avif|max:12288',
            'detail_image_file'  => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,avif|max:12288',
            'price_sar'          => 'required|numeric|min:0',
            'price_usd'          => 'nullable|numeric|min:0',
            'advance_deposit_sar'=> 'nullable|numeric|min:0',
            'advance_deposit_usd'=> 'nullable|numeric|min:0',
            'features'           => 'nullable|array',
            'features.*'         => 'nullable|string|max:255',
            'dimensions'         => 'nullable|string|max:255',
            'finish_or_fabric'   => 'nullable|string|max:255',
            'core_material'      => 'nullable|string|max:255',
            'hardware'           => 'nullable|string|max:255',
            'is_active'          => 'nullable|boolean',
        ]);

        // ── Primary Photo (Upload Replacement or Keep Existing) ─────────────
        if ($request->hasFile('image_file')) {
            if ($villaDesign->photo_url && str_contains($villaDesign->photo_url, '/storage/')) {
                $oldPath = str_replace('/storage/', '', parse_url($villaDesign->photo_url, PHP_URL_PATH));
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image_file')->store('villa-designs', 'public');
            $photoUrl = Storage::url($path);
        } else {
            $photoUrl = $villaDesign->photo_url;
        }

        // ── Detail Photo (Upload Replacement or Keep Existing) ──────────────
        if ($request->hasFile('detail_image_file')) {
            if ($villaDesign->detail_photo_url && str_contains($villaDesign->detail_photo_url, '/storage/')) {
                $oldDPath = str_replace('/storage/', '', parse_url($villaDesign->detail_photo_url, PHP_URL_PATH));
                Storage::disk('public')->delete($oldDPath);
            }
            $dPath = $request->file('detail_image_file')->store('villa-designs/details', 'public');
            $detailPhotoUrl = Storage::url($dPath);
        } else {
            $detailPhotoUrl = $villaDesign->detail_photo_url;
        }

        // ── Category Names ──────────────────────────────────────────────────
        $defaults = VillaDesign::defaultCategories();
        $catKey = Str::slug($validated['category_key'], '_');
        $catEn = $validated['category_name_en'] ?? ($defaults[$catKey]['name_en'] ?? ucwords(str_replace('_', ' ', $catKey)));
        $catAr = $validated['category_name_ar'] ?? ($defaults[$catKey]['name_ar'] ?? $catEn);

        // ── Prices ──────────────────────────────────────────────────────────
        $priceSar = (float)$validated['price_sar'];
        $priceUsd = !empty($validated['price_usd']) ? (float)$validated['price_usd'] : round($priceSar / 3.75, 2);
        $depositSar = !empty($validated['advance_deposit_sar']) ? (float)$validated['advance_deposit_sar'] : round($priceSar * 0.40, 2);
        $depositUsd = !empty($validated['advance_deposit_usd']) ? (float)$validated['advance_deposit_usd'] : round($priceUsd * 0.40, 2);

        // ── Features ────────────────────────────────────────────────────────
        $features = array_values(array_filter($request->input('features', []), function ($f) {
            return !empty(trim($f ?? ''));
        }));
        if (empty($features)) {
            $features = $villaDesign->features ?? [];
        }

        // ── Specs ───────────────────────────────────────────────────────────
        $specs = [
            'dimensions'     => $validated['dimensions'] ?? ($villaDesign->specs['dimensions'] ?? ''),
            'finishOrFabric' => $validated['finish_or_fabric'] ?? ($villaDesign->specs['finishOrFabric'] ?? ''),
            'coreMaterial'   => $validated['core_material'] ?? ($villaDesign->specs['coreMaterial'] ?? ''),
            'hardware'       => $validated['hardware'] ?? ($villaDesign->specs['hardware'] ?? ''),
        ];

        $villaDesign->update([
            'type'                => $validated['type'],
            'category_key'        => $catKey,
            'category_name_en'    => $catEn,
            'category_name_ar'    => $catAr,
            'title_en'            => $validated['title_en'],
            'title_ar'            => $validated['title_ar'],
            'tagline'             => $validated['tagline'],
            'location_tag'        => $validated['location_tag'],
            'photo_url'           => $photoUrl,
            'detail_photo_url'    => $detailPhotoUrl,
            'price_sar'           => $priceSar,
            'price_usd'           => $priceUsd,
            'advance_deposit_sar' => $depositSar,
            'advance_deposit_usd' => $depositUsd,
            'features'            => $features,
            'specs'               => $specs,
            'is_active'           => $request->has('is_active'),
        ]);

        return redirect()->route('seller.villa-designs.index')
            ->with('success', "Villa Design '{$villaDesign->title_en}' updated successfully.");
    }

    public function destroy(VillaDesign $villaDesign)
    {
        if ($villaDesign->photo_url && str_contains($villaDesign->photo_url, '/storage/')) {
            $oldPath = str_replace('/storage/', '', parse_url($villaDesign->photo_url, PHP_URL_PATH));
            Storage::disk('public')->delete($oldPath);
        }
        if ($villaDesign->detail_photo_url && str_contains($villaDesign->detail_photo_url, '/storage/')) {
            $oldDPath = str_replace('/storage/', '', parse_url($villaDesign->detail_photo_url, PHP_URL_PATH));
            Storage::disk('public')->delete($oldDPath);
        }

        $title = $villaDesign->title_en;
        $villaDesign->delete();

        return redirect()->route('seller.villa-designs.index')
            ->with('success', "Villa Design '{$title}' removed from storefront showcase.");
    }
}
