@extends('layouts.seller')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{
    categoryId: '{{ old('category_id', $categories->first()->id ?? '') }}',
    subcategories: {{ json_encode($subcategories) }},
    previewUrl: null,
    galleryPreviews: [],
    name: '{{ old('name', '') }}',
    tagline: '{{ old('tagline', '') }}',
    price: '{{ old('price', '') }}',
    comparePrice: '{{ old('compare_at_price', '') }}',
    stock: '{{ old('stock', '10') }}',
    dragging: false,
    filteredSubcategories() {
        if (!this.categoryId) return [];
        return this.subcategories.filter(s => s.category_id == this.categoryId);
    },
    handleMainFile(event) {
        const file = event.target.files[0] || (event.dataTransfer && event.dataTransfer.files[0]);
        if (!file) return;
        this.dragging = false;
        const reader = new FileReader();
        reader.onload = (e) => { this.previewUrl = e.target.result; };
        reader.readAsDataURL(file);
        if (event.dataTransfer) {
            // Move file to the real input
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
    productType: '{{ old('product_type', 'ready_made') }}',
    minPrice: '{{ old('min_price', '800.00') }}',
    maxPrice: '{{ old('max_price', '1000.00') }}',
    defaultHeight: '{{ old('default_height', '180') }}',
    defaultWidth: '{{ old('default_width', '140') }}',
    measurementUnit: '{{ old('measurement_unit', 'cm') }}',
    prefillDemo(type) {
        if (type === 'majlis') {
            this.productType = 'ready_made';
            this.name = 'Al-Rawdah Royal Majlis L-Sofa';
            this.tagline = 'Gold-braided ivory chenille, 8-seater modular Majlis configuration';
            this.price = '4800.00';
            this.comparePrice = '5500.00';
            this.stock = '5';
        } else if (type === 'custom_window' || type === 'window') {
            this.productType = 'custom_fit';
            this.name = 'Thermal Break Double-Glazed Sliding Window';
            this.tagline = '50°C SASO Thermal Barrier · 38dB Acoustic Isolation · Made to Measure';
            this.price = '800.00';
            this.comparePrice = '1000.00';
            this.minPrice = '800.00';
            this.maxPrice = '1000.00';
            this.defaultHeight = '180';
            this.defaultWidth = '140';
            this.stock = '50';
        }
    }
}">

    {{-- Top Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-stone-200">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-amber-800 mb-1">
                <a href="{{ route('seller.products.index') }}" class="hover:underline">Catalog</a>
                <span>/</span>
                <span>New Edition</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-stone-900 tracking-tight">Add Product to Catalog</h1>
            <p class="text-xs text-stone-500 mt-1">Upload real product photos — JPG, PNG, or WebP, max 8MB each.</p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <span class="text-xs text-stone-400 font-medium hidden sm:inline">Quick fill:</span>
            <button type="button" @click="prefillDemo('majlis')" class="px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-900 text-xs font-bold rounded-xl border border-amber-200 transition-colors">
                🛋 Majlis Sofa
            </button>
            <button type="button" @click="prefillDemo('window')" class="px-3 py-1.5 bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs font-bold rounded-xl border border-stone-300 transition-colors">
                🪟 Window Frame
            </button>
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

    <form method="POST" action="{{ route('seller.products.store') }}" enctype="multipart/form-data"
          class="space-y-6">
        @csrf

        {{-- ═══════════════════════════════════════════════════════
             SECTION 1 — PHOTO UPLOAD (Most important — top of form)
        ══════════════════════════════════════════════════════════ --}}
        <div class="bg-white rounded-3xl border border-stone-200 p-6 sm:p-8 shadow-sm space-y-5">
            <h2 class="text-sm font-bold uppercase tracking-wider text-stone-900 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-stone-900 text-white text-xs flex items-center justify-center font-bold">1</span>
                Product Photos · صور المنتج
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Primary Photo Drop Zone --}}
                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-700">
                        Main Product Photo <span class="text-rose-500">*</span>
                    </label>

                    {{-- Drop zone --}}
                    <div
                        class="relative border-2 rounded-2xl transition-all duration-200 overflow-hidden"
                        :class="dragging ? 'border-amber-400 bg-amber-50' : 'border-dashed border-stone-300 bg-stone-50 hover:border-stone-400'"
                        @dragover.prevent="dragging = true"
                        @dragleave.prevent="dragging = false"
                        @drop.prevent="handleMainFile($event)"
                    >
                        {{-- Preview if chosen --}}
                        <template x-if="previewUrl">
                            <div class="relative aspect-4/3">
                                <img :src="previewUrl" alt="Preview" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-stone-900/30 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                    <span class="text-white text-xs font-bold bg-stone-900/70 px-3 py-1.5 rounded-full">Click to Change Photo</span>
                                </div>
                                <label for="image_file_input" class="absolute inset-0 cursor-pointer"></label>
                            </div>
                        </template>

                        {{-- Empty state --}}
                        <template x-if="!previewUrl">
                            <div class="flex flex-col items-center justify-center py-14 px-6 text-center cursor-pointer" @click="$refs.mainInput.click()">
                                <div class="w-14 h-14 rounded-2xl bg-stone-200 flex items-center justify-center mb-3">
                                    <svg class="w-7 h-7 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-bold text-stone-700">Click or drag & drop your photo</p>
                                <p class="text-xs text-stone-400 mt-1">JPG, PNG, WebP · Max 8 MB</p>
                                <div class="mt-4 px-5 py-2 bg-stone-900 text-white text-xs font-bold rounded-full inline-block">
                                    Choose Photo
                                </div>
                            </div>
                        </template>

                        {{-- Real file input --}}
                        <input
                            id="image_file_input"
                            name="image_file"
                            type="file"
                            accept="image/jpeg,image/png,image/webp,image/gif"
                            class="block w-full text-xs text-stone-700 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-stone-900 file:text-white hover:file:bg-stone-800 cursor-pointer border border-stone-300 rounded-xl bg-white p-1.5 mt-3"
                            x-ref="mainInput"
                            @change="handleMainFile($event)"
                        >
                    </div>

                    <p class="text-[11px] text-stone-400">Main storefront photo. Select directly from your computer (JPG, PNG, WebP).</p>
                </div>

                {{-- Gallery Photos --}}
                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-700">
                        Additional Gallery Photos <span class="text-stone-400 font-normal">(up to 8)</span>
                    </label>

                    <div
                        class="relative border-2 border-dashed border-stone-300 bg-stone-50 hover:border-stone-400 rounded-2xl transition-all duration-200 min-h-[180px]"
                        @click="$refs.galleryInput.click()"
                    >
                        <template x-if="galleryPreviews.length === 0">
                            <div class="flex flex-col items-center justify-center py-10 px-6 text-center cursor-pointer">
                                <div class="w-10 h-10 rounded-xl bg-stone-200 flex items-center justify-center mb-2">
                                    <svg class="w-5 h-5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </div>
                                <p class="text-xs font-bold text-stone-600">Add More Photos</p>
                                <p class="text-[11px] text-stone-400 mt-0.5">Multiple angles, detail shots, room context</p>
                            </div>
                        </template>

                        <template x-if="galleryPreviews.length > 0">
                            <div class="p-3 grid grid-cols-3 gap-2 cursor-pointer">
                                <template x-for="(src, idx) in galleryPreviews" :key="idx">
                                    <div class="aspect-square rounded-xl overflow-hidden bg-stone-200">
                                        <img :src="src" class="w-full h-full object-cover">
                                    </div>
                                </template>
                                <div class="aspect-square rounded-xl border-2 border-dashed border-stone-300 flex items-center justify-center text-stone-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </div>
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
                    <p class="text-[11px] text-stone-400">Show the product from multiple angles to increase conversion.</p>
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
                        placeholder="e.g. Al-Rawdah Royal Majlis L-Sofa"
                        class="w-full px-3.5 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900 focus:outline-none">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">Tagline / Highlight</label>
                    <input type="text" name="tagline" x-model="tagline"
                        placeholder="e.g. Gold-braided ivory chenille, 8-seater modular Majlis"
                        class="w-full px-3.5 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">Category <span class="text-rose-500">*</span></label>
                    <select name="category_id" x-model="categoryId" required
                        class="w-full px-3.5 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900 focus:outline-none">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">Subcategory</label>
                    <select name="subcategory_id"
                        class="w-full px-3.5 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900 focus:outline-none">
                        <option value="">— Select (Optional) —</option>
                        <template x-for="sub in filteredSubcategories()" :key="sub.id">
                            <option :value="sub.id" x-text="sub.name"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">Brand / Studio</label>
                    <select name="brand_id"
                        class="w-full px-3.5 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900 focus:outline-none">
                        <option value="">— In-House Atelier Studio —</option>
                        @foreach($brands as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center">
                    <label class="inline-flex items-center gap-2.5 cursor-pointer mt-5">
                        <input type="checkbox" name="is_featured" value="1"
                            class="w-4 h-4 rounded text-stone-900 border-stone-300 focus:ring-stone-900">
                        <div>
                            <span class="text-xs font-bold text-stone-800 block">Pin to Featured Editions</span>
                            <span class="text-[11px] text-stone-400">Shown on homepage hero section</span>
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
                            :placeholder="productType === 'custom_fit' ? '800.00' : '1800.00'"
                            class="w-full pl-8 pr-3 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900 focus:outline-none font-bold">
                    </div>
                    <p class="text-[11px] text-stone-400 mt-1" x-show="productType === 'custom_fit'">Display price starting bound (e.g. 800 SAR)</p>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">
                        <span x-text="productType === 'custom_fit' ? 'Max Price Range (SAR)' : 'Compare At Price (SAR)'"></span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-sm text-stone-500 font-bold">﷼</span>
                        <input type="number" step="0.01" name="compare_at_price" x-model="comparePrice"
                            :placeholder="productType === 'custom_fit' ? '1000.00' : '2100.00'"
                            class="w-full pl-8 pr-3 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900 focus:outline-none">
                    </div>
                    <p class="text-[11px] text-stone-400 mt-1" x-show="productType === 'custom_fit'">Upper price bound shown as "800 to 1,000 SAR"</p>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">Stock / Production Capacity <span class="text-rose-500">*</span></label>
                    <input type="number" name="stock" x-model="stock" required min="0"
                        placeholder="10"
                        class="w-full px-3.5 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900 focus:outline-none">
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════
             SECTION 3B — Made-to-Measure Custom Specifications (Shown when Custom Fit)
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
                    <input type="text" name="dimensions"
                        placeholder='e.g. 300cm W × 90cm D × 85cm H'
                        class="w-full px-3.5 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">Primary Materials</label>
                    <input type="text" name="materials"
                        placeholder="e.g. Italian Chenille, Solid Walnut"
                        class="w-full px-3.5 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">Color / Finish</label>
                    <input type="text" name="color"
                        placeholder="e.g. Ivory Gold / Matte Black"
                        class="w-full px-3.5 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">Full Product Description <span class="text-rose-500">*</span></label>
                <textarea name="description" required rows="4"
                    placeholder="Describe craftsmanship, materials, Saudi climate suitability, warranty, and delivery timeline..."
                    class="w-full px-3.5 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900 focus:outline-none"></textarea>
            </div>
        </div>

        {{-- ═══════════════════════════
             Submit Bar
        ════════════════════════════ --}}
        <div class="flex items-center justify-between bg-white rounded-2xl border border-stone-200 px-6 py-4 shadow-sm">
            <a href="{{ route('seller.products.index') }}"
               class="px-5 py-2.5 bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs font-bold rounded-xl transition-colors">
                ← Cancel
            </a>
            <button type="submit"
                class="px-8 py-3 bg-stone-900 hover:bg-stone-800 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-md transition-all hover:scale-105 active:scale-95 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                Publish to Storefront
            </button>
        </div>
    </form>
</div>
@endsection
