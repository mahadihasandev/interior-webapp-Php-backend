<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Seller & Studio Dashboard' }} — L’Atelier Architectural</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (local build via Vite) -->
    @vite(['resources/css/app.css'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full bg-stone-50 text-stone-900 antialiased font-sans selection:bg-amber-100 selection:text-amber-900" x-data="{ mobileMenuOpen: false }">
    <div class="min-h-full flex flex-col md:flex-row">
        <!-- Sidebar Navigation (Laptop md / Large Desktop lg) -->
        <aside class="hidden md:flex flex-col w-64 lg:w-72 bg-white border-r border-stone-200 shrink-0">
            <div class="h-20 flex items-center px-6 border-b border-stone-200">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-800 font-serif font-bold text-lg">
                        L
                    </div>
                    <div>
                        <span class="font-serif font-bold text-stone-900 tracking-tight text-base block">L’Atelier Studio</span>
                        <span class="text-[10px] text-stone-400 uppercase tracking-widest font-medium">Seller Portal</span>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-6 space-y-1 text-sm font-medium overflow-y-auto">
                <div class="text-[10px] font-bold text-stone-400 uppercase tracking-widest px-3 py-1">
                    Orders & Fulfillment
                </div>

                <a href="{{ route('seller.orders.ready_made') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('seller.orders.ready_made*') ? 'bg-stone-900 text-white shadow-xs font-semibold' : 'text-stone-700 hover:text-stone-900 hover:bg-stone-100' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('seller.orders.ready_made*') ? 'text-white' : 'text-stone-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <span>Ready-Made Sales List</span>
                </a>

                <a href="{{ route('seller.orders.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('seller.orders.index') ? 'bg-stone-900 text-white shadow-xs font-semibold' : 'text-stone-700 hover:text-stone-900 hover:bg-stone-100' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('seller.orders.index') ? 'text-white' : 'text-stone-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    <span>Custom Orders & Quotes</span>
                </a>

                <div class="text-[10px] font-bold text-stone-400 uppercase tracking-widest px-3 pt-5 pb-1">
                    Catalog Management
                </div>

                <a href="{{ route('seller.products.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('seller.products.index') || request()->routeIs('seller.products.edit') ? 'bg-stone-900 text-white shadow-xs font-semibold' : 'text-stone-700 hover:text-stone-900 hover:bg-stone-100' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('seller.products.index') ? 'text-white' : 'text-stone-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    <span>Product Catalog</span>
                </a>

                <a href="{{ route('seller.products.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('seller.products.create') ? 'bg-stone-900 text-white shadow-xs font-semibold' : 'text-stone-700 hover:text-stone-900 hover:bg-stone-100' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('seller.products.create') ? 'text-white' : 'text-stone-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Add New Product</span>
                </a>

                <a href="{{ route('seller.categories.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('seller.categories.*') ? 'bg-stone-900 text-white shadow-xs font-semibold' : 'text-stone-700 hover:text-stone-900 hover:bg-stone-100' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('seller.categories.*') ? 'text-white' : 'text-stone-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    <span>Categories & Subs</span>
                </a>

                <a href="{{ route('seller.brands.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('seller.brands.*') ? 'bg-stone-900 text-white shadow-xs font-semibold' : 'text-stone-700 hover:text-stone-900 hover:bg-stone-100' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('seller.brands.*') ? 'text-white' : 'text-stone-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <span>Brands & Studios</span>
                </a>

                <div class="text-[10px] font-bold text-amber-800 uppercase tracking-widest px-3 pt-5 pb-1 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    <span>Villa Showcase · تصاميم الفلل</span>
                </div>

                <a href="{{ route('seller.villa-designs.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('seller.villa-designs.index') || request()->routeIs('seller.villa-designs.edit') ? 'bg-amber-950 text-amber-100 shadow-xs font-semibold' : 'text-stone-700 hover:text-stone-900 hover:bg-amber-50/60' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('seller.villa-designs.index') || request()->routeIs('seller.villa-designs.edit') ? 'text-amber-400' : 'text-amber-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span>Villa Designs Showcase</span>
                </a>

                <a href="{{ route('seller.villa-designs.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('seller.villa-designs.create') ? 'bg-amber-950 text-amber-100 shadow-xs font-semibold' : 'text-stone-700 hover:text-stone-900 hover:bg-amber-50/60' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('seller.villa-designs.create') ? 'text-amber-400' : 'text-amber-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>+ Add Villa Design</span>
                </a>
            </nav>

            <!-- User footer profile -->
            <div class="p-4 border-t border-stone-200 bg-stone-50/50">
                @auth
                    <div class="flex items-center justify-between">
                        <div class="truncate mr-2">
                            <p class="text-xs font-bold text-stone-900 truncate">{{ auth()->user()->name }}</p>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="inline-flex px-1.5 py-0.5 text-[9px] font-bold rounded bg-amber-100 text-amber-900 border border-amber-200 uppercase tracking-wider">
                                    {{ auth()->user()->role ?? 'Staff' }}
                                </span>
                                @if(auth()->user()->vendor)
                                    <span class="text-[10px] text-stone-400 truncate">
                                        {{ auth()->user()->vendor->name }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('login') }}" title="Switch Demo Role" class="p-1.5 text-stone-400 hover:text-stone-900 rounded-lg hover:bg-stone-200/50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        </a>
                    </div>
                    <div class="mt-2 flex items-center justify-between text-[11px] text-stone-500 pt-2 border-t border-stone-200/60">
                        <a href="{{ route('login') }}" class="hover:text-stone-900 font-medium">Switch Account</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="hover:text-rose-600 font-medium">Sign Out</button>
                        </form>
                    </div>
                @else
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-stone-900">Demo Guest View</p>
                            <p class="text-[10px] text-stone-500">All vendors preview</p>
                        </div>
                        <a href="{{ route('login') }}" class="px-2.5 py-1 bg-stone-900 text-white rounded-lg text-xs font-semibold hover:bg-stone-800 transition-colors">
                            Sign In
                        </a>
                    </div>
                @endauth
            </div>
        </aside>

        <!-- Mobile Header Bar (Smartphone sm) -->
        <header class="md:hidden bg-white border-b border-stone-200 px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-800 font-serif font-bold">
                    L
                </div>
                <div>
                    <span class="font-serif font-bold text-stone-900 text-sm">L’Atelier Studio</span>
                    @auth
                        <span class="text-[10px] text-stone-500 block -mt-0.5">{{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
                    @endauth
                </div>
            </div>
            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('login') }}" class="text-[11px] text-stone-500 hover:text-stone-900 font-medium">Switch</a>
                @else
                    <a href="{{ route('login') }}" class="text-[11px] text-stone-900 font-bold">Sign In</a>
                @endauth
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 text-stone-600 hover:text-stone-900 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                </button>
            </div>
        </header>

        <!-- Mobile Drawer -->
        <div x-show="mobileMenuOpen" x-cloak class="md:hidden bg-white border-b border-stone-200 p-4 space-y-1 text-sm font-medium">
            <a href="{{ route('seller.orders.ready_made') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('seller.orders.ready_made*') ? 'bg-stone-900 text-white font-bold' : 'text-stone-700 hover:bg-stone-100' }}">📦 Ready-Made Sales</a>
            <a href="{{ route('seller.orders.index') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('seller.orders.index') ? 'bg-stone-900 text-white font-bold' : 'text-stone-700 hover:bg-stone-100' }}">🏗️ Custom Orders & Quotes</a>
            <a href="{{ route('seller.products.index') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('seller.products.*') ? 'bg-stone-900 text-white font-bold' : 'text-stone-700 hover:bg-stone-100' }}">🪑 Product Catalog</a>
            <a href="{{ route('seller.products.create') }}" class="block px-3 py-2 rounded-lg text-stone-700 hover:bg-stone-100">➕ Add New Product</a>
            <a href="{{ route('seller.categories.index') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('seller.categories.*') ? 'bg-stone-900 text-white font-bold' : 'text-stone-700 hover:bg-stone-100' }}">🏷️ Categories & Subs</a>
            <a href="{{ route('seller.brands.index') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('seller.brands.*') ? 'bg-stone-900 text-white font-bold' : 'text-stone-700 hover:bg-stone-100' }}">🏢 Brands & Studios</a>
            <a href="{{ route('seller.villa-designs.index') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('seller.villa-designs.*') ? 'bg-amber-900 text-white font-bold' : 'text-amber-900 bg-amber-50/70 hover:bg-amber-100' }}">🏰 Villa Designs Showcase (تصاميم الفلل)</a>
            <a href="{{ route('seller.villa-designs.create') }}" class="block px-3 py-2 rounded-lg text-amber-900 hover:bg-amber-100">➕ Add Villa Design</a>
            <a href="{{ route('login') }}" class="block px-3 py-2 text-xs text-amber-900 font-bold hover:bg-amber-50 rounded-lg">Demo Role Switcher</a>
        </div>

        <!-- Main Workspace (Responsive sm, md, lg) -->
        <main class="flex-1 flex flex-col overflow-y-auto">
            <!-- Flash Notification -->
            @if(session('success'))
                <div class="m-4 md:m-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2">
                        <span class="font-bold">✓</span>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('info'))
                <div class="m-4 md:m-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-medium flex items-center gap-2 shadow-sm">
                    <span>ℹ</span>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            <div class="p-4 sm:p-6 md:p-8 lg:p-10 space-y-8 flex-1">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
