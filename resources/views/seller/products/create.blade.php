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
    prefillDemo(type) {
        if (type === 'majlis') {
            this.name = 'Al-Rawdah Royal Majlis L-Sofa';
            this.tagline = 'Gold-braided ivory chenille, 8-seater modular Majlis configuration';
            this.price = '4800.00';
            this.comparePrice = '5500.00';
            this.stock = '5';
        } else if (type === 'window') {
            this.name = 'Desert Horizon Thermal-Break Window Frame';
            this.tagline = '50°C rated double-glazed aluminium, matte black powder coat';
            this.price = '2200.00';
            this.comparePrice = '2600.00';
            this.stock = '12';
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
             SECTION 3 — Pricing
        ════════════════════════════ --}}
        <div class="bg-white rounded-3xl border border-stone-200 p-6 sm:p-8 shadow-sm space-y-4">
            <h2 class="text-sm font-bold uppercase tracking-wider text-stone-900 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-stone-900 text-white text-xs flex items-center justify-center font-bold">3</span>
                Pricing & Inventory · الأسعار والمخزون
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">Retail Price (SAR) <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-sm text-stone-500 font-bold">﷼</span>
                        <input type="number" step="0.01" name="price" x-model="price" required
                            placeholder="1800.00"
                            class="w-full pl-8 pr-3 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900 focus:outline-none font-bold">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">Compare At Price (SAR)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-sm text-stone-500 font-bold">﷼</span>
                        <input type="number" step="0.01" name="compare_at_price" x-model="comparePrice"
                            placeholder="2100.00"
                            class="w-full pl-8 pr-3 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">Stock Units <span class="text-rose-500">*</span></label>
                    <input type="number" name="stock" x-model="stock" required min="0"
                        placeholder="10"
                        class="w-full px-3.5 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900 focus:outline-none">
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
