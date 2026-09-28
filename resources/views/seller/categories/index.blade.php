@extends('layouts.seller')

@section('content')
<div class="space-y-6" x-data="{
    newCatModal: false,
    newSubModal: false,
    selectedCatId: null,
    selectedCatName: '',
    openAddSub(catId, catName) {
        this.selectedCatId = catId;
        this.selectedCatName = catName;
        this.newSubModal = true;
    }
}">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-stone-200">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-amber-900 mb-1">
                <span class="inline-block w-2 h-2 rounded-full bg-amber-500"></span>
                <span>Taxonomy Architecture</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-stone-900 tracking-tight">
                Categories & Subcategories
            </h1>
            <p class="text-xs sm:text-sm text-stone-600 mt-1 max-w-2xl">
                Organize store navigation and filterable classifications for both ready-made and custom architectural furnishings.
            </p>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <button type="button" @click="newCatModal = true" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-stone-900 hover:bg-stone-800 text-white text-xs font-bold uppercase tracking-wider transition-colors shadow-xs cursor-pointer">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Add Category</span>
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

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($categories as $cat)
            <div class="bg-white rounded-3xl border border-stone-200 shadow-2xs overflow-hidden flex flex-col justify-between">
                <div>
                    <!-- Banner Image / Header -->
                    <div class="h-36 relative overflow-hidden bg-stone-900">
                        <img src="{{ $cat->image_url }}" alt="{{ $cat->name }}" class="w-full h-full object-cover filter brightness-75">
                        <div class="absolute inset-0 bg-gradient-to-t from-stone-950/80 via-transparent to-transparent"></div>
                        <div class="absolute bottom-3 left-4 right-4 flex items-end justify-between">
                            <div>
                                <h3 class="text-lg font-serif font-bold text-white drop-shadow-sm">{{ $cat->name }}</h3>
                                <p class="text-[11px] text-stone-200 font-mono">slug: {{ $cat->slug }}</p>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/90 text-stone-900 shadow-xs">
                                {{ $cat->products_count }} Products
                            </span>
                        </div>
                    </div>

                    <div class="p-5 space-y-4">
                        <p class="text-xs text-stone-600 leading-relaxed">
                            {{ $cat->description ?? 'No description entered.' }}
                        </p>

                        <!-- Subcategories -->
                        <div class="pt-3 border-t border-stone-100">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-stone-500">
                                    Subcategories ({{ $cat->subcategories->count() }})
                                </span>
                                <button type="button" @click="openAddSub({{ $cat->id }}, '{{ addslashes($cat->name) }}')" class="text-[11px] font-bold text-amber-900 hover:text-amber-950 hover:underline cursor-pointer">
                                    + Add Subcategory
                                </button>
                            </div>

                            <div class="flex flex-wrap gap-1.5">
                                @forelse($cat->subcategories as $sub)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs bg-stone-100 border border-stone-200 text-stone-800">
                                        <span class="font-medium">{{ $sub->name }}</span>
                                        <span class="text-[10px] text-stone-400 font-bold">({{ $sub->products_count }})</span>
                                    </span>
                                @empty
                                    <span class="text-xs text-stone-400 italic">No subcategories defined yet.</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-5 py-3 bg-stone-50/70 border-t border-stone-100 flex items-center justify-between text-xs">
                    <a href="{{ route('seller.products.index', ['category_id' => $cat->id]) }}" class="font-bold text-stone-800 hover:text-stone-950 hover:underline">
                        View Products &rarr;
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <!-- MODAL: ADD CATEGORY -->
    <div x-show="newCatModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-950/70 backdrop-blur-xs">
        <div @click.away="newCatModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-stone-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-stone-200">
                <h3 class="text-lg font-serif font-bold text-stone-900">Add New Category</h3>
                <button type="button" @click="newCatModal = false" class="text-stone-400 hover:text-stone-900 text-lg">✕</button>
            </div>

            <form method="POST" action="{{ route('seller.categories.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">Category Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="e.g. Architectural Partitions" class="w-full px-3 py-2 text-xs bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900">
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-700">Category Cover Photo</label>
                    <input type="file" name="image_file" accept="image/*" class="w-full text-xs text-stone-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-stone-900 file:text-white hover:file:bg-stone-800">
                    <p class="text-[10px] text-stone-400">Or provide image URL below:</p>
                    <input type="url" name="image_url" placeholder="https://images.unsplash.com/..." class="w-full px-3 py-2 text-xs bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">Description</label>
                    <textarea name="description" rows="3" placeholder="Brief editorial description..." class="w-full px-3 py-2 text-xs bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900"></textarea>
                </div>

                <div class="pt-3 border-t border-stone-200 flex items-center justify-end gap-3">
                    <button type="button" @click="newCatModal = false" class="px-4 py-2 bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs font-bold rounded-xl">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-stone-900 hover:bg-stone-800 text-white text-xs font-bold rounded-xl">Create Category</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: ADD SUBCATEGORY -->
    <div x-show="newSubModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-950/70 backdrop-blur-xs">
        <div @click.away="newSubModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-stone-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-stone-200">
                <div>
                    <span class="text-[10px] font-bold text-amber-800 uppercase tracking-widest" x-text="'Parent: ' + selectedCatName"></span>
                    <h3 class="text-lg font-serif font-bold text-stone-900">Add Subcategory</h3>
                </div>
                <button type="button" @click="newSubModal = false" class="text-stone-400 hover:text-stone-900 text-lg">✕</button>
            </div>

            <form method="POST" action="{{ route('seller.subcategories.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="category_id" :value="selectedCatId">

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">Subcategory Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="e.g. Swivel Armchairs" class="w-full px-3 py-2 text-xs bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">Description (Optional)</label>
                    <textarea name="description" rows="2" placeholder="Subcategory details..." class="w-full px-3 py-2 text-xs bg-stone-50 border border-stone-300 rounded-xl focus:ring-2 focus:ring-stone-900"></textarea>
                </div>

                <div class="pt-3 border-t border-stone-200 flex items-center justify-end gap-3">
                    <button type="button" @click="newSubModal = false" class="px-4 py-2 bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs font-bold rounded-xl">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-stone-900 hover:bg-stone-800 text-white text-xs font-bold rounded-xl">Save Subcategory</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
