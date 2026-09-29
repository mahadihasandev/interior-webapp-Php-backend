@extends('layouts.seller')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{
    categoryId: '{{ old('category_id', $categories->first()->id ?? '') }}',
    subcategories: {{ json_encode($subcategories) }},
    previewUrl: null,
    galleryPreviews: [],
    name: '{{ old('name', '') }}',
    tagline: '{{ old('tagline', '') }}',
    price: '{{ old('price', '800.00') }}',
    comparePrice: '{{ old('compare_at_price', '1000.00') }}',
    stock: '{{ old('stock', '50') }}',
    defaultHeight: '{{ old('default_height', '180') }}',
    defaultWidth: '{{ old('default_width', '140') }}',
    measurementUnit: '{{ old('measurement_unit', 'cm') }}',
    dimensions: '{{ old('dimensions', '180cm H × 140cm W (Customizable)') }}',
    materials: '{{ old('materials', 'Alupco Architectural Aluminum 2.0mm, Low-E Double Glazing (6mm+12A+6mm)') }}',
    color: '{{ old('color', 'Matte Architectural Black / Champagne Bronze / Sand White') }}',
    description: '{{ old('description', 'Precision-engineered for Saudi Arabian climates. Features dual polyamide thermal isolators resisting external wall temperatures up to 50°C while maintaining cool interior comfort. Multi-chamber extruded aluminum profile with argon-filled acoustic glazing, dust-proof hermetic perimeter gaskets, and German heavy-duty roller bearings.') }}',
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
    prefillPreset(preset) {
        if (preset === 'thermal_window') {
            this.name = 'Thermal Break 50°C Double-Glazed Window';
            this.tagline = 'SASO certified thermal break · 38dB acoustic barrier · Double sliding shutters';
            this.price = '800.00';
            this.comparePrice = '1000.00';
            this.defaultHeight = '180';
            this.defaultWidth = '140';
            this.dimensions = '180cm H × 140cm W (Customizable)';
            this.materials = 'Alupco Architectural Aluminum 2.0mm, Low-E Double Glazing (6mm+12A+6mm)';
            this.color = 'Matte Architectural Black / Champagne Bronze';
            this.description = 'Precision-engineered for Saudi Arabian climates. Features dual polyamide thermal isolators resisting external wall temperatures up to 50°C while maintaining cool interior comfort.';
        } else if (preset === 'acoustic_window') {
            this.name = 'Acoustic Triple-Glazed Shutter Window';
            this.tagline = '42dB sound dampening · Heavy-duty multi-shutter · Riyadh villa grade';
            this.price = '950.00';
            this.comparePrice = '1200.00';
            this.defaultHeight = '200';
            this.defaultWidth = '160';
            this.dimensions = '200cm H × 160cm W (Customizable)';
            this.materials = 'Royal Gulf Alloy 2.5mm, Triple Laminated Acoustic Glass (6+10A+6+10A+6)';
            this.color = 'Champagne Bronze / Charcoal Grey';
            this.description = 'Heavy-duty acoustic window tailored for royal Majlis privacy and highway-adjacent villas in Riyadh and Jeddah.';
        } else if (preset === 'patio_system') {
            this.name = 'Minimalist Slim Aluminum Sliding Patio System';
            this.tagline = 'Ultra-thin 18mm sightlines · Floor-to-ceiling panoramic glass · 2 to 4 shutters';
            this.price = '1100.00';
            this.comparePrice = '1400.00';
            this.defaultHeight = '280';
            this.defaultWidth = '360';
            this.dimensions = '280cm H × 360cm W (Customizable)';
            this.materials = 'Minimalist Slim Alloy 1.8mm, High-Clarity Solarium Double Glass';
            this.color = 'Deep Graphite / Warm Sand Bronze';
            this.description = 'Flush floor-track panoramic glass sliding system providing seamless transitions from villa living rooms to pool solariums.';
        } else if (preset === 'mashrabiya_bay') {
            this.name = 'Royal Saudi Villa Mashrabiya Glass Bay Window';
            this.tagline = 'Traditional Saudi geometric motif · Thermal double glazing · Majlis focal point';
            this.price = '850.00';
            this.comparePrice = '1150.00';
            this.defaultHeight = '220';
            this.defaultWidth = '180';
            this.dimensions = '220cm H × 180cm W (Customizable)';
            this.materials = 'Laser-Cut Brass Clad Alloy, Low-E Reflective Gold/Bronze Glass';
            this.color = 'Royal Gold Clad / Sand White';
            this.description = 'Bespoke geometric Islamic mashrabiya pattern integrated into high-efficiency architectural thermal glazing.';
        }
    }
}">

    {{-- Top Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-stone-200">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-amber-800 mb-1">
                <a href="{{ route('seller.products.index') }}" class="hover:underline">Catalog</a>
                <span>/</span>
                <span class="text-amber-900 font-extrabold">Add Custom Order</span>
            </div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-2xl sm:text-3xl font-serif font-bold text-stone-900 tracking-tight">Add Custom Order</h1>
                <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 text-xs font-bold border border-amber-300">
                    Bespoke Made-to-Measure
                </span>
            </div>
            <p class="text-xs text-stone-500 mt-1">Upload and configure custom-fit architectural profiles for the <strong>Bespoke Made-to-Measure Editions</strong> homepage showcase.</p>
        </div>

        <div class="flex items-center gap-2 shrink-0 flex-wrap">
            <span class="text-xs text-stone-400 font-medium hidden sm:inline">Quick Preset:</span>
            <button type="button" @click="prefillPreset('thermal_window')" class="px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-900 text-xs font-bold rounded-xl border border-amber-200 transition-colors">
                🪟 50°C Window
            </button>
            <button type="button" @click="prefillPreset('acoustic_window')" class="px-2.5 py-1.5 bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs font-bold rounded-xl border border-stone-300 transition-colors">
                🔇 Acoustic
            </button>
            <button type="button" @click="prefillPreset('patio_system')" class="px-2.5 py-1.5 bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs font-bold rounded-xl border border-stone-300 transition-colors">
                🚪 Patio System
            </button>
            <button type="button" @click="prefillPreset('mashrabiya_bay')" class="px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-900 text-xs font-bold rounded-xl border border-amber-200 transition-colors">
                🕌 Mashrabiya
            </button>
        </div>
    </div>

    {{-- Storefront Notice Banner --}}
    <div class="p-4 rounded-2xl bg-amber-50/80 border border-amber-200/90 text-amber-950 flex items-start gap-3 shadow-2xs">
        <div class="w-8 h-8 rounded-xl bg-amber-200/60 flex items-center justify-center text-amber-800 shrink-0 mt-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="text-xs space-y-1">
            <p class="font-bold text-amber-900">Homepage Display Notice · ظهور فوري في واجهة المتجر</p>
            <p class="text-amber-800/90 leading-relaxed">
                Products created here are automatically categorized as <strong>Bespoke Made-to-Measure (<code class="bg-amber-100/70 px-1 py-0.5 rounded font-mono text-[11px]">custom_fit</code>)</strong>. They appear in the 4-card <strong>"Custom Architectural Products"</strong> section right below the hero banner with an interactive <strong>ORDER NOW →</strong> custom specification builder.
            </p>
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

    {{-- Main Form --}}
    <form method="POST" action="{{ route('seller.products.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <input type="hidden" name="product_type" value="custom_fit">

        {{-- ═══════════════════════════
             SECTION 1 — Product Photos
        ════════════════════════════ --}}
        <div class="bg-white rounded-3xl border border-stone-200 p-6 sm:p-8 shadow-sm space-y-4">
            <h2 class="text-sm font-bold uppercase tracking-wider text-stone-900 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-amber-800 text-white text-xs flex items-center justify-center font-bold">1</span>
                Architectural Photography
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Main Image Upload --}}
                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-700">
                        Primary Showcase Photo <span class="text-rose-500">*</span>
                    </label>

                    <div
                        class="relative border-2 border-dashed rounded-2xl overflow-hidden transition-all duration-200 text-center"
                        :class="dragging ? 'border-amber-700 bg-amber-50' : 'border-stone-300 bg-stone-50 hover:border-stone-400'"
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
                            <div class="flex flex-col items-center justify-center py-12 px-6 text-center cursor-pointer" @click="$refs.mainInput.click()">
                                <div class="w-12 h-12 rounded-2xl bg-amber-100/70 text-amber-800 flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-bold text-stone-700">Click or drag & drop storefront photo</p>
                                <p class="text-xs text-stone-400 mt-1">JPG, PNG, WebP · Max 8 MB</p>
                                <div class="mt-3 px-4 py-1.5 bg-stone-900 text-white text-xs font-bold rounded-full inline-block">
                                    Choose Photo
                                </div>
                            </div>
                        </template>

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
                    <p class="text-[11px] text-stone-400">Card background image for the custom architectural grid.</p>
                </div>

                {{-- Gallery Photos --}}
                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-700">
                        Additional Detail Photos <span class="text-stone-400 font-normal">(up to 8)</span>
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
                                <p class="text-xs font-bold text-stone-600">Add Framing & Glazing Closeups</p>
                                <p class="text-[11px] text-stone-400 mt-0.5">Aluminum finish, acoustic seal, and glass detail shots</p>
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
                    <p class="text-[11px] text-stone-400">Shown in the interactive customizer modal for customers.</p>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════
             SECTION 2 — Identity & Branding
        ════════════════════════════ --}}
        <div class="bg-white rounded-3xl border border-stone-200 p-6 sm:p-8 shadow-sm space-y-4">
            <h2 class="text-sm font-bold uppercase tracking-wider text-stone-900 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-amber-800 text-white text-xs flex items-center justify-center font-bold">2</span>
                Profile Identity & Taxonomy
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">Custom Product Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" x-model="name" required
                        placeholder="e.g. Thermal Break 50°C Double-Glazed Window"
                        class="w-full px-3.5 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-amber-800 focus:outline-none font-medium">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">Highlight Tagline</label>
                    <input type="text" name="tagline" x-model="tagline"
                        placeholder="e.g. SASO certified thermal break · 38dB acoustic barrier · Double sliding shutters"
                        class="w-full px-3.5 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-amber-800 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">Category <span class="text-rose-500">*</span></label>
                    <select name="category_id" x-model="categoryId" required
                        class="w-full px-3.5 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-amber-800 focus:outline-none">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">Subcategory (Optional)</label>
                    <select name="subcategory_id"
                        class="w-full px-3.5 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-amber-800 focus:outline-none">
                        <option value="">— Select (Optional) —</option>
                        <template x-for="sub in filteredSubcategories()" :key="sub.id">
                            <option :value="sub.id" x-text="sub.name"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">Fabricator / Brand Studio</label>
                    <select name="brand_id"
                        class="w-full px-3.5 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-amber-800 focus:outline-none">
                        <option value="">— In-House Atelier Workshop —</option>
                        @foreach($brands as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center">
                    <label class="inline-flex items-center gap-2.5 cursor-pointer mt-5">
                        <input type="checkbox" name="is_featured" value="1" checked
                            class="w-4 h-4 rounded text-amber-800 border-stone-300 focus:ring-amber-800">
                        <div>
                            <span class="text-xs font-bold text-stone-800 block">Feature in Homepage Custom Section</span>
                            <span class="text-[11px] text-stone-400">Selected for the 4-card architectural grid</span>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════
             SECTION 3 — Price Range & Capacity
        ════════════════════════════ --}}
        <div class="bg-white rounded-3xl border border-stone-200 p-6 sm:p-8 shadow-sm space-y-4">
            <h2 class="text-sm font-bold uppercase tracking-wider text-stone-900 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-amber-800 text-white text-xs flex items-center justify-center font-bold">3</span>
                Price Range & Production Capacity
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">
                        Starting Price / Min (SAR) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-sm text-stone-500 font-bold">﷼</span>
                        <input type="number" step="0.01" name="price" x-model="price" required
                            placeholder="800.00"
                            class="w-full pl-8 pr-3 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-amber-800 focus:outline-none font-bold text-stone-900">
                    </div>
                    <p class="text-[11px] text-stone-400 mt-1">Starting bound (e.g. 800 SAR)</p>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">
                        Max Price Range (SAR)
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-sm text-stone-500 font-bold">﷼</span>
                        <input type="number" step="0.01" name="compare_at_price" x-model="comparePrice"
                            placeholder="1000.00"
                            class="w-full pl-8 pr-3 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-amber-800 focus:outline-none font-bold text-stone-900">
                    </div>
                    <p class="text-[11px] text-stone-400 mt-1">Displays as: <strong class="text-stone-700" x-text="`${price} – ${comparePrice} SAR`"></strong></p>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">Monthly Production Capacity <span class="text-rose-500">*</span></label>
                    <input type="number" name="stock" x-model="stock" required min="1"
                        placeholder="50"
                        class="w-full px-3.5 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-amber-800 focus:outline-none">
                    <p class="text-[11px] text-stone-400 mt-1">Workshop orders accepted per cycle</p>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════
             SECTION 4 — Made-to-Measure Custom Form Settings
        ════════════════════════════ --}}
        <div class="bg-white rounded-3xl border-2 border-amber-300/80 p-6 sm:p-8 shadow-sm space-y-6">
            <div class="flex items-center justify-between pb-3 border-b border-stone-200">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-800 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/></svg>
                        Customer Specification Form Settings
                    </span>
                    <h3 class="text-lg font-serif font-bold text-stone-900">Custom Order Parameters</h3>
                    <p class="text-xs text-stone-500">Configure default dimensions and measurement unit for the customer customizer page.</p>
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
            <div class="p-4 rounded-2xl bg-amber-50/50 border border-amber-200/60 space-y-3">
                <span class="text-xs font-bold text-stone-800 block">Options Pre-Configured for Customers in Storefront:</span>
                <div class="flex flex-wrap gap-2 text-xs">
                    <span class="px-3 py-1 bg-white border border-stone-300 rounded-lg text-stone-800 font-medium">🪟 Shutters: 1 Fixed, 2 Sliding, 3 Tri-Slide, 4 Bi-Fold</span>
                    <span class="px-3 py-1 bg-white border border-stone-300 rounded-lg text-stone-800 font-medium">🛡 Aluminum: Alupco Thermal 2.0mm, Royal Gulf 2.5mm, Slim 1.8mm</span>
                    <span class="px-3 py-1 bg-white border border-stone-300 rounded-lg text-stone-800 font-medium">🪞 Glass: Reflective Bronze, Low-E Clear, Smoky Grey, Frosted</span>
                    <span class="px-3 py-1 bg-white border border-stone-300 rounded-lg text-stone-800 font-medium">⚙ Addons: Stainless Fly Screen, Multi-Lock, Motorized</span>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════
             SECTION 5 — Technical Specs & Description
        ════════════════════════════ --}}
        <div class="bg-white rounded-3xl border border-stone-200 p-6 sm:p-8 shadow-sm space-y-4">
            <h2 class="text-sm font-bold uppercase tracking-wider text-stone-900 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-amber-800 text-white text-xs flex items-center justify-center font-bold">5</span>
                Architectural Specifications & Engineering Details
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">Standard / Base Dimensions</label>
                    <input type="text" name="dimensions" x-model="dimensions"
                        placeholder="e.g. 180cm H × 140cm W (Customizable)"
                        class="w-full px-3.5 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-amber-800 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">Materials & Thermal Isolator</label>
                    <input type="text" name="materials" x-model="materials"
                        placeholder="e.g. Alupco Architectural Aluminum 2.0mm, Low-E Double Glazing"
                        class="w-full px-3.5 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-amber-800 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">Color / Anodized Finish</label>
                    <input type="text" name="color" x-model="color"
                        placeholder="e.g. Matte Architectural Black / Champagne Bronze"
                        class="w-full px-3.5 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-amber-800 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-stone-600 mb-1">Architectural Description & SASO Compliance <span class="text-rose-500">*</span></label>
                <textarea name="description" x-model="description" required rows="4"
                    placeholder="Describe thermal isolation, acoustic rating, glass thickness, installation warranty, and delivery across Riyadh, Jeddah, and Neom..."
                    class="w-full px-3.5 py-2.5 text-sm bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-amber-800 focus:outline-none"></textarea>
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
                class="px-8 py-3 bg-amber-800 hover:bg-amber-900 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-md transition-all hover:scale-105 active:scale-95 flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4 text-amber-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Publish Custom Architectural Product
            </button>
        </div>
    </form>
</div>
@endsection
