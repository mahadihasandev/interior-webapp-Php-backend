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
    <div class="min-h-full flex flex-col lg:flex-row">
        <!-- 1. DESKTOP PERMANENT SIDEBAR (Large screens lg / xl: >= 1024px) -->
        <aside class="hidden lg:flex flex-col w-72 bg-white border-r border-stone-200 shrink-0 min-h-screen sticky top-0 h-screen">
            <div class="h-20 flex items-center px-6 border-b border-stone-200 justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/25 flex items-center justify-center text-amber-800 font-serif font-bold text-lg shadow-2xs">
                        L
                    </div>
                    <div>
                        <span class="font-serif font-bold text-stone-900 tracking-tight text-base block">L’Atelier Studio</span>
                        <span class="text-[10px] text-amber-800 uppercase tracking-widest font-bold">Seller & CAD Portal</span>
                    </div>
                </div>
            </div>

            <!-- Desktop Navigation Links -->
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

                <a href="{{ route('seller.custom_products.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('seller.custom_products.*') ? 'bg-amber-950 text-amber-100 shadow-xs font-semibold' : 'text-amber-950 bg-amber-50/80 hover:bg-amber-100/90 font-medium' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('seller.custom_products.*') ? 'text-amber-400' : 'text-amber-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <span class="font-bold">Custom Architectural</span>
                    <span class="ml-auto text-[9px] font-bold px-1.5 py-0.5 rounded bg-amber-200 text-amber-950 uppercase tracking-wider">Serials</span>
                </a>

                <a href="{{ route('seller.products.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('seller.products.create') ? 'bg-stone-900 text-white shadow-xs font-semibold' : 'text-stone-700 hover:text-stone-900 hover:bg-stone-100' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('seller.products.create') ? 'text-white' : 'text-stone-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Add New Product</span>
                </a>

                <a href="{{ route('seller.products.create_custom') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('seller.products.create_custom') ? 'bg-amber-900 text-amber-50 shadow-xs font-semibold' : 'text-amber-900 bg-amber-50/70 hover:bg-amber-100 hover:text-amber-950' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('seller.products.create_custom') ? 'text-amber-200' : 'text-amber-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/></svg>
                    <span class="font-bold">Add Custom Order</span>
                    <span class="ml-auto text-[9px] font-bold px-1.5 py-0.5 rounded bg-amber-200/80 text-amber-950 uppercase tracking-wider">Custom</span>
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
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
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

                <div class="pt-4 border-t border-stone-100">
                    <a href="{{ env('FRONTEND_URL', 'http://localhost:3000') }}" target="_blank" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition-colors">
                        <span>← Customer Web Storefront</span>
                        <span class="text-[10px] text-stone-400 font-mono">3000 ↗</span>
                    </a>
                </div>
            </nav>

            <!-- User profile footer -->
            <div class="p-4 border-t border-stone-200 bg-stone-50/70">
                @auth
                    <div class="flex items-center justify-between">
                        <div class="truncate mr-2">
                            <p class="text-xs font-bold text-stone-900 truncate">{{ auth()->user()->name }}</p>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="inline-flex px-1.5 py-0.5 text-[9px] font-bold rounded bg-amber-100 text-amber-900 border border-amber-200 uppercase tracking-wider">
                                    {{ auth()->user()->role ?? 'Staff' }}
                                </span>
                                @if(auth()->user()->vendor)
                                    <span class="text-[10px] text-stone-500 truncate">
                                        {{ auth()->user()->vendor->name }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('login') }}" title="Switch Demo Role" class="p-1.5 text-stone-400 hover:text-stone-900 rounded-lg hover:bg-stone-200/50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        </a>
                    </div>
                    <div class="mt-2.5 flex items-center justify-between text-[11px] text-stone-500 pt-2 border-t border-stone-200">
                        <a href="{{ route('login') }}" class="hover:text-stone-900 font-medium">Switch Role</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="hover:text-rose-600 font-bold cursor-pointer">Sign Out</button>
                        </form>
                    </div>
                @else
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-stone-900">Demo Guest View</p>
                            <p class="text-[10px] text-stone-500">All vendors preview</p>
                        </div>
                        <a href="{{ route('login') }}" class="px-3 py-1.5 bg-stone-900 text-white rounded-xl text-xs font-bold hover:bg-stone-800 transition-colors">
                            Sign In
                        </a>
                    </div>
                @endauth
            </div>
        </aside>

        <!-- 2. TABLET & MOBILE TOP HEADER BAR (Screens < 1024px: md & sm) -->
        <header class="lg:hidden sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-stone-200 px-4 sm:px-6 py-3 flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-3">
                <button @click="mobileMenuOpen = true" class="p-2 -ml-2 text-stone-700 hover:text-stone-900 hover:bg-stone-100 rounded-xl transition-colors cursor-pointer" aria-label="Open Navigation Menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                </button>
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-amber-500/10 border border-amber-500/25 flex items-center justify-center text-amber-800 font-serif font-bold text-sm shadow-2xs">
                        L
                    </div>
                    <div>
                        <span class="font-serif font-bold text-stone-900 text-sm leading-tight block">L’Atelier Studio</span>
                        <span class="text-[10px] text-stone-500 font-sans">Seller Portal</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ env('FRONTEND_URL', 'http://localhost:3000') }}" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold text-stone-700 hover:text-stone-900 bg-stone-100 hover:bg-stone-200 border border-stone-200 transition-colors">
                    <span>Storefront ↗</span>
                </a>
                @auth
                    <div class="hidden sm:flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-50 border border-amber-200 text-[10px] font-bold text-amber-900">
                        <span>{{ auth()->user()->role ?? 'Staff' }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="p-1.5 text-stone-500 hover:text-rose-600 hover:bg-stone-100 rounded-lg text-xs font-medium cursor-pointer" title="Sign Out">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="px-3 py-1.5 bg-stone-900 text-white rounded-xl text-xs font-bold hover:bg-stone-800 transition-colors">
                        Sign In
                    </a>
                @endauth
            </div>
        </header>

        <!-- 3. MOBILE & TABLET SLIDE-OVER DRAWER (Off-canvas, Smooth Animated) -->
        <!-- Backdrop -->
        <div x-show="mobileMenuOpen" 
             x-cloak 
             @click="mobileMenuOpen = false" 
             class="fixed inset-0 bg-stone-950/60 backdrop-blur-xs z-40 lg:hidden transition-opacity duration-300">
        </div>

        <!-- Slide-over Drawer Panel -->
        <aside x-show="mobileMenuOpen" 
               x-cloak 
               class="fixed inset-y-0 left-0 w-80 max-w-[85vw] bg-white z-50 shadow-2xl flex flex-col justify-between lg:hidden border-r border-stone-200 overflow-y-auto animate-in slide-in-from-left duration-300">
            <div>
                <!-- Drawer Top Header -->
                <div class="h-16 flex items-center justify-between px-5 border-b border-stone-200 bg-stone-50">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-amber-500/10 border border-amber-500/25 flex items-center justify-center text-amber-800 font-serif font-bold text-sm">
                            L
                        </div>
                        <div>
                            <span class="font-serif font-bold text-stone-900 text-sm block">L’Atelier Studio</span>
                            <span class="text-[9px] text-stone-500 font-medium uppercase tracking-wider">Navigation Menu</span>
                        </div>
                    </div>
                    <button @click="mobileMenuOpen = false" class="p-2 text-stone-500 hover:text-stone-900 hover:bg-stone-200/50 rounded-lg cursor-pointer text-lg font-bold">
                        ✕
                    </button>
                </div>

                <!-- Navigation List inside Drawer -->
                <div class="p-4 space-y-1 text-sm font-medium">
                    <div class="text-[10px] font-bold text-stone-400 uppercase tracking-widest px-3 py-1.5">
                        Orders & Fulfillment
                    </div>
                    <a href="{{ route('seller.orders.ready_made') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('seller.orders.ready_made*') ? 'bg-stone-900 text-white font-bold' : 'text-stone-700 hover:bg-stone-100' }}">
                        <span>📦</span>
                        <span>Ready-Made Sales List</span>
                    </a>
                    <a href="{{ route('seller.orders.index') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('seller.orders.index') ? 'bg-stone-900 text-white font-bold' : 'text-stone-700 hover:bg-stone-100' }}">
                        <span>🏗️</span>
                        <span>Custom Orders & Quotes</span>
                    </a>

                    <div class="text-[10px] font-bold text-stone-400 uppercase tracking-widest px-3 pt-4 pb-1.5">
                        Catalog Management
                    </div>
                    <a href="{{ route('seller.products.index') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('seller.products.index') || request()->routeIs('seller.products.edit') ? 'bg-stone-900 text-white font-bold' : 'text-stone-700 hover:bg-stone-100' }}">
                        <span>🪑</span>
                        <span>Product Catalog</span>
                    </a>
                    <a href="{{ route('seller.products.create') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('seller.products.create') ? 'bg-stone-900 text-white font-bold' : 'text-stone-700 hover:bg-stone-100' }}">
                        <span>➕</span>
                        <span>Add New Product</span>
                    </a>
                    <a href="{{ route('seller.products.create_custom') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('seller.products.create_custom') ? 'bg-amber-900 text-white font-bold' : 'text-amber-900 bg-amber-50/70 hover:bg-amber-100' }}">
                        <span>🪟</span>
                        <span class="font-bold">Add Custom Order</span>
                    </a>
                    <a href="{{ route('seller.categories.index') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('seller.categories.*') ? 'bg-stone-900 text-white font-bold' : 'text-stone-700 hover:bg-stone-100' }}">
                        <span>🏷️</span>
                        <span>Categories & Subs</span>
                    </a>
                    <a href="{{ route('seller.brands.index') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('seller.brands.*') ? 'bg-stone-900 text-white font-bold' : 'text-stone-700 hover:bg-stone-100' }}">
                        <span>🏢</span>
                        <span>Brands & Studios</span>
                    </a>

                    <div class="text-[10px] font-bold text-amber-800 uppercase tracking-widest px-3 pt-4 pb-1.5 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        <span>Villa Showcase · تصاميم الفلل</span>
                    </div>
                    <a href="{{ route('seller.villa-designs.index') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('seller.villa-designs.index') || request()->routeIs('seller.villa-designs.edit') ? 'bg-amber-950 text-amber-100 font-bold' : 'text-amber-950 bg-amber-50/70 hover:bg-amber-100' }}">
                        <span>🏰</span>
                        <span>Villa Designs Showcase</span>
                    </a>
                    <a href="{{ route('seller.villa-designs.create') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('seller.villa-designs.create') ? 'bg-amber-950 text-amber-100 font-bold' : 'text-stone-700 hover:bg-stone-100' }}">
                        <span>➕</span>
                        <span>Add Villa Design</span>
                    </a>

                    <div class="pt-3 border-t border-stone-100 space-y-1">
                        <a href="{{ env('FRONTEND_URL', 'http://localhost:3000') }}" target="_blank" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold text-stone-800 bg-stone-100 hover:bg-stone-200">
                            <span>← Visit Web Storefront</span>
                            <span class="font-mono text-[10px] text-stone-500">3000 ↗</span>
                        </a>
                        <a href="{{ route('login') }}" class="block px-3 py-2 text-xs font-bold text-amber-900 hover:bg-amber-50 rounded-xl">
                            Demo Role Switcher
                        </a>
                    </div>
                </div>
            </div>

            <!-- Drawer Bottom User Profile & Sign Out -->
            <div class="p-4 border-t border-stone-200 bg-stone-50">
                @auth
                    <div class="flex items-center justify-between mb-3">
                        <div class="truncate mr-2">
                            <p class="text-xs font-bold text-stone-900 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] text-stone-500 truncate">{{ auth()->user()->email }}</p>
                            <span class="inline-block mt-1 px-1.5 py-0.5 text-[9px] font-bold rounded bg-amber-100 text-amber-900 border border-amber-200 uppercase">
                                {{ auth()->user()->role ?? 'Staff' }}
                            </span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="block">
                        @csrf
                        <button type="submit" class="w-full py-2.5 px-3 bg-stone-900 hover:bg-stone-800 text-white rounded-xl text-xs font-bold transition-colors cursor-pointer text-center">
                            Sign Out of Studio
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="w-full block py-2.5 px-3 bg-stone-900 hover:bg-stone-800 text-white rounded-xl text-xs font-bold transition-colors text-center">
                        Sign In
                    </a>
                @endauth
            </div>
        </aside>

        <!-- 4. MAIN WORKSPACE (Fully Responsive across Small, Medium, Large) -->
        <main class="flex-1 flex flex-col overflow-y-auto min-w-0">
            <!-- Flash Notification -->
            @if(session('success'))
                <div class="m-3 sm:m-4 md:m-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-medium flex items-center justify-between shadow-2xs">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-emerald-700">✓</span>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('info'))
                <div class="m-3 sm:m-4 md:m-6 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs font-medium flex items-center gap-2 shadow-2xs">
                    <span>ℹ</span>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            <div class="p-3 sm:p-5 md:p-6 lg:p-8 xl:p-10 space-y-6 sm:space-y-8 flex-1 min-w-0">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
