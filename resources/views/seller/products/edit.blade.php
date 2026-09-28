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
    }
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
                                 onerror="this.src='https://placehold.co/600x400/f5f5f4/a8a29e?text=No+Image'">
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
                                    <img src="{{ $gUrl }}" alt="Gallery" class="w-full h-full object-cover">
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
                            class="w-full pl-8 pr-3 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900 focus:outline-none font-bold">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">Compare At (SAR)</label>
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
        <div class="flex items-center justify-between bg-white rounded-2xl border border-stone-200 px-6 py-4 shadow-sm">
            <a href="{{ route('seller.products.index') }}"
               class="px-5 py-2.5 bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs font-bold rounded-xl transition-colors">
                ← Cancel
            </a>
            <button type="submit"
                class="px-8 py-3 bg-stone-900 hover:bg-stone-800 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-md transition-all hover:scale-105 active:scale-95 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection
