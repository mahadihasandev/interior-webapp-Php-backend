@extends('layouts.seller')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-stone-200">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-amber-900 mb-1">
                <span class="inline-block w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                <span>Storefront Showcase · واجهة المتجر الرئيسية</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-stone-900 tracking-tight">
                Villa Architectural Designs & Majlis
            </h1>
            <p class="text-xs sm:text-sm text-stone-600 mt-1 max-w-2xl">
                Manage the live custom inspiration cards displayed at the top of the storefront homepage. Add new categories, upload real project photos, set SAR prices, and configure Saudi villa specifications.
            </p>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('seller.villa-designs.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-950 hover:bg-stone-900 text-amber-200 hover:text-white text-xs font-bold uppercase tracking-wider transition-all shadow-md hover:scale-105 active:scale-95 cursor-pointer">
                <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Add Villa Design</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-white border border-stone-200 shadow-2xs">
            <span class="text-[11px] font-bold text-stone-400 uppercase tracking-wider block">Total Villa Designs</span>
            <div class="flex items-baseline gap-2 mt-1">
                <span class="text-2xl font-serif font-bold text-stone-900">{{ $stats['total'] }}</span>
                <span class="text-xs text-stone-500">showcased</span>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-stone-200 shadow-2xs">
            <span class="text-[11px] font-bold text-stone-400 uppercase tracking-wider block">Fittings & Partitions</span>
            <div class="flex items-baseline gap-2 mt-1">
                <span class="text-2xl font-serif font-bold text-amber-900">{{ $stats['fittings'] }}</span>
                <span class="text-xs text-stone-500">windows & glass</span>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-stone-200 shadow-2xs">
            <span class="text-[11px] font-bold text-stone-400 uppercase tracking-wider block">Bespoke Majlis & Sofas</span>
            <div class="flex items-baseline gap-2 mt-1">
                <span class="text-2xl font-serif font-bold text-stone-900">{{ $stats['sofas'] }}</span>
                <span class="text-xs text-stone-500">salons</span>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-stone-200 shadow-2xs">
            <span class="text-[11px] font-bold text-stone-400 uppercase tracking-wider block">Active On Storefront</span>
            <div class="flex items-baseline gap-2 mt-1">
                <span class="text-2xl font-serif font-bold text-emerald-600">{{ $stats['active'] }}</span>
                <span class="text-xs text-emerald-700 font-semibold">Live Now</span>
            </div>
        </div>
    </div>

    <!-- Filter & Category Pills (Matching Storefront Tabs) -->
    <div class="bg-white rounded-3xl border border-stone-200 p-4 shadow-xs space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            {{-- Category Filter Pills --}}
            <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
                <a href="{{ route('seller.villa-designs.index', array_filter(['search' => $search, 'type' => $type])) }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 {{ empty($categoryKey) ? 'bg-stone-900 text-white shadow-xs' : 'bg-stone-100 hover:bg-stone-200 text-stone-700' }}">
                    All Villa Designs
                </a>
                @foreach($availableCategories as $cat)
                    <a href="{{ route('seller.villa-designs.index', array_filter(['category' => $cat->category_key, 'search' => $search, 'type' => $type])) }}"
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 flex items-center gap-1.5 {{ $categoryKey === $cat->category_key ? 'bg-stone-900 text-white shadow-xs' : 'bg-stone-100 hover:bg-stone-200 text-stone-700' }}">
                        <span>{{ $cat->category_name_en }}</span>
                        <span class="text-[10px] opacity-75 font-normal">({{ $cat->category_name_ar }})</span>
                    </a>
                @endforeach
            </div>

            {{-- Search & Type Filter --}}
            <form method="GET" action="{{ route('seller.villa-designs.index') }}" class="flex items-center gap-2 shrink-0">
                @if(!empty($categoryKey))
                    <input type="hidden" name="category" value="{{ $categoryKey }}">
                @endif
                <select name="type" onchange="this.form.submit()" class="px-3 py-1.5 text-xs bg-stone-50 border border-stone-300 rounded-xl font-medium focus:ring-1 focus:ring-stone-900 focus:outline-none">
                    <option value="">All Types</option>
                    <option value="fitting" {{ $type === 'fitting' ? 'selected' : '' }}>Architectural Fittings</option>
                    <option value="sofa" {{ $type === 'sofa' ? 'selected' : '' }}>Bespoke Sofas</option>
                </select>

                <div class="relative">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search title or district..."
                           class="w-44 sm:w-56 pl-8 pr-3 py-1.5 text-xs bg-stone-50 border border-stone-300 rounded-xl focus:ring-1 focus:ring-stone-900 focus:outline-none">
                    <svg class="w-3.5 h-3.5 text-stone-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </form>
        </div>
    </div>

    <!-- Cards Grid (Exact 1:1 Visual Rendering Matching User Screenshot) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($designs as $design)
            <div class="bg-white rounded-3xl border border-stone-200 hover:border-stone-400 shadow-2xs hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col justify-between">
                <div>
                    {{-- Photo Box with Exact Storefront Badges --}}
                    <div class="relative aspect-16/10 overflow-hidden bg-stone-950">
                        <img src="{{ $design->photo_url }}" alt="{{ $design->title_en }}"
                             class="w-full h-full object-cover object-center filter brightness-[0.92]"
                             onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=600&q=80';">
                        <div class="absolute inset-0 bg-gradient-to-t from-stone-950/85 via-stone-950/20 to-black/30"></div>

                        {{-- Top Badges --}}
                        <div class="absolute top-3 left-3 right-3 flex items-center justify-between gap-2">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-white/95 text-stone-900 shadow-xs">
                                {{ $design->type === 'fitting' ? 'Architectural Fitting' : 'Bespoke Sofa' }}
                            </span>

                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-stone-900/90 text-amber-300 border border-stone-700 backdrop-blur-xs truncate max-w-[190px]">
                                📍 {{ $design->location_tag }}
                            </span>
                        </div>

                        {{-- Category Tag bottom right --}}
                        <div class="absolute top-11 right-3">
                            <span class="px-2 py-0.5 rounded-md text-[9px] font-bold bg-amber-500/90 text-stone-950 shadow-xs">
                                {{ $design->category_name_en }}
                            </span>
                        </div>

                        {{-- Title & Arabic Subtitle overlay --}}
                        <div class="absolute bottom-3 left-3 right-3 space-y-0.5">
                            <h3 class="text-base font-serif font-bold text-white leading-tight drop-shadow-md">
                                {{ $design->title_en }}
                            </h3>
                            <p class="text-[11px] text-amber-200/90 font-medium">
                                {{ $design->title_ar }}
                            </p>
                        </div>
                    </div>

                    {{-- Details & Highlights --}}
                    <div class="p-5 space-y-4">
                        {{-- Tagline / Details --}}
                        <p class="text-xs text-stone-600 line-clamp-2 leading-relaxed">
                            {{ $design->tagline }}
                        </p>

                        {{-- 3 Key Features (Green Checkmarks) --}}
                        <div class="space-y-1.5 pt-1">
                            @foreach($design->features ?? [] as $feat)
                                <div class="flex items-start gap-1.5 text-[11px] text-stone-800 font-medium">
                                    <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span class="line-clamp-1">{{ $feat }}</span>
                                </div>
                            @endforeach
                        </div>

                        {{-- Specs Pill Box --}}
                        <div class="p-3 rounded-2xl bg-stone-50 border border-stone-200/80 text-xs space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] text-stone-500">Dimensions:</span>
                                <span class="font-bold text-stone-900 truncate max-w-[170px]">{{ $design->specs['dimensions'] ?? 'Custom' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] text-stone-500">Finish / Fabric:</span>
                                <span class="font-bold text-stone-900 truncate max-w-[170px]" title="{{ $design->specs['finishOrFabric'] ?? '' }}">
                                    {{ $design->specs['finishOrFabric'] ?? 'Architectural Grade' }}
                                </span>
                            </div>
                        </div>

                        {{-- Pricing Box --}}
                        <div class="flex items-baseline justify-between pt-2 border-t border-stone-100">
                            <div>
                                <span class="text-[9px] uppercase tracking-wider text-stone-400 font-bold block">Estimated Custom Price</span>
                                <div class="flex items-baseline gap-1 mt-0.5">
                                    <span class="text-base font-serif font-bold text-stone-900">
                                        {{ number_format($design->price_sar) }} SAR
                                    </span>
                                    <span class="text-[10px] text-stone-400 font-mono">
                                        (${{ number_format($design->price_usd) }})
                                    </span>
                                </div>
                            </div>

                            <div class="text-right">
                                <span class="text-[9px] uppercase tracking-wider text-amber-800 font-bold block">40% Deposit to Build</span>
                                <span class="text-xs font-bold text-amber-900 mt-0.5 block">
                                    {{ number_format($design->advance_deposit_sar) }} SAR
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Bar --}}
                <div class="px-5 py-3 bg-stone-50/70 border-t border-stone-100 flex items-center justify-between">
                    <span class="inline-flex items-center gap-1.5 text-[10px] font-bold {{ $design->is_active ? 'text-emerald-700' : 'text-stone-400' }}">
                        <span class="w-2 h-2 rounded-full {{ $design->is_active ? 'bg-emerald-500' : 'bg-stone-300' }}"></span>
                        {{ $design->is_active ? 'Active on Storefront' : 'Draft / Hidden' }}
                    </span>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('seller.villa-designs.edit', $design) }}"
                           class="px-3 py-1.5 text-xs font-bold text-stone-700 bg-white hover:bg-stone-100 border border-stone-200 rounded-lg shadow-2xs transition-colors">
                            Edit
                        </a>

                        <form method="POST" action="{{ route('seller.villa-designs.destroy', $design) }}"
                              onsubmit="return confirm('Delete this villa design from the storefront showcase?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer"
                                    title="Delete Villa Design">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-stone-200">
                <div class="w-16 h-16 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 flex items-center justify-center mx-auto mb-4 text-2xl">
                    🏰
                </div>
                <h3 class="text-lg font-serif font-bold text-stone-900">No Villa Designs Found</h3>
                <p class="text-xs text-stone-500 mt-1 max-w-sm mx-auto">
                    No designs match your filter criteria. Click below to add a new design to the storefront showcase.
                </p>
                <div class="mt-5">
                    <a href="{{ route('seller.villa-designs.create') }}" class="px-5 py-2.5 bg-stone-900 text-white text-xs font-bold rounded-xl inline-flex items-center gap-2">
                        + Add Villa Design
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($designs->hasPages())
        <div class="pt-4">
            {{ $designs->links() }}
        </div>
    @endif
</div>
@endsection
