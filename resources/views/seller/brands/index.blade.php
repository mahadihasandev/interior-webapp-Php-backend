@extends('layouts.seller')

@section('content')
<div class="space-y-6" x-data="{ newBrandModal: false }">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-stone-200">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-amber-900 mb-1">
                <span class="inline-block w-2 h-2 rounded-full bg-amber-500"></span>
                <span>Partner Studios & Maker Registry</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-stone-900 tracking-tight">
                Brands & Design Studios
            </h1>
            <p class="text-xs sm:text-sm text-stone-600 mt-1 max-w-2xl">
                Manage registered artisan design workshops, timber joiners, structural glass foundries, and Italian lighting houses.
            </p>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <button type="button" @click="newBrandModal = true" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-stone-900 hover:bg-stone-800 text-white text-xs font-bold uppercase tracking-wider transition-colors shadow-xs cursor-pointer">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Register Brand / Studio</span>
            </button>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm font-medium flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-700 hover:text-emerald-950 font-bold text-xs">✕</button>
        </div>
    @endif

    <!-- Brands Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($brands as $brand)
            <div class="bg-white rounded-3xl border border-stone-200 shadow-2xs p-6 space-y-4 flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            @if($brand->logo_url)
                                <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" class="w-12 h-12 rounded-xl object-cover border border-stone-200" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=120&q=80';">
                            @else
                                <div class="w-12 h-12 rounded-xl bg-stone-900 text-white flex items-center justify-center font-serif font-bold text-lg">
                                    {{ substr($brand->name, 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <h3 class="font-bold text-stone-900 text-sm">{{ $brand->name }}</h3>
                                <p class="text-xs text-stone-500">📍 {{ $brand->origin_country ?? 'Global' }}</p>
                            </div>
                        </div>

                        @if($brand->is_featured)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-300">
                                Featured
                            </span>
                        @endif
                    </div>

                    <p class="text-xs text-stone-600 leading-relaxed">
                        {{ $brand->description ?? 'Bespoke design studio partnering with L’Atelier.' }}
                    </p>
                </div>

                <div class="pt-4 border-t border-stone-100 flex items-center justify-between text-xs">
                    <span class="text-stone-500 font-medium">
                        {{ $brand->products_count }} Products Active
                    </span>
                    <a href="{{ route('seller.products.index', ['brand_id' => $brand->id]) }}" class="font-bold text-stone-800 hover:text-stone-950 hover:underline">
                        View Products &rarr;
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <!-- MODAL: REGISTER BRAND -->
    <div x-show="newBrandModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-950/70 backdrop-blur-xs">
        <div @click.away="newBrandModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-stone-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-stone-200">
                <h3 class="text-lg font-serif font-bold text-stone-900">Register Brand / Studio</h3>
                <button type="button" @click="newBrandModal = false" class="text-stone-400 hover:text-stone-900 text-lg">✕</button>
            </div>

            <form method="POST" action="{{ route('seller.brands.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">Brand / Studio Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="e.g. Kyoto Artisan Woodcraft" class="w-full px-3 py-2 text-xs bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">Country of Origin</label>
                    <input type="text" name="origin_country" placeholder="e.g. Denmark, Japan, Italy, United States" class="w-full px-3 py-2 text-xs bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">Logo / Studio Photo URL</label>
                    <input type="url" name="logo_url" placeholder="https://images.unsplash.com/..." class="w-full px-3 py-2 text-xs bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">Studio Philosophy / Description</label>
                    <textarea name="description" rows="3" placeholder="Artisan techniques, materials specialization..." class="w-full px-3 py-2 text-xs bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900"></textarea>
                </div>

                <div class="flex items-center pt-2">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" class="w-4 h-4 rounded text-stone-900 border-stone-300">
                        <span class="text-xs font-bold text-stone-800">Featured Brand Partner</span>
                    </label>
                </div>

                <div class="pt-3 border-t border-stone-200 flex items-center justify-end gap-3">
                    <button type="button" @click="newBrandModal = false" class="px-4 py-2 bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs font-bold rounded-xl">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-stone-900 hover:bg-stone-800 text-white text-xs font-bold rounded-xl">Register Brand</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
