<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ReadyMadeOrderAndCatalogSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Brands
        $brands = [
            [
                'name' => 'L’Atelier Nordique',
                'slug' => 'latelier-nordique',
                'origin_country' => 'Denmark',
                'logo_url' => 'https://images.unsplash.com/photo-1541123437800-1bb1317badc2?auto=format&fit=crop&w=200&q=80',
                'description' => 'Copenhagen studio crafting minimalist Scandinavian oak and ash joinery.',
                'is_featured' => true,
            ],
            [
                'name' => 'Kyoto Artisan Woodcraft',
                'slug' => 'kyoto-artisan-woodcraft',
                'origin_country' => 'Japan',
                'logo_url' => 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?auto=format&fit=crop&w=200&q=80',
                'description' => 'Master Japanese joinery honoring traditional sashimono techniques.',
                'is_featured' => true,
            ],
            [
                'name' => 'Atelier Luce Milano',
                'slug' => 'atelier-luce-milano',
                'origin_country' => 'Italy',
                'logo_url' => 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?auto=format&fit=crop&w=200&q=80',
                'description' => 'Architectural lighting fixtures in spun brass, handblown Murano glass, and alabaster.',
                'is_featured' => true,
            ],
            [
                'name' => 'Apex Architectural Glass & Aluminum',
                'slug' => 'apex-architectural-glass',
                'origin_country' => 'United States',
                'logo_url' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=200&q=80',
                'description' => 'Engineered structural fluted glass partitions and thermal break casements.',
                'is_featured' => true,
            ],
            [
                'name' => 'IronCraft Forge Studio',
                'slug' => 'ironcraft-forge-studio',
                'origin_country' => 'United States',
                'logo_url' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&w=200&q=80',
                'description' => 'Industrial forged gates, steel pergolas, and laser-cut architectural screens.',
                'is_featured' => false,
            ],
        ];

        $brandModels = [];
        foreach ($brands as $b) {
            $brandModels[$b['slug']] = Brand::updateOrCreate(['slug' => $b['slug']], $b);
        }

        // 2. Seed Subcategories for Each Category
        $livingRoom = Category::where('slug', 'living-room')->first();
        $bedroom = Category::where('slug', 'bedroom')->first();
        $lighting = Category::where('slug', 'lighting')->first();
        $dining = Category::where('slug', 'dining')->first();
        $decor = Category::where('slug', 'decor')->first();

        $subcategoriesData = [];

        if ($livingRoom) {
            $subcategoriesData[] = ['category_id' => $livingRoom->id, 'name' => 'Lounge & Accent Chairs', 'slug' => 'lounge-accent-chairs', 'description' => 'Curved bouclé, leather reading chairs, and armchairs'];
            $subcategoriesData[] = ['category_id' => $livingRoom->id, 'name' => 'Modular Sofas & Couches', 'slug' => 'modular-sofas-couches', 'description' => 'Bespoke sectionals, chaise lounges, and linear seating'];
            $subcategoriesData[] = ['category_id' => $livingRoom->id, 'name' => 'Coffee & Side Tables', 'slug' => 'coffee-side-tables', 'description' => 'Travertine slabs, fluted walnut, and nesting tables'];
        }

        if ($bedroom) {
            $subcategoriesData[] = ['category_id' => $bedroom->id, 'name' => 'Platform Beds', 'slug' => 'platform-beds', 'description' => 'Low-profile Japandi and floating platform bed frames'];
            $subcategoriesData[] = ['category_id' => $bedroom->id, 'name' => 'Nightstands & Floating Shelves', 'slug' => 'nightstands-floating-shelves', 'description' => 'Solid walnut bedside consoles with soft-close drawers'];
        }

        if ($lighting) {
            $subcategoriesData[] = ['category_id' => $lighting->id, 'name' => 'Chandeliers & Pendants', 'slug' => 'chandeliers-pendants', 'description' => 'Spun brass and handblown frosted glass statement fixtures'];
            $subcategoriesData[] = ['category_id' => $lighting->id, 'name' => 'Floor & Table Lamps', 'slug' => 'floor-table-lamps', 'description' => 'Sculptural ambient task lights and marble base lanterns'];
        }

        if ($dining) {
            $subcategoriesData[] = ['category_id' => $dining->id, 'name' => 'Solid Oak Dining Tables', 'slug' => 'solid-oak-dining-tables', 'description' => 'Heirloom European oak dining surfaces with chamfered edges'];
            $subcategoriesData[] = ['category_id' => $dining->id, 'name' => 'Dining Chairs & Stools', 'slug' => 'dining-chairs-stools', 'description' => 'Ergonomic upholstered dining seats and counter stools'];
        }

        if ($decor) {
            $subcategoriesData[] = ['category_id' => $decor->id, 'name' => 'Ceramic & Stoneware Vessels', 'slug' => 'ceramic-stoneware-vessels', 'description' => 'Handmade terracotta and mineral-glazed sculptural vases'];
            $subcategoriesData[] = ['category_id' => $decor->id, 'name' => 'Architectural Mirrors', 'slug' => 'architectural-mirrors', 'description' => 'Minimal brass frame vanity and floor-length mirrors'];
        }

        $subModels = [];
        foreach ($subcategoriesData as $sub) {
            $subModels[$sub['slug']] = Subcategory::updateOrCreate(
                ['category_id' => $sub['category_id'], 'slug' => $sub['slug']],
                $sub
            );
        }

        // 3. Link Existing Products to Brands & Subcategories
        $chair = Product::where('slug', 'aura-boucle-curved-lounge-chair')->first();
        if ($chair && isset($brandModels['latelier-nordique'], $subModels['lounge-accent-chairs'])) {
            $chair->update([
                'brand_id' => $brandModels['latelier-nordique']->id,
                'subcategory_id' => $subModels['lounge-accent-chairs']->id,
            ]);
        }

        $diningTable = Product::where('slug', 'nordic-minimalist-oak-dining-table')->first();
        if ($diningTable && isset($brandModels['latelier-nordique'], $subModels['solid-oak-dining-tables'])) {
            $diningTable->update([
                'brand_id' => $brandModels['latelier-nordique']->id,
                'subcategory_id' => $subModels['solid-oak-dining-tables']->id,
            ]);
        }

        $chandelier = Product::where('slug', 'solstice-spun-brass-chandelier')->first();
        if ($chandelier && isset($brandModels['atelier-luce-milano'], $subModels['chandeliers-pendants'])) {
            $chandelier->update([
                'brand_id' => $brandModels['atelier-luce-milano']->id,
                'subcategory_id' => $subModels['chandeliers-pendants']->id,
            ]);
        }

        $bed = Product::where('slug', 'kyoto-low-platform-king-bed')->first();
        if ($bed && isset($brandModels['kyoto-artisan-woodcraft'], $subModels['platform-beds'])) {
            $bed->update([
                'brand_id' => $brandModels['kyoto-artisan-woodcraft']->id,
                'subcategory_id' => $subModels['platform-beds']->id,
            ]);
        }

        $vase = Product::where('slug', 'kanso-matte-ceramic-sculptural-vase')->first();
        if ($vase && isset($brandModels['kyoto-artisan-woodcraft'], $subModels['ceramic-stoneware-vessels'])) {
            $vase->update([
                'brand_id' => $brandModels['kyoto-artisan-woodcraft']->id,
                'subcategory_id' => $subModels['ceramic-stoneware-vessels']->id,
            ]);
        }

        $coffeeTable = Product::where('slug', 'sora-travertine-walnut-coffee-table')->first();
        if ($coffeeTable && isset($brandModels['latelier-nordique'], $subModels['coffee-side-tables'])) {
            $coffeeTable->update([
                'brand_id' => $brandModels['latelier-nordique']->id,
                'subcategory_id' => $subModels['coffee-side-tables']->id,
            ]);
        }

        // 4. Seed Ready-Made Orders & Sales
        $sampleOrders = [
            [
                'order_number' => 'INT-91K7A4',
                'customer_name' => 'Sophia Vance',
                'customer_email' => 'sophia.vance@studio-artisan.com',
                'customer_phone' => '+1 (555) 349-1829',
                'shipping_address' => '420 West Broadway, Penthouse 8B',
                'city' => 'New York, NY',
                'postal_code' => '10012',
                'status' => 'processing',
                'payment_method' => 'card',
                'payment_status' => 'paid',
                'notes' => 'White glove freight delivery requested. Call 2 hours prior to arrival.',
                'created_at' => now()->subHours(8),
                'items' => [
                    [
                        'product_slug' => 'aura-boucle-curved-lounge-chair',
                        'quantity' => 2,
                    ],
                    [
                        'product_slug' => 'sora-travertine-walnut-coffee-table',
                        'quantity' => 1,
                    ],
                ],
            ],
            [
                'order_number' => 'INT-82B3X1',
                'customer_name' => 'Marcus Holloway',
                'customer_email' => 'marcus.holloway@techvault.io',
                'customer_phone' => '+1 (555) 782-9012',
                'shipping_address' => '88 King Street, Suite 1400',
                'city' => 'San Francisco, CA',
                'postal_code' => '94107',
                'status' => 'shipped',
                'payment_method' => 'apple_pay',
                'payment_status' => 'paid',
                'notes' => 'Commercial high-rise lobby reception delivery.',
                'created_at' => now()->subDays(2),
                'items' => [
                    [
                        'product_slug' => 'kyoto-low-platform-king-bed',
                        'quantity' => 1,
                    ],
                    [
                        'product_slug' => 'kanso-matte-ceramic-sculptural-vase',
                        'quantity' => 2,
                    ],
                ],
            ],
            [
                'order_number' => 'INT-44M9P2',
                'customer_name' => 'Elena Rostova',
                'customer_email' => 'elena.rostova@designhaus.de',
                'customer_phone' => '+1 (555) 914-5521',
                'shipping_address' => '150 Lincoln Road, Unit 5A',
                'city' => 'Miami Beach, FL',
                'postal_code' => '33139',
                'status' => 'delivered',
                'payment_method' => 'card',
                'payment_status' => 'paid',
                'notes' => 'Delivered to concierge with proof of signature.',
                'created_at' => now()->subDays(6),
                'items' => [
                    [
                        'product_slug' => 'solstice-spun-brass-chandelier',
                        'quantity' => 2,
                    ],
                ],
            ],
            [
                'order_number' => 'INT-67R5Q8',
                'customer_name' => 'Julian Thorne',
                'customer_email' => 'julian.thorne@atelier.design',
                'customer_phone' => '+1 (555) 321-7890',
                'shipping_address' => '72 Park Avenue, Apt 11',
                'city' => 'New York, NY',
                'postal_code' => '10016',
                'status' => 'pending',
                'payment_method' => 'bank_transfer',
                'payment_status' => 'pending',
                'notes' => 'Awaiting corporate wire transfer confirmation.',
                'created_at' => now()->subMinutes(45),
                'items' => [
                    [
                        'product_slug' => 'nordic-minimalist-oak-dining-table',
                        'quantity' => 1,
                    ],
                    [
                        'product_slug' => 'solstice-spun-brass-chandelier',
                        'quantity' => 1,
                    ],
                ],
            ],
            [
                'order_number' => 'INT-12D8K3',
                'customer_name' => 'Claire Dupont',
                'customer_email' => 'claire.dupont@lumierestudio.fr',
                'customer_phone' => '+1 (555) 441-2098',
                'shipping_address' => '310 Michigan Ave, Loft 9',
                'city' => 'Chicago, IL',
                'postal_code' => '60604',
                'status' => 'shipped',
                'payment_method' => 'card',
                'payment_status' => 'paid',
                'notes' => 'Tracking Carrier: White Glove Express #WGE-889104.',
                'created_at' => now()->subDays(3),
                'items' => [
                    [
                        'product_slug' => 'aura-boucle-curved-lounge-chair',
                        'quantity' => 1,
                    ],
                    [
                        'product_slug' => 'kanso-matte-ceramic-sculptural-vase',
                        'quantity' => 3,
                    ],
                ],
            ],
        ];

        foreach ($sampleOrders as $ordData) {
            $itemsData = $ordData['items'];
            unset($ordData['items']);

            $subtotal = 0;
            $itemsToCreate = [];

            foreach ($itemsData as $itemInfo) {
                $p = Product::where('slug', $itemInfo['product_slug'])->first();
                if ($p) {
                    $itemPrice = $p->price;
                    $itemSubtotal = $itemPrice * $itemInfo['quantity'];
                    $subtotal += $itemSubtotal;
                    $itemsToCreate[] = [
                        'product_id' => $p->id,
                        'product_name' => $p->name,
                        'price' => $itemPrice,
                        'quantity' => $itemInfo['quantity'],
                        'subtotal' => $itemSubtotal,
                    ];
                }
            }

            $shippingFee = $subtotal > 2000 ? 0.00 : 150.00;
            $ordData['subtotal'] = $subtotal;
            $ordData['shipping_fee'] = $shippingFee;
            $ordData['total_amount'] = $subtotal + $shippingFee;

            $order = Order::updateOrCreate(
                ['order_number' => $ordData['order_number']],
                $ordData
            );

            // Re-populate order items
            $order->items()->delete();
            foreach ($itemsToCreate as $it) {
                $it['order_id'] = $order->id;
                OrderItem::create($it);
            }
        }
    }
}
