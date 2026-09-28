<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class InteriorShopSeeder extends Seeder
{
    public function run(): void
    {
        $livingRoom = Category::updateOrCreate(
            ['slug' => 'living-room'],
            [
                'name' => 'Living Room',
                'description' => 'Contemporary sofas, lounge seating, coffee tables, and bespoke media consoles.',
                'image_url' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&w=1000&q=80',
                'icon' => 'Armchair',
            ]
        );

        $bedroom = Category::updateOrCreate(
            ['slug' => 'bedroom'],
            [
                'name' => 'Bedroom & Suites',
                'description' => 'Artisan bed frames, minimalist nightstands, and tranquil sanctuaries.',
                'image_url' => 'https://images.unsplash.com/photo-1616594039964-ae9021a400a0?auto=format&fit=crop&w=1000&q=80',
                'icon' => 'Bed',
            ]
        );

        $lighting = Category::updateOrCreate(
            ['slug' => 'lighting'],
            [
                'name' => 'Lighting & Fixtures',
                'description' => 'Sculptural pendant lights, architectural floor lamps, and ambient glow.',
                'image_url' => 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?auto=format&fit=crop&w=1000&q=80',
                'icon' => 'Lamp',
            ]
        );

        $dining = Category::updateOrCreate(
            ['slug' => 'dining'],
            [
                'name' => 'Dining & Kitchen',
                'description' => 'Solid oak dining tables, ergonomic dining chairs, and curated barstools.',
                'image_url' => 'https://images.unsplash.com/photo-1617806118233-18e1de247200?auto=format&fit=crop&w=1000&q=80',
                'icon' => 'Utensils',
            ]
        );

        $decor = Category::updateOrCreate(
            ['slug' => 'decor'],
            [
                'name' => 'Decor & Objects',
                'description' => 'Handcrafted ceramic vessels, artisanal mirrors, and natural fiber textiles.',
                'image_url' => 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?auto=format&fit=crop&w=1000&q=80',
                'icon' => 'Sparkles',
            ]
        );

        // Products
        $products = [
            [
                'category_id' => $livingRoom->id,
                'name' => 'Aura Bouclé Curved Lounge Chair',
                'slug' => 'aura-boucle-curved-lounge-chair',
                'tagline' => 'Organic contours wrapped in textured warm cream bouclé fabric',
                'description' => 'The Aura Lounge Chair pairs architectural flow with plush, cloud-like comfort. Sculpted with a kiln-dried hardwood frame and upholstered in premium textured bouclé, it serves as a commanding centerpiece in any modern living sanctuary.',
                'price' => 780.00,
                'compare_at_price' => 950.00,
                'dimensions' => '36"W x 34"D x 30"H',
                'materials' => 'Textured Bouclé, High-Resilience Foam, FSC Hardwood',
                'color' => 'Warm Cream / Alabaster',
                'stock' => 14,
                'image_url' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?auto=format&fit=crop&w=1000&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?auto=format&fit=crop&w=1000&q=80',
                    'https://images.unsplash.com/photo-1567538096630-e0c55bd6374c?auto=format&fit=crop&w=1000&q=80',
                ],
                'is_featured' => true,
                'rating' => 4.95,
                'reviews_count' => 38,
            ],
            [
                'category_id' => $dining->id,
                'name' => 'Nordic Minimalist Oak Dining Table',
                'slug' => 'nordic-minimalist-oak-dining-table',
                'tagline' => 'Solid European white oak with soft chamfered edge profiling',
                'description' => 'Designed with timeless Scandinavian restraint, this solid European oak dining table comfortably seats 8. Finished with a subtle matte polyurethane seal that preserves the raw tactile grain while resisting daily spills and heat.',
                'price' => 1250.00,
                'compare_at_price' => 1490.00,
                'dimensions' => '84"L x 38"W x 30"H',
                'materials' => 'Solid White European Oak, Steel Reinforcements',
                'color' => 'Natural Pale Oak',
                'stock' => 8,
                'image_url' => 'https://images.unsplash.com/photo-1617806118233-18e1de247200?auto=format&fit=crop&w=1000&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1617806118233-18e1de247200?auto=format&fit=crop&w=1000&q=80',
                ],
                'is_featured' => true,
                'rating' => 4.90,
                'reviews_count' => 24,
            ],
            [
                'category_id' => $lighting->id,
                'name' => 'Solstice Spun Brass Chandelier',
                'slug' => 'solstice-spun-brass-chandelier',
                'tagline' => 'Hand-spun brushed brass with frosted opal glass globes',
                'description' => 'Suspended like a celestial harmony, the Solstice fixture casts warm, diffused illumination across open living or dining volumes. Compatible with dimmable Warm-Dim LED modules for intimate evening ambience.',
                'price' => 460.00,
                'compare_at_price' => 580.00,
                'dimensions' => '42"Dia x 24"H (Adjustable 48" drop stem)',
                'materials' => 'Solid Spun Brass, Handblown Opal Glass',
                'color' => 'Brushed Satin Brass',
                'stock' => 20,
                'image_url' => 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?auto=format&fit=crop&w=1000&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?auto=format&fit=crop&w=1000&q=80',
                ],
                'is_featured' => true,
                'rating' => 4.88,
                'reviews_count' => 19,
            ],
            [
                'category_id' => $bedroom->id,
                'name' => 'Kyoto Low Platform King Bed',
                'slug' => 'kyoto-low-platform-king-bed',
                'tagline' => 'Japandi aesthetic with floating bedside cantilever ledges',
                'description' => 'Inspired by traditional Japanese ryokan architecture, the Kyoto King Bed features integrated floating side nightstands, recessed slat support for optimal ventilation, and solid walnut joinery built to endure generations.',
                'price' => 1890.00,
                'compare_at_price' => 2200.00,
                'dimensions' => '92"W x 88"L x 28"H',
                'materials' => 'Solid American Walnut, Engineered Slat Base',
                'color' => 'Deep Warm Walnut',
                'stock' => 5,
                'image_url' => 'https://images.unsplash.com/photo-1616594039964-ae9021a400a0?auto=format&fit=crop&w=1000&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1616594039964-ae9021a400a0?auto=format&fit=crop&w=1000&q=80',
                ],
                'is_featured' => true,
                'rating' => 4.97,
                'reviews_count' => 42,
            ],
            [
                'category_id' => $decor->id,
                'name' => 'Kanso Matte Ceramic Sculptural Vase',
                'slug' => 'kanso-matte-ceramic-sculptural-vase',
                'tagline' => 'Earthen terracotta clay with matte chalk texture',
                'description' => 'Individually wheel-thrown and wood-fired by master ceramicists, the Kanso vase exhibits organic micro-imperfections that honor the Japanese philosophy of wabi-sabi. Beautiful standing solo or styled with dried botanicals.',
                'price' => 140.00,
                'compare_at_price' => 175.00,
                'dimensions' => '8.5"Dia x 14"H',
                'materials' => 'Handmade Terracotta, Mineral Matte Glaze',
                'color' => 'Sand Beige',
                'stock' => 30,
                'image_url' => 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?auto=format&fit=crop&w=1000&q=80',
                'gallery' => [],
                'is_featured' => false,
                'rating' => 4.75,
                'reviews_count' => 15,
            ],
            [
                'category_id' => $livingRoom->id,
                'name' => 'Sora Travertine & Walnut Coffee Table',
                'slug' => 'sora-travertine-walnut-coffee-table',
                'tagline' => 'Honed Italian Roman travertine slab on fluted walnut pedestals',
                'description' => 'A sculptural dialogue between warm textured wood and cool natural stone. Each travertine top displays unique natural veining and porous cavities filled with transparent resin for effortless maintenance.',
                'price' => 920.00,
                'compare_at_price' => 1100.00,
                'dimensions' => '48"L x 28"W x 15"H',
                'materials' => 'Honed Italian Travertine, Fluted American Walnut',
                'color' => 'Ivory Stone / Walnut',
                'stock' => 9,
                'image_url' => 'https://images.unsplash.com/photo-1533090161767-e6ffed986c88?auto=format&fit=crop&w=1000&q=80',
                'gallery' => [],
                'is_featured' => true,
                'rating' => 4.92,
                'reviews_count' => 28,
            ],
        ];

        foreach ($products as $productData) {
            Product::updateOrCreate(
                ['slug' => $productData['slug']],
                $productData
            );
        }
    }
}
