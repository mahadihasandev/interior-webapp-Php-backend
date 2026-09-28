<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — L’Atelier Studio & Vendor Portal</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        stone: {
                            50: '#fafaf9',
                            100: '#f5f5f4',
                            200: '#e7e5e4',
                            300: '#d6d3d1',
                            400: '#a8a29e',
                            500: '#78716c',
                            600: '#57534e',
                            700: '#44403c',
                            800: '#292524',
                            900: '#1c1917',
                            950: '#0c0a09',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full bg-stone-50 text-stone-900 antialiased font-sans flex flex-col justify-center py-12 sm:px-6 lg:px-8">

    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <!-- Logo & Brand Header -->
        <div class="text-center">
            <div class="inline-flex w-12 h-12 rounded-2xl bg-amber-500/10 border border-amber-500/30 items-center justify-center text-amber-800 font-serif font-bold text-xl mb-3 shadow-xs">
                L
            </div>
            <h2 class="text-2xl font-serif font-bold tracking-tight text-stone-900">
                L’Atelier Architectural Studio
            </h2>
            <p class="mt-1 text-xs text-stone-500 uppercase tracking-widest font-medium">
                Seller, Workshop & Admin Portal
            </p>
        </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4 sm:px-0">
        <div class="bg-white py-8 px-6 sm:px-10 shadow-sm border border-stone-200 rounded-3xl space-y-6">

            <!-- Flash & Error Messages -->
            @if(session('info'))
                <div class="p-3 bg-stone-100 border border-stone-200 rounded-xl text-xs text-stone-700 font-medium">
                    {{ session('info') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-700 space-y-1">
                    @foreach($errors->all() as $error)
                        <p>• {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <!-- Standard Credentials Login Form -->
            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-1">
                        Email Address
                    </label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        required
                        value="{{ old('email', 'admin@apexglass.com') }}"
                        class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-xs text-stone-900 focus:outline-none focus:border-stone-900 focus:bg-white"
                        placeholder="you@studio.com"
                    >
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-stone-700">
                            Password
                        </label>
                        <span class="text-[10px] text-stone-400 font-mono">Demo: password123</span>
                    </div>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        value="password123"
                        class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-xs text-stone-900 focus:outline-none focus:border-stone-900 focus:bg-white"
                    >
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer text-stone-600">
                        <input type="checkbox" name="remember" checked class="rounded border-stone-300 text-stone-900 focus:ring-stone-900">
                        <span>Remember session</span>
                    </label>
                </div>

                <button
                    type="submit"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-xs font-bold uppercase tracking-widest text-white bg-stone-900 hover:bg-stone-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-stone-900 transition-colors"
                >
                    Sign In to Dashboard
                </button>
            </form>

            <!-- 1-Click Fast Role Switcher for Client Demo -->
            <div class="pt-4 border-t border-stone-200 space-y-3">
                <div class="text-center">
                    <span class="text-[10px] uppercase tracking-widest text-stone-500 font-bold bg-white px-2">
                        Client Demonstration 1-Click Fast Login
                    </span>
                </div>

                <div class="grid grid-cols-1 gap-2 text-xs">
                    <!-- Super Admin -->
                    <a
                        href="{{ route('quick.login', 'admin@interior.com') }}"
                        class="flex items-center justify-between p-2.5 rounded-xl border border-stone-200 hover:border-stone-900 hover:bg-stone-50 transition-colors"
                    >
                        <div class="flex items-center gap-2">
                            <span class="text-sm">👑</span>
                            <div>
                                <p class="font-bold text-stone-900">Super Administrator</p>
                                <p class="text-[10px] text-stone-500">All vendors & platform control</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-mono text-stone-400">admin@interior.com →</span>
                    </a>

                    <!-- Vendor Admin Apex Glass -->
                    <a
                        href="{{ route('quick.login', 'admin@apexglass.com') }}"
                        class="flex items-center justify-between p-2.5 rounded-xl border border-amber-200 bg-amber-50/40 hover:bg-amber-50 hover:border-amber-300 transition-colors"
                    >
                        <div class="flex items-center gap-2">
                            <span class="text-sm">🏢</span>
                            <div>
                                <p class="font-bold text-stone-900">Vendor Admin: Apex Glass</p>
                                <p class="text-[10px] text-stone-500">Quoting & custom fitting orders</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-mono text-amber-800">admin@apexglass.com →</span>
                    </a>

                    <!-- Production Manager Apex Glass -->
                    <a
                        href="{{ route('quick.login', 'workshop@apexglass.com') }}"
                        class="flex items-center justify-between p-2.5 rounded-xl border border-stone-200 hover:border-stone-900 hover:bg-stone-50 transition-colors"
                    >
                        <div class="flex items-center gap-2">
                            <span class="text-sm">🔨</span>
                            <div>
                                <p class="font-bold text-stone-900">Workshop Production Manager</p>
                                <p class="text-[10px] text-stone-500">7-stage manufacturing timeline</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-mono text-stone-400">workshop@apexglass.com →</span>
                    </a>

                    <!-- Vendor Admin IronCraft -->
                    <a
                        href="{{ route('quick.login', 'admin@ironcraft.com') }}"
                        class="flex items-center justify-between p-2.5 rounded-xl border border-stone-200 hover:border-stone-900 hover:bg-stone-50 transition-colors"
                    >
                        <div class="flex items-center gap-2">
                            <span class="text-sm">🏗️</span>
                            <div>
                                <p class="font-bold text-stone-900">Vendor Admin: IronCraft Gate & Canopy</p>
                                <p class="text-[10px] text-stone-500">Heavy steel fittings vendor</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-mono text-stone-400">admin@ironcraft.com →</span>
                    </a>
                </div>
            </div>

            <!-- Return to Customer Storefront Link -->
            <div class="text-center pt-2">
                <a href="{{ env('FRONTEND_URL', 'http://localhost:3000') }}" class="text-xs text-stone-500 hover:text-stone-900 font-medium">
                    ← Return to Customer Web Storefront
                </a>
            </div>
        </div>
    </div>

</body>
</html>
