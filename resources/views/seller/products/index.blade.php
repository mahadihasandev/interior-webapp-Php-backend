@extends('layouts.seller')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-stone-200">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-amber-900 mb-1">
                <span class="inline-block w-2 h-2 rounded-full bg-amber-500"></span>
                <span>Catalog & Inventory System</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-stone-900 tracking-tight">
                Ready-Made Product Catalog
            </h1>
            <p class="text-xs sm:text-sm text-stone-600 mt-1 max-w-2xl">
                Manage all finished inventory items sold across living, bedroom, lighting, dining, and decor.
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
            <a href="{{ route('seller.products.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-stone-900 hover:bg-stone-800 text-white text-xs font-bold uppercase tracking-wider transition-colors shadow-xs">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Add Product</span>
            </a>
            <a href="{{ route('seller.products.create_custom') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-800 hover:bg-amber-900 text-white text-xs font-bold uppercase tracking-wider transition-colors shadow-xs">
                <svg class="w-4 h-4 text-amber-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/></svg>
                <span>+ Add Custom Order</span>
            </a>
            <a href="{{ route('seller.orders.ready_made') }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-white border border-stone-300 hover:bg-stone-50 text-stone-800 text-xs font-bold uppercase tracking-wider transition-colors">
                <svg class="w-4 h-4 text-stone-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                <span class="hidden sm:inline">Sales List</span>
            </a>
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

    <!-- Search & Filter Controls -->
    <div class="bg-white p-4 rounded-2xl border border-stone-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form method="GET" action="{{ route('seller.products.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <div class="relative w-full sm:w-64">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search products, materials..." class="w-full pl-9 pr-3 py-1.5 text-xs bg-stone-50 border border-stone-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-stone-900">
                <svg class="w-4 h-4 text-stone-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>

            <!-- Type filter -->
            <select name="product_type" class="px-3 py-1.5 text-xs bg-stone-50 border border-stone-300 rounded-xl font-medium" onchange="this.form.submit()">
                <option value="">All Catalog Types</option>
                <option value="custom_fit" {{ ($productType ?? '') === 'custom_fit' ? 'selected' : '' }}>🪟 Custom Orders Only</option>
                <option value="ready_made" {{ ($productType ?? '') === 'ready_made' ? 'selected' : '' }}>📦 Ready-Made Only</option>
            </select>

            <!-- Category filter -->
            <select name="category_id" class="px-3 py-1.5 text-xs bg-stone-50 border border-stone-300 rounded-xl" onchange="this.form.submit()">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>

            <!-- Brand filter -->
            <select name="brand_id" class="px-3 py-1.5 text-xs bg-stone-50 border border-stone-300 rounded-xl" onchange="this.form.submit()">
                <option value="">All Brands & Studios</option>
                @foreach($brands as $b)
                    <option value="{{ $b->id }}" {{ $brandId == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                @endforeach
            </select>

            <button type="submit" class="px-3.5 py-1.5 bg-stone-900 hover:bg-stone-800 text-white text-xs font-bold rounded-xl shadow-xs">
                Filter
            </button>

            @if(!empty($search) || !empty($categoryId) || !empty($brandId))
                <a href="{{ route('seller.products.index') }}" class="text-xs text-rose-600 hover:underline">Reset</a>
            @endif
        </form>

        <div class="text-xs text-stone-500 shrink-0 font-medium">
            Showing <span class="font-bold text-stone-900">{{ $products->total() }}</span> catalog items
        </div>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-2xl border border-stone-200 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-stone-200 bg-stone-50 text-[11px] font-bold text-stone-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4">Item & SKU</th>
                        <th class="py-3.5 px-4">Category & Subcategory</th>
                        <th class="py-3.5 px-4">Brand / Studio</th>
                        <th class="py-3.5 px-4">Retail Price</th>
                        <th class="py-3.5 px-4">Stock Level</th>
                        <th class="py-3.5 px-4">Featured</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-xs">
                    @forelse($products as $p)
                        <tr class="hover:bg-stone-50/70 transition-colors">
                            <!-- Item & SKU -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    @if($p->image_url)
                                        <img src="{{ $p->image_url }}" alt="{{ $p->name }}" class="w-12 h-12 rounded-lg object-cover border border-stone-200 shrink-0" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=120&q=80';">
                                    @else
                                        <div class="w-12 h-12 rounded-lg bg-stone-100 border border-stone-200 flex items-center justify-center text-stone-400 text-lg shrink-0">
                                            🪑
                                        </div>
                                    @endif
                                    <div class="truncate max-w-[240px]">
                                        <div class="font-bold text-stone-900 text-xs truncate" title="{{ $p->name }}">
                                            {{ $p->name }}
                                        </div>
                                        <div class="text-[11px] text-stone-500 truncate" title="{{ $p->tagline }}">
                                            {{ $p->tagline ?? $p->slug }}
                                        </div>
                                        <div class="text-[10px] text-stone-400 font-mono mt-0.5 flex items-center gap-1.5">
                                            <span>SKU: {{ strtoupper(substr($p->slug, 0, 8)) }}-{{ $p->id }}</span>
                                            @if($p->product_type === 'custom_fit')
                                                <span class="px-1.5 py-0.2 bg-amber-100 text-amber-900 font-bold rounded text-[9px] border border-amber-300">Made-to-Measure</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Category & Subcategory -->
                            <td class="py-3.5 px-4">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-stone-100 text-stone-800 border border-stone-200">
                                    {{ $p->category->name ?? 'Uncategorized' }}
                                </span>
                                @if($p->subcategory)
                                    <div class="text-[11px] text-stone-500 mt-1">
                                        ↳ {{ $p->subcategory->name }}
                                    </div>
                                @endif
                            </td>

                            <!-- Brand / Studio -->
                            <td class="py-3.5 px-4">
                                @if($p->brand)
                                    <div class="font-bold text-stone-800 text-xs">{{ $p->brand->name }}</div>
                                    <div class="text-[10px] text-stone-500">{{ $p->brand->origin_country }}</div>
                                @else
                                    <span class="text-stone-400 text-xs">In-House Studio</span>
                                @endif
                            </td>

                            <!-- Price -->
                            <td class="py-3.5 px-4">
                                @if($p->product_type === 'custom_fit' && $p->compare_at_price && $p->compare_at_price > $p->price)
                                    <div class="font-bold text-amber-900 text-sm">
                                        ﷼{{ number_format($p->price, 0) }} – ﷼{{ number_format($p->compare_at_price, 0) }}
                                    </div>
                                    <div class="text-[10px] text-stone-400">Custom Price Range</div>
                                @else
                                    <div class="font-bold text-stone-900 text-sm">
                                        ﷼{{ number_format($p->price, 2) }}
                                    </div>
                                    @if($p->compare_at_price)
                                        <div class="text-[10px] text-stone-400 line-through">
                                            ﷼{{ number_format($p->compare_at_price, 2) }}
                                        </div>
                                    @endif
                                @endif
                            </td>

                            <!-- Stock -->
                            <td class="py-3.5 px-4">
                                @if($p->stock > 5)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-900 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                        {{ $p->stock }} in stock
                                    </span>
                                @elseif($p->stock > 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                        Only {{ $p->stock }} left
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-900 border border-rose-200">
                                        Out of stock
                                    </span>
                                @endif
                            </td>

                            <!-- Featured -->
                            <td class="py-3.5 px-4">
                                @if($p->is_featured)
                                    <span class="text-xs font-bold text-amber-900 bg-amber-100 px-2 py-0.5 rounded-full border border-amber-300">
                                        ★ Featured
                                    </span>
                                @else
                                    <span class="text-stone-400 text-xs">—</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('seller.products.edit', $p->id) }}" class="p-1.5 text-stone-600 hover:text-stone-900 hover:bg-stone-100 rounded-lg transition-colors" title="Edit Product">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>

                                    <form method="POST" action="{{ route('seller.products.destroy', $p->id) }}" onsubmit="return confirm('Are you sure you want to delete this product: {{ addslashes($p->name) }}?');" class="inline" x-data="{ deleting: false }" @submit="if(deleting) return false; deleting = true;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" :disabled="deleting" class="p-1.5 text-stone-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed" title="Delete Product">
                                            <template x-if="!deleting">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </template>
                                            <template x-if="deleting">
                                                <svg class="w-4 h-4 animate-spin text-rose-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            </template>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-stone-500">
                                <div class="text-3xl mb-2">🪑</div>
                                <p class="text-sm font-semibold text-stone-800">No products found</p>
                                <p class="text-xs text-stone-500 mt-1">Add your first ready-made product to the catalog.</p>
                                <a href="{{ route('seller.products.create') }}" class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 bg-stone-900 text-white rounded-xl text-xs font-bold">
                                    + Add New Product
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
</div>
@endsection
