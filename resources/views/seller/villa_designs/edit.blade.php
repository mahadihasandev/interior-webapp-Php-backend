@extends('layouts.seller')

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="{
    titleEn: '{{ old('title_en', $villaDesign->title_en) }}',
    titleAr: '{{ old('title_ar', $villaDesign->title_ar) }}',
    type: '{{ old('type', $villaDesign->type) }}',
    categoryMode: 'preset',
    categoryKey: '{{ old('category_key', $villaDesign->category_key) }}',
    categoryNameEn: '{{ old('category_name_en', $villaDesign->category_name_en) }}',
    categoryNameAr: '{{ old('category_name_ar', $villaDesign->category_name_ar) }}',
    locationTag: '{{ old('location_tag', $villaDesign->location_tag) }}',
    tagline: '{{ old('tagline', $villaDesign->tagline) }}',
    priceSar: '{{ old('price_sar', (int)$villaDesign->price_sar) }}',
    dimensions: '{{ old('dimensions', $villaDesign->specs['dimensions'] ?? '') }}',
    finishOrFabric: '{{ old('finish_or_fabric', $villaDesign->specs['finishOrFabric'] ?? '') }}',
    coreMaterial: '{{ old('core_material', $villaDesign->specs['coreMaterial'] ?? '') }}',
    hardware: '{{ old('hardware', $villaDesign->specs['hardware'] ?? '') }}',
    feature1: '{{ old('features.0', $villaDesign->features[0] ?? '') }}',
    feature2: '{{ old('features.1', $villaDesign->features[1] ?? '') }}',
    feature3: '{{ old('features.2', $villaDesign->features[2] ?? '') }}',
    previewUrl: '{{ $villaDesign->photo_url }}',
    fileName: null,
    fileSize: null,
    dragging: false,

    get priceUsd() {
        const sar = parseFloat(this.priceSar);
        if (!sar || isNaN(sar)) return 0;
        return Math.round(sar / 3.75);
    },
    get depositSar() {
        const sar = parseFloat(this.priceSar);
        if (!sar || isNaN(sar)) return 0;
        return Math.round(sar * 0.40);
    },

    categoriesMap: {
        'privacy_partition': { en: 'Privacy & Mashrabiya', ar: 'فواصل الخصوصية والمشربية' },
        'majlis': { en: 'Royal Majlis & Salons', ar: 'المجالس وصالونات الاستقبال' },
        'thermal_window': { en: '50°C Thermal Windows', ar: 'نوافذ العزل الحراري (مقاومة 50°م)' },
        'family_living': { en: 'Family Living Lounges', ar: 'صالات المعيشة العائلية' }
    },

    selectCategory(key) {
        this.categoryKey = key;
        if (this.categoriesMap[key]) {
            this.categoryNameEn = this.categoriesMap[key].en;
            this.categoryNameAr = this.categoriesMap[key].ar;
        }
    },

    handleFile(event) {
        const file = event.target.files[0] || (event.dataTransfer && event.dataTransfer.files[0]);
        if (!file) return;
        this.dragging = false;
        this.fileName = file.name;
        this.fileSize = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
        const reader = new FileReader();
        reader.onload = (e) => { this.previewUrl = e.target.result; };
        reader.readAsDataURL(file);
        if (event.dataTransfer) {
            const dt = new DataTransfer();
            dt.items.add(file);
            document.getElementById('image_file_input').files = dt.files;
        }
    }
}">

    {{-- Top Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-stone-200">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-amber-900 mb-1">
                <a href="{{ route('seller.villa-designs.index') }}" class="hover:underline">Villa Showcase</a>
                <span>/</span>
                <span>Edit #{{ $villaDesign->id }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-stone-900 tracking-tight">
                Edit: {{ $villaDesign->title_en }}
            </h1>
            <p class="text-xs text-stone-500 mt-1">
                Update photos, titles, Arabic translations, specifications, and Saudi pricing.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('seller.villa-designs.index') }}" class="px-4 py-2 bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs font-bold rounded-xl transition-colors">
                ← Back to List
            </a>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 text-sm">
            <p class="font-bold mb-1">Please fix the following issues:</p>
            <ul class="list-disc list-inside text-xs space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('seller.villa-designs.update', $villaDesign) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- Left Column: Form Fields --}}
            <div class="lg:col-span-7 space-y-6">

                {{-- SECTION 1 — PHOTO UPLOAD (DIRECT FILE UPLOAD - NO LINK) --}}
                <div class="bg-white rounded-3xl border border-stone-200 p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between pb-1 border-b border-stone-100">
                        <h2 class="text-sm font-bold uppercase tracking-wider text-stone-900 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-stone-900 text-white text-xs flex items-center justify-center font-bold">1</span>
                            Showcase Photo · صورة التصميم
                        </h2>
                        <span class="text-[11px] text-emerald-800 bg-emerald-50 px-2.5 py-0.5 rounded-full font-bold border border-emerald-200 flex items-center gap-1">
                            <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Upload File from Device</span>
                        </span>
                    </div>

                    <div
                        class="relative border-2 rounded-2xl transition-all duration-200 overflow-hidden"
                        :class="dragging ? 'border-amber-500 bg-amber-50/70 ring-2 ring-amber-300' : (fileName ? 'border-emerald-300 bg-stone-900' : 'border-dashed border-stone-300 bg-stone-50 hover:border-stone-400')"
                        @dragover.prevent="dragging = true"
                        @dragleave.prevent="dragging = false"
                        @drop.prevent="handleFile($event)"
                    >
                        {{-- Image Display: Either Existing or Newly Selected File --}}
                        <div class="relative aspect-16/10 group">
                            <img :src="previewUrl" alt="Showcase Photo" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-stone-950/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3">
                                <button type="button" @click="$refs.mainInput.click()"
                                        class="px-5 py-2.5 bg-white hover:bg-stone-100 text-stone-900 text-xs font-bold rounded-xl shadow-lg cursor-pointer flex items-center gap-2">
                                    <svg class="w-4 h-4 text-stone-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span>Upload New Photo From Device</span>
                                </button>
                            </div>
                        </div>

                        {{-- Native File Input (Always accessible and visible) --}}
                    </div>

                    {{-- Always-Visible Native File Input Bar --}}
                    <div class="p-3.5 rounded-2xl bg-stone-50 border border-stone-200 space-y-2">
                        <label for="image_file_input" class="block text-xs font-bold text-stone-700 uppercase tracking-wider">
                            Select New Photo File to Replace (اختر صورة جديدة من جهازك):
                        </label>
                        <input
                            type="file"
                            id="image_file_input"
                            name="image_file"
                            accept="image/jpeg,image/png,image/webp,image/gif,image/avif"
                            class="block w-full text-xs text-stone-700 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-stone-900 file:text-white hover:file:bg-stone-800 cursor-pointer border border-stone-300 rounded-xl bg-white p-1.5"
                            @change="handleFile($event)"
                        >
                        <div class="flex items-center justify-between text-[11px] text-stone-500 pt-1">
                            <span id="file_status_text">
                                <span class="font-bold text-stone-700">Status:</span> 
                                <span class="font-mono text-stone-900" x-text="fileName ? ('New file selected: ' + fileName + ' (' + fileSize + ')') : 'Keeping currently saved photo'">Keeping currently saved photo</span>
                            </span>
                        </div>
                    </div>

                    {{-- Optional Detail Close-Up Photo Upload --}}
                    <div class="pt-2 border-t border-stone-100 space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-stone-600">
                            Optional Material Detail / Macro Photo (صورة قريبة للخامة)
                        </label>
                        <input type="file" name="detail_image_file" accept="image/jpeg,image/png,image/webp,image/gif"
                               class="w-full text-xs text-stone-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-stone-100 file:text-stone-800 hover:file:bg-stone-200 cursor-pointer">
                        <p class="text-[10px] text-stone-400">Upload a new photo file to replace detail close-up (leave empty to keep current).</p>
                    </div>
                </div>

                {{-- SECTION 2 — CATEGORY & TYPE --}}
                <div class="bg-white rounded-3xl border border-stone-200 p-6 shadow-sm space-y-4">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-stone-900 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-stone-900 text-white text-xs flex items-center justify-center font-bold">2</span>
                        Category & Showcase Classification
                    </h2>

                    <div class="grid grid-cols-2 gap-3">
                        <label class="p-3 rounded-2xl border-2 cursor-pointer flex items-center gap-3 transition-all"
                               :class="type === 'fitting' ? 'border-amber-600 bg-amber-50/50 text-amber-950 font-bold' : 'border-stone-200 text-stone-600 hover:border-stone-300'">
                            <input type="radio" name="type" value="fitting" x-model="type" class="hidden">
                            <span class="text-lg">🪟</span>
                            <div>
                                <span class="text-xs block font-bold">Architectural Fitting</span>
                                <span class="text-[10px] text-stone-500 font-normal">Windows, Partitions, Mashrabiya</span>
                            </div>
                        </label>

                        <label class="p-3 rounded-2xl border-2 cursor-pointer flex items-center gap-3 transition-all"
                               :class="type === 'sofa' ? 'border-amber-600 bg-amber-50/50 text-amber-950 font-bold' : 'border-stone-200 text-stone-600 hover:border-stone-300'">
                            <input type="radio" name="type" value="sofa" x-model="type" class="hidden">
                            <span class="text-lg">🛋</span>
                            <div>
                                <span class="text-xs block font-bold">Bespoke Majlis & Sofa</span>
                                <span class="text-[10px] text-stone-500 font-normal">Modular Salons, Bouclé, Leather</span>
                            </div>
                        </label>
                    </div>

                    <div class="space-y-3 pt-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-stone-700">Room Category</label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            <button type="button" @click="selectCategory('privacy_partition')"
                                    class="p-2.5 rounded-xl border text-left text-xs transition-all cursor-pointer"
                                    :class="categoryKey === 'privacy_partition' ? 'bg-stone-900 text-white font-bold border-stone-900' : 'bg-stone-50 border-stone-200 text-stone-700 hover:bg-stone-100'">
                                <div class="font-bold">Privacy Screens</div>
                                <div class="text-[10px] opacity-75 font-normal">فواصل المشربية</div>
                            </button>

                            <button type="button" @click="selectCategory('majlis')"
                                    class="p-2.5 rounded-xl border text-left text-xs transition-all cursor-pointer"
                                    :class="categoryKey === 'majlis' ? 'bg-stone-900 text-white font-bold border-stone-900' : 'bg-stone-50 border-stone-200 text-stone-700 hover:bg-stone-100'">
                                <div class="font-bold">Royal Majlis</div>
                                <div class="text-[10px] opacity-75 font-normal">المجالس الفاخرة</div>
                            </button>

                            <button type="button" @click="selectCategory('thermal_window')"
                                    class="p-2.5 rounded-xl border text-left text-xs transition-all cursor-pointer"
                                    :class="categoryKey === 'thermal_window' ? 'bg-stone-900 text-white font-bold border-stone-900' : 'bg-stone-50 border-stone-200 text-stone-700 hover:bg-stone-100'">
                                <div class="font-bold">Thermal Windows</div>
                                <div class="text-[10px] opacity-75 font-normal">نوافذ عزل 50°م</div>
                            </button>

                            <button type="button" @click="selectCategory('family_living')"
                                    class="p-2.5 rounded-xl border text-left text-xs transition-all cursor-pointer"
                                    :class="categoryKey === 'family_living' ? 'bg-stone-900 text-white font-bold border-stone-900' : 'bg-stone-50 border-stone-200 text-stone-700 hover:bg-stone-100'">
                                <div class="font-bold">Family Lounges</div>
                                <div class="text-[10px] opacity-75 font-normal">صالات العائلة</div>
                            </button>
                        </div>

                        <input type="hidden" name="category_key" :value="categoryKey">
                        <input type="hidden" name="category_name_en" :value="categoryNameEn">
                        <input type="hidden" name="category_name_ar" :value="categoryNameAr">
                    </div>
                </div>

                {{-- SECTION 3 — TITLES & DESCRIPTION --}}
                <div class="bg-white rounded-3xl border border-stone-200 p-6 shadow-sm space-y-4">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-stone-900 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-stone-900 text-white text-xs flex items-center justify-center font-bold">3</span>
                        Titles & Details
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">English Title</label>
                            <input type="text" name="title_en" x-model="titleEn" required
                                   class="w-full px-3.5 py-2.5 text-xs bg-stone-50 border border-stone-300 rounded-xl font-bold focus:ring-2 focus:ring-stone-900">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">Arabic Title (العنوان بالعربي)</label>
                            <input type="text" name="title_ar" x-model="titleAr" required dir="rtl"
                                   class="w-full px-3.5 py-2.5 text-xs bg-stone-50 border border-stone-300 rounded-xl font-bold focus:ring-2 focus:ring-stone-900 text-right">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">Location Badge</label>
                        <input type="text" name="location_tag" x-model="locationTag" required
                               class="w-full px-3.5 py-2.5 text-xs bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">Details & Architectural Summary</label>
                        <textarea name="tagline" x-model="tagline" required rows="3"
                                  class="w-full px-3.5 py-2.5 text-xs bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900"></textarea>
                    </div>
                </div>

                {{-- SECTION 4 — 3 SAUDI FEATURES --}}
                <div class="bg-white rounded-3xl border border-stone-200 p-6 shadow-sm space-y-4">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-stone-900 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-stone-900 text-white text-xs flex items-center justify-center font-bold">4</span>
                        Key Saudi Highlights (3 Checkmarks)
                    </h2>

                    <div class="space-y-3">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs shrink-0">✓</span>
                            <input type="text" name="features[]" x-model="feature1" required
                                   class="w-full px-3 py-2 text-xs bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900">
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs shrink-0">✓</span>
                            <input type="text" name="features[]" x-model="feature2" required
                                   class="w-full px-3 py-2 text-xs bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900">
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs shrink-0">✓</span>
                            <input type="text" name="features[]" x-model="feature3" required
                                   class="w-full px-3 py-2 text-xs bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900">
                        </div>
                    </div>
                </div>

                {{-- SECTION 5 — SPECS & PRICING --}}
                <div class="bg-white rounded-3xl border border-stone-200 p-6 shadow-sm space-y-4">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-stone-900 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-stone-900 text-white text-xs flex items-center justify-center font-bold">5</span>
                        Specifications & Pricing (SAR)
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">Dimensions</label>
                            <input type="text" name="dimensions" x-model="dimensions"
                                   class="w-full px-3 py-2 text-xs bg-stone-50 border border-stone-300 rounded-xl">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">Finish / Fabric</label>
                            <input type="text" name="finish_or_fabric" x-model="finishOrFabric"
                                   class="w-full px-3 py-2 text-xs bg-stone-50 border border-stone-300 rounded-xl">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">Core Material</label>
                            <input type="text" name="core_material" x-model="coreMaterial"
                                   class="w-full px-3 py-2 text-xs bg-stone-50 border border-stone-300 rounded-xl">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">Hardware / Plinth</label>
                            <input type="text" name="hardware" x-model="hardware"
                                   class="w-full px-3 py-2 text-xs bg-stone-50 border border-stone-300 rounded-xl">
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200/80 space-y-3 pt-4">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-amber-950 mb-1">
                                    Price in SAR (ريال)
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2.5 text-xs font-bold text-amber-800">﷼</span>
                                    <input type="number" step="1" name="price_sar" x-model="priceSar" required
                                           class="w-full pl-7 pr-3 py-2 text-sm bg-white border border-amber-300 rounded-xl font-bold text-stone-900 focus:ring-2 focus:ring-stone-900">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-stone-500 mb-1">Est. USD ($)</label>
                                <input type="number" name="price_usd" :value="priceUsd" readonly
                                       class="w-full px-3 py-2 text-sm bg-stone-100 border border-stone-200 rounded-xl text-stone-600 font-mono">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-amber-900 mb-1">40% Deposit</label>
                                <div class="px-3 py-2 bg-white border border-amber-300 rounded-xl text-sm font-bold text-amber-900">
                                    <span x-text="depositSar.toLocaleString() + ' SAR'"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-2">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ $villaDesign->is_active ? 'checked' : '' }}
                                   class="w-4 h-4 rounded text-stone-900 border-stone-300 focus:ring-stone-900">
                            <span class="text-xs font-bold text-stone-800">Published live on storefront showcase</span>
                        </label>
                    </div>
                </div>

                {{-- Submit Bar --}}
                <div class="flex items-center justify-between bg-white rounded-2xl border border-stone-200 px-6 py-4 shadow-sm">
                    <a href="{{ route('seller.villa-designs.index') }}" class="px-5 py-2.5 bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs font-bold rounded-xl transition-colors">
                        ← Cancel
                    </a>
                    <button type="submit"
                            class="px-8 py-3 bg-stone-900 hover:bg-stone-800 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-md transition-all hover:scale-105 active:scale-95 flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Save Changes</span>
                    </button>
                </div>
            </div>

            {{-- Right Column: Live Sticky Preview --}}
            <div class="lg:col-span-5">
                <div class="sticky top-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-stone-500 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Live Storefront Card Preview
                        </span>
                        <span class="text-[10px] text-stone-400">Updates as you edit</span>
                    </div>

                    <div class="bg-white rounded-3xl border border-stone-200 shadow-xl overflow-hidden flex flex-col justify-between">
                        <div>
                            <div class="relative aspect-16/10 overflow-hidden bg-stone-900">
                                <img :src="previewUrl" alt="Card Preview" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-stone-950/85 via-stone-950/20 to-black/30"></div>

                                <div class="absolute top-3 left-3 right-3 flex items-center justify-between gap-2">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-white/95 text-stone-900 shadow-xs"
                                          x-text="type === 'fitting' ? 'Architectural Fitting' : 'Bespoke Sofa'"></span>

                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-stone-900/90 text-amber-300 border border-stone-700 backdrop-blur-xs truncate max-w-[190px]"
                                          x-text="'📍 ' + locationTag"></span>
                                </div>

                                <div class="absolute bottom-3 left-3 right-3 space-y-0.5">
                                    <h4 class="text-base font-serif font-bold text-white leading-tight drop-shadow-md"
                                        x-text="titleEn"></h4>
                                    <p class="text-[11px] text-amber-200/90 font-medium"
                                       x-text="titleAr"></p>
                                </div>
                            </div>

                            <div class="p-5 space-y-4">
                                <p class="text-xs text-stone-600 line-clamp-2 leading-relaxed"
                                   x-text="tagline"></p>

                                <div class="space-y-1.5 pt-1">
                                    <div class="flex items-start gap-1.5 text-[11px] text-stone-800 font-medium">
                                        <span class="text-emerald-600 font-bold shrink-0 mt-0.5">✓</span>
                                        <span x-text="feature1"></span>
                                    </div>
                                    <div class="flex items-start gap-1.5 text-[11px] text-stone-800 font-medium">
                                        <span class="text-emerald-600 font-bold shrink-0 mt-0.5">✓</span>
                                        <span x-text="feature2"></span>
                                    </div>
                                    <div class="flex items-start gap-1.5 text-[11px] text-stone-800 font-medium">
                                        <span class="text-emerald-600 font-bold shrink-0 mt-0.5">✓</span>
                                        <span x-text="feature3"></span>
                                    </div>
                                </div>

                                <div class="p-3 rounded-2xl bg-stone-50 border border-stone-200/80 text-xs space-y-1">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[11px] text-stone-500">Dimensions:</span>
                                        <span class="font-bold text-stone-900" x-text="dimensions"></span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-[11px] text-stone-500">Finish / Fabric:</span>
                                        <span class="font-bold text-stone-900 truncate max-w-[170px]" x-text="finishOrFabric"></span>
                                    </div>
                                </div>

                                <div class="flex items-baseline justify-between pt-2 border-t border-stone-100">
                                    <div>
                                        <span class="text-[9px] uppercase tracking-wider text-stone-400 font-bold block">Estimated Custom Price</span>
                                        <div class="flex items-baseline gap-1 mt-0.5">
                                            <span class="text-base font-serif font-bold text-stone-900"
                                                  x-text="(parseFloat(priceSar || 0)).toLocaleString() + ' SAR'"></span>
                                            <span class="text-[10px] text-stone-400 font-mono"
                                                  x-text="'($' + priceUsd.toLocaleString() + ')'"></span>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <span class="text-[9px] uppercase tracking-wider text-amber-800 font-bold block">40% Deposit to Build</span>
                                        <span class="text-xs font-bold text-amber-900 mt-0.5 block"
                                              x-text="depositSar.toLocaleString() + ' SAR'"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="px-5 py-3 bg-amber-50/50 border-t border-amber-100 text-center">
                            <span class="text-[11px] text-amber-900 font-bold">
                                Category Tab: <span x-text="categoryNameEn"></span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
