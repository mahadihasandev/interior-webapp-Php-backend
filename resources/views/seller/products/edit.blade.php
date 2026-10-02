@extends('layouts.seller')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{
    categoryId: '{{ old('category_id', $product->category_id) }}',
    subcategories: {{ json_encode($subcategories) }},
    previewUrl: '{{ $product->image_url }}',
    newFileChosen: false,
    galleryPreviews: [],
    name: '{{ old('name', $product->name) }}',
    tagline: '{{ old('tagline', $product->tagline) }}',
    price: '{{ old('price', $product->price) }}',
    comparePrice: '{{ old('compare_at_price', $product->compare_at_price) }}',
    stock: '{{ old('stock', $product->stock) }}',
    dragging: false,
    filteredSubcategories() {
        if (!this.categoryId) return [];
        return this.subcategories.filter(s => s.category_id == this.categoryId);
    },
    handleMainFile(event) {
        const file = event.target.files[0] || (event.dataTransfer && event.dataTransfer.files[0]);
        if (!file) return;
        this.dragging = false;
        this.newFileChosen = true;
        const reader = new FileReader();
        reader.onload = (e) => { this.previewUrl = e.target.result; };
        reader.readAsDataURL(file);
        if (event.dataTransfer) {
            const dt = new DataTransfer();
            dt.items.add(file);
            document.getElementById('image_file_input').files = dt.files;
        }
    },
    handleGalleryFiles(event) {
        const files = event.target.files;
        this.galleryPreviews = [];
        for (let i = 0; i < files.length && i < 8; i++) {
            const reader = new FileReader();
            reader.onload = (e) => { this.galleryPreviews.push(e.target.result); };
            reader.readAsDataURL(files[i]);
        }
    },
    productType: '{{ old('product_type', $product->product_type ?? 'ready_made') }}',
    minPrice: '{{ old('min_price', $product->customization_options['min_price'] ?? $product->price) }}',
    maxPrice: '{{ old('max_price', $product->customization_options['max_price'] ?? ($product->compare_at_price ?? $product->price)) }}',
    defaultHeight: '{{ old('default_height', $product->customization_options['default_height'] ?? 180) }}',
    defaultWidth: '{{ old('default_width', $product->customization_options['default_width'] ?? 140) }}',
    measurementUnit: '{{ old('measurement_unit', $product->customization_options['measurement_unit'] ?? 'cm') }}'
}">

    {{-- Top Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-stone-200">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-amber-800 mb-1">
                <a href="{{ route('seller.products.index') }}" class="hover:underline">Catalog</a>
                <span>/</span>
                <span>Edit #{{ $product->id }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-stone-900 tracking-tight">
                Edit: {{ Str::limit($product->name, 50) }}
            </h1>
            <p class="text-xs text-stone-500 mt-1">Upload a new photo to replace the current one, or leave it unchanged.</p>
        </div>
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-xl border
                {{ $product->stock > 0 ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-rose-50 border-rose-200 text-rose-800' }}">
                <span class="w-2 h-2 rounded-full {{ $product->stock > 0 ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                {{ $product->stock > 0 ? $product->stock.' units in stock' : 'Out of Stock' }}
            </span>
        </div>
    </div>

    {{-- Error Summary --}}
    @if ($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 text-sm">
            <p class="font-bold mb-1">Please fix the following:</p>
            <ul class="list-disc list-inside text-xs space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('seller.products.update', $product->id) }}" enctype="multipart/form-data"
          class="space-y-6">
        @csrf
        @method('PUT')

        {{-- ═══════════════════════════════════════════════════════
             SECTION 1 — PHOTO UPLOAD
        ══════════════════════════════════════════════════════════ --}}
        <div class="bg-white rounded-3xl border border-stone-200 p-6 sm:p-8 shadow-sm space-y-5">
            <h2 class="text-sm font-bold uppercase tracking-wider text-stone-900 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-stone-900 text-white text-xs flex items-center justify-center font-bold">1</span>
                Product Photos · صور المنتج
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Main Photo --}}
                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-700">
                        Main Photo
                        <template x-if="newFileChosen">
                            <span class="ml-1 text-amber-700 font-bold">(New file selected ↑)</span>
                        </template>
                        <template x-if="!newFileChosen">
                            <span class="ml-1 text-stone-400 font-normal">(Current — click to replace)</span>
                        </template>
                    </label>

                    <div
                        class="relative border-2 rounded-2xl overflow-hidden transition-all duration-200 cursor-pointer"
                        :class="dragging ? 'border-amber-400 bg-amber-50 scale-[1.01]' : (newFileChosen ? 'border-amber-400' : 'border-stone-300 hover:border-stone-500')"
                        @dragover.prevent="dragging = true"
                        @dragleave.prevent="dragging = false"
                        @drop.prevent="handleMainFile($event)"
                        @click="$refs.mainInput.click()"
                    >
                        {{-- Photo Preview --}}
                        <div class="aspect-4/3 bg-stone-100">
                            <img :src="previewUrl" alt="Product Photo"
                                 class="w-full h-full object-cover"
                                 onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=600&q=80';">
                        </div>

                        {{-- Overlay --}}
                        <div class="absolute inset-0 bg-stone-900/0 hover:bg-stone-900/40 transition-all flex items-center justify-center">
                            <div class="opacity-0 hover:opacity-100 transition-opacity">
                                <div class="bg-white/90 backdrop-blur-sm rounded-2xl px-5 py-3 flex items-center gap-2 shadow-lg">
                                    <svg class="w-4 h-4 text-stone-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01"/>
                                    </svg>
                                    <span class="text-xs font-bold text-stone-800">Click or drag to replace photo</span>
                                </div>
                            </div>
                        </div>

                        <input
                            id="image_file_input"
                            name="image_file"
                            type="file"
                            accept="image/jpeg,image/png,image/webp,image/gif"
                            class="hidden"
                            x-ref="mainInput"
                            @change="handleMainFile($event)"
                        >
                    </div>

                    <p class="text-[11px] text-stone-400">JPG, PNG, or WebP · Max 8MB · Leave blank to keep current photo.</p>
                </div>

                {{-- Gallery Photos --}}
                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-700">
                        Add More Gallery Photos
                    </label>

                    {{-- Existing gallery --}}
                    @if(!empty($product->gallery) && count($product->gallery) > 0)
                        <div class="grid grid-cols-4 gap-1.5 mb-2">
                            @foreach($product->gallery as $gUrl)
                                <div class="aspect-square rounded-xl overflow-hidden bg-stone-100 border border-stone-200">
                                    <img src="{{ $gUrl }}" alt="Gallery" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=400&q=80';">
                                </div>
                            @endforeach
                        </div>
                        <p class="text-[11px] text-stone-500 mb-2">↑ Existing gallery ({{ count($product->gallery) }} photos). New uploads will be added to these.</p>
                    @endif

                    <div
                        class="relative border-2 border-dashed border-stone-300 bg-stone-50 hover:border-stone-400 rounded-2xl transition-all duration-200 min-h-[130px] cursor-pointer"
                        @click="$refs.galleryInput.click()"
                    >
                        <template x-if="galleryPreviews.length === 0">
                            <div class="flex flex-col items-center justify-center py-8 px-6 text-center">
                                <svg class="w-8 h-8 text-stone-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/>
                                </svg>
                                <p class="text-xs font-bold text-stone-600">Upload new gallery photos</p>
                                <p class="text-[11px] text-stone-400 mt-0.5">They'll be added alongside existing ones</p>
                            </div>
                        </template>
                        <template x-if="galleryPreviews.length > 0">
                            <div class="p-3 grid grid-cols-4 gap-1.5">
                                <template x-for="(src, idx) in galleryPreviews" :key="idx">
                                    <div class="aspect-square rounded-xl overflow-hidden bg-stone-200 border-2 border-amber-300">
                                        <img :src="src" class="w-full h-full object-cover">
                                    </div>
                                </template>
                            </div>
                        </template>

                        <input
                            name="gallery_files[]"
                            type="file"
                            accept="image/jpeg,image/png,image/webp,image/gif"
                            multiple
                            class="hidden"
                            x-ref="galleryInput"
                            @change="handleGalleryFiles($event)"
                        >
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════
             SECTION 2 — Identity
        ════════════════════════════ --}}
        <div class="bg-white rounded-3xl border border-stone-200 p-6 sm:p-8 shadow-sm space-y-4">
            <h2 class="text-sm font-bold uppercase tracking-wider text-stone-900 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-stone-900 text-white text-xs flex items-center justify-center font-bold">2</span>
                Product Identity & Branding
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">Product Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" x-model="name" required
                        class="w-full px-3.5 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900 focus:outline-none">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">Tagline / Highlight</label>
                    <input type="text" name="tagline" x-model="tagline"
                        class="w-full px-3.5 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">Category <span class="text-rose-500">*</span></label>
                    <select name="category_id" x-model="categoryId" required
                        class="w-full px-3.5 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900 focus:outline-none">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">Subcategory</label>
                    <select name="subcategory_id"
                        class="w-full px-3.5 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900 focus:outline-none">
                        <option value="">— Optional —</option>
                        <template x-for="sub in filteredSubcategories()" :key="sub.id">
                            <option :value="sub.id" :selected="sub.id == {{ $product->subcategory_id ?? 'null' }}" x-text="sub.name"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">Brand / Studio</label>
                    <select name="brand_id"
                        class="w-full px-3.5 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900 focus:outline-none">
                        <option value="">— In-House Atelier Studio —</option>
                        @foreach($brands as $b)
                            <option value="{{ $b->id }}" {{ $product->brand_id == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center">
                    <label class="inline-flex items-center gap-2.5 cursor-pointer mt-5">
                        <input type="checkbox" name="is_featured" value="1" {{ $product->is_featured ? 'checked' : '' }}
                            class="w-4 h-4 rounded text-stone-900 border-stone-300 focus:ring-stone-900">
                        <div>
                            <span class="text-xs font-bold text-stone-800 block">Pin to Featured Editions</span>
                            <span class="text-[11px] text-stone-400">Shown on homepage</span>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════
             PRODUCT TYPE SELECTOR
        ════════════════════════════ --}}
        <div class="bg-amber-50/60 rounded-3xl border border-amber-200/80 p-5 sm:p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-900 block">Catalog Mode · نوع المنتج</span>
                    <p class="text-xs text-stone-600 mt-0.5">Select whether this is a ready-made item or a custom-made architectural order with customer specifications.</p>
                </div>
                <div class="flex items-center p-1 bg-white border border-stone-200 rounded-2xl gap-1 shrink-0">
                    <button type="button" @click="productType = 'ready_made'"
                        :class="productType === 'ready_made' ? 'bg-stone-900 text-white shadow-sm' : 'text-stone-600 hover:text-stone-900'"
                        class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5">
                        📦 Ready-Made Edition
                    </button>
                    <button type="button" @click="productType = 'custom_fit'"
                        :class="productType === 'custom_fit' ? 'bg-amber-800 text-white shadow-sm' : 'text-stone-600 hover:text-stone-900'"
                        class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5">
                        🪟 Bespoke Made-to-Measure
                    </button>
                </div>
                <input type="hidden" name="product_type" :value="productType">
            </div>
        </div>

        {{-- ═══════════════════════════
             SECTION 3 — Pricing & Range
        ════════════════════════════ --}}
        <div class="bg-white rounded-3xl border border-stone-200 p-6 sm:p-8 shadow-sm space-y-4">
            <h2 class="text-sm font-bold uppercase tracking-wider text-stone-900 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-stone-900 text-white text-xs flex items-center justify-center font-bold">3</span>
                <span x-text="productType === 'custom_fit' ? 'Price Range & Order Capacity (e.g. 800 to 1000 SAR)' : 'Pricing & Inventory · الأسعار والمخزون'"></span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">
                        <span x-text="productType === 'custom_fit' ? 'Min Price / Starting (SAR)' : 'Retail Price (SAR)'"></span>
                        <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-sm text-stone-500 font-bold">﷼</span>
                        <input type="number" step="0.01" name="price" x-model="price" required
                            class="w-full pl-8 pr-3 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900 focus:outline-none font-bold">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">
                        <span x-text="productType === 'custom_fit' ? 'Max Price Range (SAR)' : 'Compare At (SAR)'"></span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-sm text-stone-500 font-bold">﷼</span>
                        <input type="number" step="0.01" name="compare_at_price" x-model="comparePrice"
                            class="w-full pl-8 pr-3 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900 focus:outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">Stock Units <span class="text-rose-500">*</span></label>
                    <input type="number" name="stock" x-model="stock" required min="0"
                        class="w-full px-3.5 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900 focus:outline-none">
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════
             SECTION 3B — Made-to-Measure Custom Specifications
        ════════════════════════════ --}}
        <div x-show="productType === 'custom_fit'" x-transition class="bg-white rounded-3xl border-2 border-amber-300/80 p-6 sm:p-8 shadow-sm space-y-6">
            <div class="flex items-center justify-between pb-3 border-b border-stone-200">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-800 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/></svg>
                        Customer Specification Form Settings
                    </span>
                    <h3 class="text-lg font-serif font-bold text-stone-900">Custom Order Parameters</h3>
                    <p class="text-xs text-stone-500">Configure default dimensions, shutters count, aluminum profiles, and glass colors that customer can choose on the custom details page.</p>
                </div>
                <span class="px-3 py-1 bg-amber-100 text-amber-900 text-[11px] font-bold rounded-full">Interactive Customizer</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">Default Height</label>
                    <input type="number" name="default_height" x-model="defaultHeight" placeholder="180"
                        class="w-full px-3.5 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-amber-800 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">Default Width</label>
                    <input type="number" name="default_width" x-model="defaultWidth" placeholder="140"
                        class="w-full px-3.5 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-amber-800 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">Measurement Unit</label>
                    <select name="measurement_unit" x-model="measurementUnit"
                        class="w-full px-3.5 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-amber-800 focus:outline-none font-bold">
                        <option value="cm">Centimeters (cm)</option>
                        <option value="mm">Millimeters (mm)</option>
                        <option value="inch">Inches (in)</option>
                    </select>
                </div>
            </div>

            {{-- Specification Options Preview Badges --}}
            <div class="p-4 rounded-2xl bg-stone-50 border border-stone-200 space-y-3">
                <span class="text-xs font-bold text-stone-700 block">Default Options Enabled for Customer Form:</span>
                <div class="flex flex-wrap gap-2 text-xs">
                    <span class="px-3 py-1 bg-white border border-stone-300 rounded-lg text-stone-800 font-medium">🪟 Shutters: 1 Fixed, 2 Sliding, 3 Tri-Slide, 4 Bi-Fold</span>
                    <span class="px-3 py-1 bg-white border border-stone-300 rounded-lg text-stone-800 font-medium">🛡 Aluminum: Alupco Thermal 2.0mm, Royal Gulf 2.5mm, Slim 1.8mm</span>
                    <span class="px-3 py-1 bg-white border border-stone-300 rounded-lg text-stone-800 font-medium">🪞 Glass: Reflective Bronze, Low-E Clear, Smoky Grey, Frosted</span>
                    <span class="px-3 py-1 bg-white border border-stone-300 rounded-lg text-stone-800 font-medium">⚙ Addons: Stainless Fly Screen, Multi-Lock, Motorized</span>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════
             SECTION 4 — Specs & Description
        ════════════════════════════ --}}
        <div class="bg-white rounded-3xl border border-stone-200 p-6 sm:p-8 shadow-sm space-y-4">
            <h2 class="text-sm font-bold uppercase tracking-wider text-stone-900 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-stone-900 text-white text-xs flex items-center justify-center font-bold">4</span>
                Specifications & Description
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">Dimensions</label>
                    <input type="text" name="dimensions" value="{{ old('dimensions', $product->dimensions) }}"
                        class="w-full px-3.5 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">Materials</label>
                    <input type="text" name="materials" value="{{ old('materials', $product->materials) }}"
                        class="w-full px-3.5 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">Color / Finish</label>
                    <input type="text" name="color" value="{{ old('color', $product->color) }}"
                        class="w-full px-3.5 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">Full Description <span class="text-rose-500">*</span></label>
                <textarea name="description" required rows="4"
                    class="w-full px-3.5 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900 focus:outline-none">{{ old('description', $product->description) }}</textarea>
            </div>
        </div>

        {{-- Submit Bar --}}
        <div class="flex flex-wrap items-center justify-between gap-4 bg-white rounded-2xl border border-stone-200 px-6 py-4 shadow-sm">
            <div class="flex items-center gap-2">
                <a href="{{ route('seller.products.index') }}"
                   class="px-5 py-2.5 bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs font-bold rounded-xl transition-colors">
                    ← Cancel
                </a>
                <button type="button"
                    onclick="if(confirm('Are you sure you want to permanently delete this product: {{ addslashes($product->name) }}?')) { document.getElementById('delete-product-form').submit(); }"
                    class="px-4 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-xl transition-colors border border-rose-200 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    <span>Delete Product</span>
                </button>
            </div>
            <button type="submit"
                class="px-8 py-3 bg-stone-900 hover:bg-stone-800 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-md transition-all hover:scale-105 active:scale-95 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Save Changes
            </button>
        </div>
    </form>

    <form id="delete-product-form" method="POST" action="{{ route('seller.products.destroy', $product->id) }}" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection
