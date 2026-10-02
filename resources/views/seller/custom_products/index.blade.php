@extends('layouts.seller')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-stone-200">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-amber-900 mb-1">
                <span class="inline-block w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                <span>Architectural Made-to-Measure · إدارة المنتجات المعمارية</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-stone-900 tracking-tight">
                Custom Architectural Products
            </h1>
            <p class="text-xs sm:text-sm text-stone-600 mt-1 max-w-2xl">
                Edit, delete, and control the exact serial display order for all made-to-measure thermal windows, pivot doors, and partitions on the storefront.
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
            <a href="{{ route('seller.products.create_custom') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-900 hover:bg-stone-900 text-amber-100 hover:text-white text-xs font-bold uppercase tracking-wider transition-all shadow-md active:scale-95 cursor-pointer">
                <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>+ Add Architectural Product</span>
            </a>

            <a href="{{ route('seller.products.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-white border border-stone-300 hover:bg-stone-50 text-stone-800 text-xs font-bold uppercase tracking-wider transition-colors">
                <svg class="w-4 h-4 text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                <span>All Catalog</span>
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm font-medium flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-950 font-bold text-xs">✕</button>
        </div>
    @endif

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 rounded-2xl bg-white border border-stone-200 shadow-2xs">
            <span class="text-[11px] font-bold text-stone-400 uppercase tracking-wider block">Total Custom Products</span>
            <div class="flex items-baseline gap-2 mt-1">
                <span class="text-2xl font-serif font-bold text-stone-900">{{ $stats['total'] }}</span>
                <span class="text-xs text-stone-500">made-to-measure</span>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-stone-200 shadow-2xs">
            <span class="text-[11px] font-bold text-stone-400 uppercase tracking-wider block">Featured On Storefront</span>
            <div class="flex items-baseline gap-2 mt-1">
                <span class="text-2xl font-serif font-bold text-amber-900">{{ $stats['featured'] }}</span>
                <span class="text-xs text-amber-700 font-semibold">Hero Showcase Pick</span>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-stone-200 shadow-2xs">
            <span class="text-[11px] font-bold text-stone-400 uppercase tracking-wider block">Active Status</span>
            <div class="flex items-baseline gap-2 mt-1">
                <span class="text-2xl font-serif font-bold text-emerald-600">{{ $stats['in_stock'] }}</span>
                <span class="text-xs text-emerald-700 font-semibold">Available for Custom Order</span>
            </div>
        </div>
    </div>

    <!-- Search & Bulk Serial Form -->
    <form method="POST" action="{{ route('seller.custom_products.reorder') }}" id="reorderForm">
        @csrf
        <div class="bg-white p-4 rounded-2xl border border-stone-200 flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
            <div class="flex items-center gap-3">
                <div class="relative w-full sm:w-72">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search custom architectural..." class="w-full pl-9 pr-3 py-1.5 text-xs bg-stone-50 border border-stone-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-stone-900" onkeydown="if(event.key === 'Enter') { event.preventDefault(); window.location.href = '{{ route('seller.custom_products.index') }}?search=' + encodeURIComponent(this.value); }">
                    <svg class="w-4 h-4 text-stone-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                @if(!empty($search))
                    <a href="{{ route('seller.custom_products.index') }}" class="text-xs text-rose-600 hover:underline">Clear Search</a>
                @endif
            </div>

            <div class="flex items-center gap-3">
                <span class="text-xs text-stone-500 font-medium hidden sm:inline">
                    Change serial numbers below, then click:
                </span>
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-stone-900 hover:bg-stone-800 text-white text-xs font-bold uppercase tracking-wider rounded-xl transition-all shadow-xs cursor-pointer">
                    <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    <span>Save Serial Order</span>
                </button>
            </div>
        </div>

        <!-- Products Table -->
        <div class="bg-white rounded-2xl border border-stone-200 shadow-2xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-stone-200 bg-stone-50 text-[11px] font-bold text-stone-500 uppercase tracking-wider">
                            <th class="py-3.5 px-4 w-28 text-center">Serial #</th>
                            <th class="py-3.5 px-4">Custom Product Spec</th>
                            <th class="py-3.5 px-4">Category & Material</th>
                            <th class="py-3.5 px-4">Dimensions</th>
                            <th class="py-3.5 px-4">Price Range (SAR)</th>
                            <th class="py-3.5 px-4">Options Built</th>
                            <th class="py-3.5 px-4 text-right">Manage Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 text-xs">
                        @forelse($products as $p)
                            <tr class="hover:bg-amber-50/30 transition-colors">
                                <!-- Serial Number Input -->
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <span class="text-stone-400 font-mono text-xs font-bold">#</span>
                                        <input
                                            type="number"
                                            name="serials[{{ $p->id }}]"
                                            value="{{ $p->sort_order ?? $loop->iteration }}"
                                            min="1"
                                            max="999"
                                            class="w-14 px-2 py-1 text-center font-mono font-bold text-xs bg-stone-50 border border-stone-300 rounded-lg focus:ring-2 focus:ring-stone-900 focus:bg-white transition-all"
                                            title="Display Serial Number on Storefront"
                                        />
                                    </div>
                                </td>

                                <!-- Custom Product Spec & Photo -->
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        @if($p->image_url)
                                            <img src="{{ $p->image_url }}" alt="{{ $p->name }}" class="w-14 h-14 rounded-xl object-cover border border-stone-200 shrink-0 shadow-2xs" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=120&q=80';">
                                        @else
                                            <div class="w-14 h-14 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-700 text-xl shrink-0">
                                                🪟
                                            </div>
                                        @endif
                                        <div class="truncate max-w-[260px]">
                                            <div class="font-bold text-stone-900 text-xs truncate" title="{{ $p->name }}">
                                                {{ $p->name }}
                                            </div>
                                            <div class="text-[11px] text-stone-500 truncate mt-0.5" title="{{ $p->tagline }}">
                                                {{ $p->tagline ?? $p->slug }}
                                            </div>
                                            <div class="text-[10px] text-stone-400 font-mono mt-1 flex items-center gap-1.5">
                                                <span class="px-1.5 py-0.2 bg-amber-100 text-amber-900 font-bold rounded text-[9px] border border-amber-300">
                                                    Made-to-Measure
                                                </span>
                                                @if($p->is_featured)
                                                    <span class="text-amber-800 font-bold">★ Hero Pick</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Category & Material Spec -->
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-stone-100 text-stone-800 border border-stone-200">
                                        {{ $p->category->name ?? 'Architectural Fitting' }}
                                    </span>
                                    <div class="text-[11px] text-stone-600 mt-1 font-medium truncate max-w-[180px]" title="{{ $p->materials }}">
                                        {{ $p->materials ?? 'Alupco Structural Aluminum' }}
                                    </div>
                                </td>

                                <!-- Dimensions -->
                                <td class="py-3.5 px-4 font-mono text-[11px] text-stone-700">
                                    {{ $p->dimensions ?? 'Fully Customizable' }}
                                </td>

                                <!-- Price Range -->
                                <td class="py-3.5 px-4">
                                    @if($p->compare_at_price && $p->compare_at_price > $p->price)
                                        <div class="font-bold text-amber-950 text-sm font-mono">
                                            ﷼{{ number_format($p->price, 0) }} – ﷼{{ number_format($p->compare_at_price, 0) }}
                                        </div>
                                        <div class="text-[10px] text-stone-400">Custom Scope Range</div>
                                    @else
                                        <div class="font-bold text-stone-900 text-sm font-mono">
                                            ﷼{{ number_format($p->price, 0) }} SAR
                                        </div>
                                    @endif
                                </td>

                                <!-- Options Built -->
                                <td class="py-3.5 px-4">
                                    @php
                                        $opts = $p->customization_options ?? [];
                                        $shuttersCount = count($opts['shutters_options'] ?? []);
                                        $glassCount = count($opts['glass_options'] ?? []);
                                        $alloyCount = count($opts['aluminum_options'] ?? []);
                                    @endphp
                                    <div class="text-[11px] space-y-0.5 text-stone-600">
                                        <div><span class="font-bold text-stone-900">{{ $shuttersCount }}</span> shutters</div>
                                        <div><span class="font-bold text-stone-900">{{ $glassCount }}</span> glass types</div>
                                        <div><span class="font-bold text-stone-900">{{ $alloyCount }}</span> alloy grades</div>
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Edit button -->
                                        <a href="{{ route('seller.products.edit', $p->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-stone-700 hover:text-stone-900 bg-stone-100 hover:bg-stone-200 rounded-lg text-xs font-bold transition-colors" title="Edit Custom Product">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            <span>Edit</span>
                                        </a>

                                        <!-- Storefront View link -->
                                        <a href="{{ env('FRONTEND_URL', 'http://localhost:3000') }}/custom-order/{{ $p->slug }}" target="_blank" class="p-1.5 text-stone-500 hover:text-stone-900 hover:bg-stone-100 rounded-lg transition-colors" title="View on Customer Storefront">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </a>

                                        <!-- Delete button -->
                                        <button
                                            type="submit"
                                            form="delete-form-{{ $p->id }}"
                                            onclick="return confirm('Are you sure you want to delete custom architectural product: \'{{ addslashes($p->name) }}\'?');"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 text-rose-600 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 rounded-lg text-xs font-bold transition-colors cursor-pointer"
                                            title="Delete Custom Product"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            <span>Delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-stone-500">
                                    <div class="text-3xl mb-2">🪟</div>
                                    <p class="text-sm font-semibold text-stone-800">No custom architectural products found</p>
                                    <p class="text-xs text-stone-500 mt-1">Add your first made-to-measure thermal window or partition.</p>
                                    <a href="{{ route('seller.products.create_custom') }}" class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 bg-amber-900 text-white rounded-xl text-xs font-bold">
                                        + Add Architectural Product
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($products->hasPages())
                <div class="p-4 border-t border-stone-200">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </form>

    <!-- Hidden Individual Delete Forms -->
    @foreach($products as $p)
        <form id="delete-form-{{ $p->id }}" method="POST" action="{{ route('seller.custom_products.destroy', $p->id) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
</div>
@endsection
