@extends('layouts.seller')

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="{
    // Form model state
    titleEn: '{{ old('title_en', '') }}',
    titleAr: '{{ old('title_ar', '') }}',
    type: '{{ old('type', 'fitting') }}',
    categoryMode: 'preset', // 'preset' or 'custom'
    categoryKey: '{{ old('category_key', 'privacy_partition') }}',
    categoryNameEn: '{{ old('category_name_en', 'Privacy & Mashrabiya') }}',
    categoryNameAr: '{{ old('category_name_ar', 'فواصل الخصوصية والمشربية') }}',
    locationTag: '{{ old('location_tag', 'Riyadh Villa · Hittin District') }}',
    tagline: '{{ old('tagline', '') }}',
    priceSar: '{{ old('price_sar', '') }}',
    dimensions: '{{ old('dimensions', '96\"H × 72\"W (2.44m × 1.83m)') }}',
    finishOrFabric: '{{ old('finish_or_fabric', 'Champagne Gold Anodized') }}',
    coreMaterial: '{{ old('core_material', '10mm Fluted Ribbed Safety Glass') }}',
    hardware: '{{ old('hardware', 'Architectural Grid + Concealed Pivots') }}',
    feature1: '{{ old('features.0', '') }}',
    feature2: '{{ old('features.1', '') }}',
    feature3: '{{ old('features.2', '') }}',
    previewUrl: null,
    fileName: null,
    fileSize: null,
    demoPhotoUrl: null,
    dragging: false,

    // Auto-calculated pricing
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

    // Category presets map
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
        this.demoPhotoUrl = null;
        const reader = new FileReader();
        reader.onload = (e) => { this.previewUrl = e.target.result; };
        reader.readAsDataURL(file);
        if (event.dataTransfer) {
            const dt = new DataTransfer();
            dt.items.add(file);
            document.getElementById('image_file_input').files = dt.files;
        }
    },

    removePhoto() {
        this.previewUrl = null;
        this.fileName = null;
        this.fileSize = null;
        this.demoPhotoUrl = null;
        const input = document.getElementById('image_file_input');
        if (input) input.value = '';
    },

    prefillPreset(preset) {
        if (preset === 'partition') {
            this.type = 'fitting';
            this.selectCategory('privacy_partition');
            this.titleEn = 'The Royal Majlis Privacy Partition';
            this.titleAr = 'فاصل الخصوصية للمجلس الملكي والمشربية العصرية';
            this.locationTag = 'Riyadh Villa · Hittin District';
            this.tagline = 'Warm champagne gold anodized aluminum with 10mm vertical fluted ribbed privacy glass and acoustic hermetic seal.';
            this.feature1 = 'Visual Privacy between Men’s Majlis & Dining Area';
            this.feature2 = 'Acoustic Sound Dampening (38dB noise barrier)';
            this.feature3 = 'Champagne Gold Electro-Sealed Anodization';
            this.dimensions = '96\"H × 72\"W (2.44m × 1.83m)';
            this.finishOrFabric = 'Champagne Gold Anodized (6063-T6 Alloy)';
            this.coreMaterial = '10mm Fluted Ribbed Safety Glass';
            this.hardware = '3×2 Architectural Grid Mullions + Hydraulic Pivot';
            this.priceSar = '10650';
            this.previewUrl = 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80';
            this.demoPhotoUrl = this.previewUrl;
            this.fileName = 'Majlis Privacy Screen (Demo Photo)';
        } else if (preset === 'sofa') {
            this.type = 'sofa';
            this.selectCategory('majlis');
            this.titleEn = 'The Diwaniya Grand Modular Salon';
            this.titleAr = 'طقم كنب المجلس والديوانية الملكية المعمارية';
            this.locationTag = 'Riyadh Penthouse · Diplomatic Quarter';
            this.tagline = 'Reconfigurable luxury 4-piece salon with generous 42\" deep lounge seating wrapped in cognac full-grain Tuscan saddle leather.';
            this.feature1 = 'Deep 42\" Lounge Depth for Generous Saudi Hospitality';
            this.feature2 = 'Full-Grain Leather that patinates richer with age';
            this.feature3 = 'Solid Hardwood Frame rated for 15+ years of gatherings';
            this.dimensions = '136\"W × 42\"D Lounge (3.45m Modular Width)';
            this.finishOrFabric = 'Cognac Saddle Full-Grain Tuscan Leather';
            this.coreMaterial = 'Multi-Density Resilience Core + Down-Feather Top';
            this.hardware = 'Matte Architectural Black Powder-Coated Steel Plinth';
            this.priceSar = '18188';
            this.previewUrl = 'https://images.unsplash.com/photo-1616594039964-ae9021a400a0?auto=format&fit=crop&w=1200&q=80';
            this.demoPhotoUrl = this.previewUrl;
            this.fileName = 'Royal Majlis Salon (Demo Photo)';
        } else if (preset === 'window') {
            this.type = 'fitting';
            this.selectCategory('thermal_window');
            this.titleEn = 'Riyadh 50°C Thermal Break Window System';
            this.titleAr = 'نوافذ العزل الحراري الفائق لمناخ الرياض (مقاومة 50° مئوية)';
            this.locationTag = 'Central KSA · Riyadh Desert Climate Approved';
            this.tagline = 'Engineered with polyamide thermal barrier and double-glazed Low-E solar glass to block desert heat, UV radiation, and micro-sand.';
            this.feature1 = 'SASO-compliant U-Value < 1.4 W/m²K for Desert Heat';
            this.feature2 = 'Double EPDM Compression Gasket resists sandstorms';
            this.feature3 = 'Low-E Solar Guard blocks 98% of solar UV glare';
            this.dimensions = '84\"H × 60\"W (2.13m × 1.52m Casement)';
            this.finishOrFabric = 'Matte Architectural Black (UV Anodized)';
            this.coreMaterial = 'Low-Iron Double-Glazed Argon Insulated Glass';
            this.hardware = 'Integrated Thermal Break Strip + Concealed Multi-Point Locks';
            this.priceSar = '9075';
            this.previewUrl = 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=1200&q=80';
            this.demoPhotoUrl = this.previewUrl;
            this.fileName = 'Thermal Window System (Demo Photo)';
        }
    }
}">

    {{-- Top Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-stone-200">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-amber-900 mb-1">
                <a href="{{ route('seller.villa-designs.index') }}" class="hover:underline">Villa Showcase</a>
                <span>/</span>
                <span>New Design</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-stone-900 tracking-tight">
                Add Villa Architectural Design · إضافة تصميم فيلا
            </h1>
            <p class="text-xs text-stone-500 mt-1">
                Creates an interactive showcase card on the storefront with real photos, category pills, Saudi features, and 40% deposit checkout.
            </p>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <span class="text-xs text-stone-400 font-medium hidden sm:inline">Quick Fill:</span>
            <button type="button" @click="prefillPreset('partition')" class="px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-900 text-xs font-bold rounded-xl border border-amber-200 transition-colors">
                ✨ Privacy Partition
            </button>
            <button type="button" @click="prefillPreset('sofa')" class="px-3 py-1.5 bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs font-bold rounded-xl border border-stone-300 transition-colors">
                🛋 Majlis Sofa
            </button>
            <button type="button" @click="prefillPreset('window')" class="px-3 py-1.5 bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs font-bold rounded-xl border border-stone-300 transition-colors">
                ☀️ Thermal Window
            </button>
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

    <form method="POST" action="{{ route('seller.villa-designs.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- Left Column: Form Fields (7 cols) --}}
            <div class="lg:col-span-7 space-y-6">

                {{-- SECTION 1 — SHOWCASE PHOTO UPLOAD (DIRECT FILE FROM DEVICE) --}}
                <div class="bg-white rounded-3xl border border-stone-200 p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between pb-2 border-b border-stone-100">
                        <div>
                            <h2 class="text-base font-bold text-stone-900 flex items-center gap-2">
                                <span class="w-7 h-7 rounded-full bg-stone-900 text-white text-xs flex items-center justify-center font-bold">1</span>
                                Showcase Photo · صورة التصميم <span class="text-rose-500">*</span>
                            </h2>
                            <p class="text-xs text-stone-500 mt-0.5">Upload a photo of this architectural design directly from your computer or device.</p>
                        </div>
                        <span class="text-[11px] text-emerald-800 bg-emerald-50 px-3 py-1 rounded-full font-bold border border-emerald-200 inline-flex items-center gap-1.5 shrink-0">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Direct File Upload</span>
                        </span>
                    </div>

                    {{-- Hidden demo preset fallback only when Quick Fill buttons are used --}}
                    <input type="hidden" name="demo_photo_url" id="demo_photo_url" :value="demoPhotoUrl">

                    {{-- Large Drag & Drop / Visual File Picker --}}
                    <div
                        id="dropzone_container"
                        class="relative border-2 border-dashed border-stone-300 hover:border-amber-500 rounded-2xl bg-stone-50 transition-all duration-200 overflow-hidden"
                        :class="dragging ? 'border-amber-500 bg-amber-50/70 ring-2 ring-amber-300' : ''"
                        @dragover.prevent="dragging = true"
                        @dragleave.prevent="dragging = false"
                        @drop.prevent="handleFile($event)"
                    >
                        {{-- Image Preview Box (Displayed once a photo is chosen) --}}
                        <div id="image_preview_box" x-show="previewUrl" style="display: none;" class="relative aspect-16/10 bg-stone-950 group">
                            <img id="image_preview_img" :src="previewUrl" alt="Selected Showcase Photo" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-stone-950/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3">
                                <label for="image_file_input" class="px-5 py-2.5 bg-white hover:bg-stone-100 text-stone-900 text-xs font-bold rounded-xl shadow-lg cursor-pointer inline-flex items-center gap-2">
                                    <svg class="w-4 h-4 text-stone-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span>Change Photo / تغيير الصورة</span>
                                </label>
                                <button type="button" @click="removePhoto()" class="px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-lg cursor-pointer">
                                    ✕ Remove
                                </button>
                            </div>
                        </div>

                        {{-- Default Empty Prompt (Rendered in raw HTML, always visible!) --}}
                        <div id="upload_prompt_box" x-show="!previewUrl" class="flex flex-col items-center justify-center py-10 px-6 text-center">
                            <div class="w-16 h-16 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-800 flex items-center justify-center mb-3">
                                <svg class="w-8 h-8 text-amber-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                            </div>
                            
                            <p class="text-base font-bold text-stone-900 mb-1">
                                Choose Showcase Photo from your Computer
                            </p>
                            <p class="text-xs text-stone-500 mb-4 max-w-md">
                                Drag and drop your image file here, or click the button below to browse your files.
                            </p>

                            <label for="image_file_input" class="px-6 py-3 bg-stone-900 hover:bg-stone-800 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-md cursor-pointer inline-flex items-center gap-2.5 transition-transform active:scale-95">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>Browse Files / اختر صورة من جهازك</span>
                            </label>

                            <p class="text-[11px] text-stone-400 mt-3 font-medium">
                                Supported formats: JPG, PNG, WEBP, GIF, AVIF · Max 12MB
                            </p>
                        </div>
                    </div>

                    {{-- Always-Visible Native File Input Bar --}}
                    <div class="p-3.5 rounded-2xl bg-stone-50 border border-stone-200 space-y-2">
                        <label for="image_file_input" class="block text-xs font-bold text-stone-700 uppercase tracking-wider">
                            Direct Photo File Selection (أو اختر الملف مباشرة هنا):
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
                                <span class="font-bold text-stone-700">Selected file:</span> 
                                <span class="font-mono text-stone-900" x-text="fileName || 'No file selected yet'">No file selected yet</span>
                            </span>
                            <span x-show="fileSize" class="font-mono text-stone-500 font-bold" x-text="fileSize"></span>
                        </div>
                    </div>

                    {{-- Optional Material Detail Photo --}}
                    <div class="pt-3 border-t border-stone-100 space-y-1.5">
                        <label for="detail_image_file" class="block text-xs font-bold uppercase tracking-wider text-stone-700">
                            Optional Detail / Macro Photo (صورة قريبة للخامة أو التفاصيل)
                        </label>
                        <input
                            type="file"
                            id="detail_image_file"
                            name="detail_image_file"
                            accept="image/jpeg,image/png,image/webp,image/gif,image/avif"
                            class="block w-full text-xs text-stone-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-stone-100 file:text-stone-800 hover:file:bg-stone-200 cursor-pointer"
                        >
                        <p class="text-[10px] text-stone-400">Optional: Used in the interactive blueprint modal when clients inspect craftsmanship.</p>
                    </div>
                </div>

                {{-- SECTION 2 — CATEGORY & DESIGN TYPE --}}
                <div class="bg-white rounded-3xl border border-stone-200 p-6 shadow-sm space-y-4">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-stone-900 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-stone-900 text-white text-xs flex items-center justify-center font-bold">2</span>
                        Category & Showcase Classification · التصنيف والقسم
                    </h2>

                    {{-- Type toggle: Fitting vs Sofa --}}
                    <div class="grid grid-cols-2 gap-3">
                        <label class="p-3 rounded-2xl border-2 cursor-pointer flex items-center gap-3 transition-all"
                               :class="type === 'fitting' ? 'border-amber-600 bg-amber-50/50 text-amber-950 font-bold' : 'border-stone-200 text-stone-600 hover:border-stone-300'">
                            <input type="radio" name="type" value="fitting" x-model="type" class="hidden">
                            <span class="text-lg">🪟</span>
                            <div>
                                <span class="text-xs block font-bold">Architectural Fitting</span>
                                <span class="text-[10px] text-stone-500 font-normal">Windows, Glass Partitions, Mashrabiya</span>
                            </div>
                        </label>

                        <label class="p-3 rounded-2xl border-2 cursor-pointer flex items-center gap-3 transition-all"
                               :class="type === 'sofa' ? 'border-amber-600 bg-amber-50/50 text-amber-950 font-bold' : 'border-stone-200 text-stone-600 hover:border-stone-300'">
                            <input type="radio" name="type" value="sofa" x-model="type" class="hidden">
                            <span class="text-lg">🛋</span>
                            <div>
                                <span class="text-xs block font-bold">Bespoke Majlis & Sofa</span>
                                <span class="text-[10px] text-stone-500 font-normal">Modular Salons, Bouclé, Tuscan Leather</span>
                            </div>
                        </label>
                    </div>

                    {{-- Category Selector: Presets or Custom --}}
                    <div class="space-y-3 pt-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-stone-700">Room Category <span class="text-rose-500">*</span></label>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            <button type="button" @click="selectCategory('privacy_partition'); categoryMode = 'preset'"
                                    class="p-2.5 rounded-xl border text-left text-xs transition-all cursor-pointer"
                                    :class="categoryKey === 'privacy_partition' && categoryMode === 'preset' ? 'bg-stone-900 text-white font-bold border-stone-900' : 'bg-stone-50 border-stone-200 text-stone-700 hover:bg-stone-100'">
                                <div class="font-bold">Privacy Screens</div>
                                <div class="text-[10px] opacity-75 font-normal">فواصل المشربية</div>
                            </button>

                            <button type="button" @click="selectCategory('majlis'); categoryMode = 'preset'"
                                    class="p-2.5 rounded-xl border text-left text-xs transition-all cursor-pointer"
                                    :class="categoryKey === 'majlis' && categoryMode === 'preset' ? 'bg-stone-900 text-white font-bold border-stone-900' : 'bg-stone-50 border-stone-200 text-stone-700 hover:bg-stone-100'">
                                <div class="font-bold">Royal Majlis</div>
                                <div class="text-[10px] opacity-75 font-normal">المجالس الفاخرة</div>
                            </button>

                            <button type="button" @click="selectCategory('thermal_window'); categoryMode = 'preset'"
                                    class="p-2.5 rounded-xl border text-left text-xs transition-all cursor-pointer"
                                    :class="categoryKey === 'thermal_window' && categoryMode === 'preset' ? 'bg-stone-900 text-white font-bold border-stone-900' : 'bg-stone-50 border-stone-200 text-stone-700 hover:bg-stone-100'">
                                <div class="font-bold">Thermal Windows</div>
                                <div class="text-[10px] opacity-75 font-normal">نوافذ عزل 50°م</div>
                            </button>

                            <button type="button" @click="selectCategory('family_living'); categoryMode = 'preset'"
                                    class="p-2.5 rounded-xl border text-left text-xs transition-all cursor-pointer"
                                    :class="categoryKey === 'family_living' && categoryMode === 'preset' ? 'bg-stone-900 text-white font-bold border-stone-900' : 'bg-stone-50 border-stone-200 text-stone-700 hover:bg-stone-100'">
                                <div class="font-bold">Family Lounges</div>
                                <div class="text-[10px] opacity-75 font-normal">صالات العائلة</div>
                            </button>
                        </div>

                        {{-- Hidden inputs for preset category --}}
                        <input type="hidden" name="category_key" :value="categoryKey">
                        <input type="hidden" name="category_name_en" :value="categoryNameEn">
                        <input type="hidden" name="category_name_ar" :value="categoryNameAr">

                        {{-- Custom category toggle if seller wants a brand new category --}}
                        <div class="pt-2">
                            <button type="button" @click="categoryMode = (categoryMode === 'custom' ? 'preset' : 'custom')"
                                    class="text-xs font-bold text-amber-900 hover:underline flex items-center gap-1">
                                <span x-text="categoryMode === 'custom' ? '← Choose standard category' : '+ Or create a brand new category name'"></span>
                            </button>

                            <template x-if="categoryMode === 'custom'">
                                <div class="mt-3 p-3 bg-amber-50/60 rounded-2xl border border-amber-200 space-y-3">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-[11px] font-bold text-stone-700 mb-1">New Category (English)</label>
                                            <input type="text" x-model="categoryNameEn" placeholder="e.g. Master Bedroom Glass Wall"
                                                   class="w-full px-3 py-2 text-xs bg-white border border-stone-300 rounded-xl"
                                                   @input="categoryKey = categoryNameEn.toLowerCase().replace(/[^a-z0-9]/g, '_')">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-stone-700 mb-1">New Category (Arabic)</label>
                                            <input type="text" x-model="categoryNameAr" placeholder="e.g. واجهات غرف النوم الزجاجية"
                                                   class="w-full px-3 py-2 text-xs bg-white border border-stone-300 rounded-xl">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- SECTION 3 — TITLES & DESCRIPTION --}}
                <div class="bg-white rounded-3xl border border-stone-200 p-6 shadow-sm space-y-4">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-stone-900 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-stone-900 text-white text-xs flex items-center justify-center font-bold">3</span>
                        Titles & Details · العنوان والتفاصيل
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">
                                English Title <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="title_en" x-model="titleEn" required
                                   placeholder="e.g. The Royal Majlis Privacy Partition"
                                   class="w-full px-3.5 py-2.5 text-xs bg-stone-50 border border-stone-300 rounded-xl font-bold focus:ring-2 focus:ring-stone-900">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">
                                Arabic Title (العنوان بالعربي) <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="title_ar" x-model="titleAr" required dir="rtl"
                                   placeholder="مثال: فاصل الخصوصية للمجلس الملكي والمشربية العصرية"
                                   class="w-full px-3.5 py-2.5 text-xs bg-stone-50 border border-stone-300 rounded-xl font-bold focus:ring-2 focus:ring-stone-900 text-right">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">
                            Location Badge · اسم الحي والمشروع <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="location_tag" x-model="locationTag" required
                               placeholder="e.g. Riyadh Villa · Hittin District / Jeddah Seafront Villa"
                               class="w-full px-3.5 py-2.5 text-xs bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">
                            Details & Architectural Summary <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="tagline" x-model="tagline" required rows="3"
                                  placeholder="Describe the aluminum alloy finish, glass ribs, acoustic dampers, climate suitability..."
                                  class="w-full px-3.5 py-2.5 text-xs bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900"></textarea>
                    </div>
                </div>

                {{-- SECTION 4 — 3 SAUDI FEATURES (GREEN CHECKMARKS) --}}
                <div class="bg-white rounded-3xl border border-stone-200 p-6 shadow-sm space-y-4">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-stone-900 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-stone-900 text-white text-xs flex items-center justify-center font-bold">4</span>
                        Key Saudi Highlights (3 Checkmarks) · مميزات التصميم
                    </h2>
                    <p class="text-xs text-stone-500">
                        These 3 bullet points appear with green checkmarks directly on the storefront card.
                    </p>

                    <div class="space-y-3">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs shrink-0">✓</span>
                            <input type="text" name="features[]" x-model="feature1" required
                                   placeholder="Feature 1 (e.g. Visual Privacy between Men’s Majlis & Dining Area)"
                                   class="w-full px-3 py-2 text-xs bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900">
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs shrink-0">✓</span>
                            <input type="text" name="features[]" x-model="feature2" required
                                   placeholder="Feature 2 (e.g. Acoustic Sound Dampening 38dB noise barrier)"
                                   class="w-full px-3 py-2 text-xs bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900">
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs shrink-0">✓</span>
                            <input type="text" name="features[]" x-model="feature3" required
                                   placeholder="Feature 3 (e.g. Champagne Gold Electro-Sealed Anodization)"
                                   class="w-full px-3 py-2 text-xs bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900">
                        </div>
                    </div>
                </div>

                {{-- SECTION 5 — SPECS & PRICING --}}
                <div class="bg-white rounded-3xl border border-stone-200 p-6 shadow-sm space-y-4">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-stone-900 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-stone-900 text-white text-xs flex items-center justify-center font-bold">5</span>
                        Specifications & Pricing (SAR) · المواصفات والأسعار
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">Dimensions</label>
                            <input type="text" name="dimensions" x-model="dimensions"
                                   placeholder='e.g. 96"H × 72"W (2.44m × 1.83m)'
                                   class="w-full px-3 py-2 text-xs bg-stone-50 border border-stone-300 rounded-xl">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">Finish / Fabric</label>
                            <input type="text" name="finish_or_fabric" x-model="finishOrFabric"
                                   placeholder="e.g. Champagne Gold Anodized Alloy"
                                   class="w-full px-3 py-2 text-xs bg-stone-50 border border-stone-300 rounded-xl">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">Core Material</label>
                            <input type="text" name="core_material" x-model="coreMaterial"
                                   placeholder="e.g. 10mm Fluted Ribbed Safety Glass"
                                   class="w-full px-3 py-2 text-xs bg-stone-50 border border-stone-300 rounded-xl">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">Hardware / Plinth</label>
                            <input type="text" name="hardware" x-model="hardware"
                                   placeholder="e.g. 3x2 Architectural Grid Mullions"
                                   class="w-full px-3 py-2 text-xs bg-stone-50 border border-stone-300 rounded-xl">
                        </div>
                    </div>

                    {{-- Pricing in SAR --}}
                    <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200/80 space-y-3 pt-4">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-amber-950 mb-1">
                                    Price in SAR (ريال سعودي) <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2.5 text-xs font-bold text-amber-800">﷼</span>
                                    <input type="number" step="1" name="price_sar" x-model="priceSar" required
                                           placeholder="10650"
                                           class="w-full pl-7 pr-3 py-2 text-sm bg-white border border-amber-300 rounded-xl font-bold text-stone-900 focus:ring-2 focus:ring-stone-900">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-stone-500 mb-1">
                                    Est. USD ($)
                                </label>
                                <input type="number" name="price_usd" :value="priceUsd" readonly
                                       class="w-full px-3 py-2 text-sm bg-stone-100 border border-stone-200 rounded-xl text-stone-600 font-mono">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-amber-900 mb-1">
                                    40% Deposit Required
                                </label>
                                <div class="px-3 py-2 bg-white border border-amber-300 rounded-xl text-sm font-bold text-amber-900">
                                    <span x-text="depositSar.toLocaleString() + ' SAR'"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Submit Buttons --}}
                <div class="flex items-center justify-between bg-white rounded-2xl border border-stone-200 px-6 py-4 shadow-sm">
                    <a href="{{ route('seller.villa-designs.index') }}" class="px-5 py-2.5 bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs font-bold rounded-xl transition-colors">
                        ← Cancel
                    </a>
                    <button type="submit"
                            class="px-8 py-3 bg-stone-900 hover:bg-stone-800 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-md transition-all hover:scale-105 active:scale-95 flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Publish to Storefront Showcase</span>
                    </button>
                </div>
            </div>

            {{-- Right Column: Live Sticky Preview (5 cols) --}}
            <div class="lg:col-span-5">
                <div class="sticky top-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-stone-500 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                            Live Storefront Card Preview
                        </span>
                        <span class="text-[10px] text-stone-400">Updates as you type</span>
                    </div>

                    {{-- Exact 1:1 Rendering of Card as on Storefront --}}
                    <div class="bg-white rounded-3xl border border-stone-200 shadow-xl overflow-hidden flex flex-col justify-between">
                        <div>
                            {{-- Image Preview with Badges --}}
                            <div class="relative aspect-16/10 overflow-hidden bg-stone-900">
                                <template x-if="previewUrl">
                                    <img :src="previewUrl" alt="Card Preview" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!previewUrl">
                                    <div class="w-full h-full flex flex-col items-center justify-center text-stone-500 text-xs gap-1 bg-stone-800">
                                        <span>📷</span>
                                        <span>No image selected yet</span>
                                    </div>
                                </template>

                                <div class="absolute inset-0 bg-gradient-to-t from-stone-950/85 via-stone-950/20 to-black/30"></div>

                                {{-- Top Badges --}}
                                <div class="absolute top-3 left-3 right-3 flex items-center justify-between gap-2">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-white/95 text-stone-900 shadow-xs"
                                          x-text="type === 'fitting' ? 'Architectural Fitting' : 'Bespoke Sofa'"></span>

                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-stone-900/90 text-amber-300 border border-stone-700 backdrop-blur-xs truncate max-w-[190px]"
                                          x-text="'📍 ' + (locationTag || 'Saudi Villa')"></span>
                                </div>

                                {{-- Title overlay --}}
                                <div class="absolute bottom-3 left-3 right-3 space-y-0.5">
                                    <h4 class="text-base font-serif font-bold text-white leading-tight drop-shadow-md"
                                        x-text="titleEn || 'Your Architectural Title Here'"></h4>
                                    <p class="text-[11px] text-amber-200/90 font-medium"
                                       x-text="titleAr || 'العنوان بالعربي يظهر هنا'"></p>
                                </div>
                            </div>

                            {{-- Details & Highlights --}}
                            <div class="p-5 space-y-4">
                                <p class="text-xs text-stone-600 line-clamp-2 leading-relaxed"
                                   x-text="tagline || 'Warm champagne gold anodized aluminum with privacy glass and acoustic seal.'"></p>

                                <div class="space-y-1.5 pt-1">
                                    <div class="flex items-start gap-1.5 text-[11px] text-stone-800 font-medium">
                                        <span class="text-emerald-600 font-bold shrink-0 mt-0.5">✓</span>
                                        <span x-text="feature1 || 'Key Highlight Feature 1'"></span>
                                    </div>
                                    <div class="flex items-start gap-1.5 text-[11px] text-stone-800 font-medium">
                                        <span class="text-emerald-600 font-bold shrink-0 mt-0.5">✓</span>
                                        <span x-text="feature2 || 'Key Highlight Feature 2'"></span>
                                    </div>
                                    <div class="flex items-start gap-1.5 text-[11px] text-stone-800 font-medium">
                                        <span class="text-emerald-600 font-bold shrink-0 mt-0.5">✓</span>
                                        <span x-text="feature3 || 'Key Highlight Feature 3'"></span>
                                    </div>
                                </div>

                                {{-- Specs Box --}}
                                <div class="p-3 rounded-2xl bg-stone-50 border border-stone-200/80 text-xs space-y-1">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[11px] text-stone-500">Dimensions:</span>
                                        <span class="font-bold text-stone-900" x-text="dimensions || 'Custom'"></span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-[11px] text-stone-500">Finish / Fabric:</span>
                                        <span class="font-bold text-stone-900 truncate max-w-[170px]" x-text="finishOrFabric || 'Anodized Alloy'"></span>
                                    </div>
                                </div>

                                {{-- Pricing --}}
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('image_file_input');
    const previewBox = document.getElementById('image_preview_box');
    const promptBox = document.getElementById('upload_prompt_box');
    const previewImg = document.getElementById('image_preview_img');
    const fileStatusText = document.getElementById('file_status_text');
    const dropzone = document.getElementById('dropzone_container');

    function updatePreview(file) {
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            if (previewImg) previewImg.src = e.target.result;
            if (previewBox) previewBox.style.display = 'block';
            if (promptBox) promptBox.style.display = 'none';

            // Also update live preview card on right side if present
            const livePreview = document.getElementById('live_card_img');
            if (livePreview) livePreview.src = e.target.result;
        };
        reader.readAsDataURL(file);

        if (fileStatusText) {
            const sizeMb = (file.size / (1024 * 1024)).toFixed(2);
            fileStatusText.innerHTML = '<span class="font-bold text-emerald-700">✓ Ready to upload:</span> <span class="font-mono text-stone-900 font-bold">' + file.name + '</span> (' + sizeMb + ' MB)';
        }
    }

    if (fileInput) {
        fileInput.addEventListener('change', function (e) {
            if (this.files && this.files[0]) {
                updatePreview(this.files[0]);
            }
        });
    }

    if (dropzone) {
        ['dragenter', 'dragover'].forEach(name => {
            dropzone.addEventListener(name, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.add('border-amber-500', 'bg-amber-50/70', 'ring-2', 'ring-amber-300');
            }, false);
        });

        ['dragleave', 'drop'].forEach(name => {
            dropzone.addEventListener(name, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.remove('border-amber-500', 'bg-amber-50/70', 'ring-2', 'ring-amber-300');
            }, false);
        });

        dropzone.addEventListener('drop', (e) => {
            if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) {
                const file = e.dataTransfer.files[0];
                fileInput.files = e.dataTransfer.files;
                updatePreview(file);
            }
        }, false);
    }
});
</script>
@endsection
