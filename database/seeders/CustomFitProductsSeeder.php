<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CustomFitProductsSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::firstOrCreate(
            ['slug' => 'architectural-windows'],
            [
                'name' => 'Architectural Windows & Fittings',
                'description' => 'Bespoke thermal-break windows, acoustic glazing, and sliding shutter systems for Saudi villas.',
                'image_url' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80',
            ]
        );

        $brand = Brand::first();
        $brandId = $brand?->id;

        $customProducts = [
            [
                'name' => 'Thermal Break 50°C Double-Glazed Window',
                'slug' => 'thermal-break-50c-double-glazed-window',
                'tagline' => 'SASO certified thermal break · 38dB acoustic barrier · Double sliding shutters',
                'description' => 'Precision-engineered for Saudi Arabian climates. Features dual polyamide thermal isolators resisting external wall temperatures up to 50°C while maintaining cool interior comfort. Multi-chamber extruded aluminum profile with argon-filled acoustic glazing, dust-proof hermetic perimeter gaskets, and German heavy-duty roller bearings.',
                'product_type' => 'custom_fit',
                'price' => 800.00,
                'compare_at_price' => 1000.00,
                'dimensions' => '180cm H × 140cm W (Fully customizable to any masonry opening)',
                'materials' => 'Alupco Architectural Aluminum 2.0mm, Low-E Double Glazing (6mm+12A+6mm)',
                'color' => 'Matte Architectural Black / Champagne Bronze / Sand White',
                'stock' => 100,
                'is_featured' => true,
                'rating' => 4.95,
                'reviews_count' => 38,
                'image_url' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=85',
                'gallery' => [
                    'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=85',
                    'https://images.unsplash.com/photo-1600566753376-12c8ab7fb75b?auto=format&fit=crop&w=1200&q=85',
                    'https://images.unsplash.com/photo-1600585154526-990dced4db0d?auto=format&fit=crop&w=1200&q=85',
                    'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1200&q=85',
                    'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1200&q=85',
                ],
                'customization_options' => [
                    'min_price' => 800.00,
                    'max_price' => 1000.00,
                    'default_height' => 180,
                    'default_width' => 140,
                    'min_height' => 60,
                    'max_height' => 320,
                    'min_width' => 60,
                    'max_width' => 450,
                    'measurement_unit' => 'cm',
                    'shutters_options' => [
                        ['id' => '1_fixed', 'name' => '1 Fixed Pane', 'description' => 'Seamless architectural picture view', 'price_delta' => 0],
                        ['id' => '2_sliding', 'name' => '2 Shutters (Double Sliding / Casement)', 'description' => 'Dual glide track with smooth rollers', 'price_delta' => 60],
                        ['id' => '3_sliding', 'name' => '3 Shutters (Tri-Rail Panoramic)', 'description' => 'Wide 66% clear opening space', 'price_delta' => 120],
                        ['id' => '4_bifold', 'name' => '4 Shutters (Quad Multi-Slide / Bi-Fold)', 'description' => 'Full terrace & garden opening', 'price_delta' => 190],
                    ],
                    'aluminum_options' => [
                        ['id' => 'alupco_2_0', 'name' => 'Alupco Thermal Break 2.0mm', 'badge' => 'SASO 50°C Certified', 'thickness' => '2.0mm', 'price_delta' => 0],
                        ['id' => 'royal_2_5', 'name' => 'Royal Gulf Heavy Duty 2.5mm', 'badge' => 'Desert Wind Resistant', 'thickness' => '2.5mm', 'price_delta' => 80],
                        ['id' => 'slim_1_8', 'name' => 'Ultra-Slim Minimalist 1.8mm', 'badge' => 'Max Glass Horizon Line', 'thickness' => '1.8mm', 'price_delta' => 50],
                        ['id' => 'std_1_5', 'name' => 'Standard Structural Aluminum 1.5mm', 'badge' => 'Courtyard / Interior', 'thickness' => '1.5mm', 'price_delta' => -40],
                    ],
                    'glass_options' => [
                        ['id' => 'bronze_refl', 'name' => 'Double Glazed Reflective Bronze', 'tint' => '#8c6239', 'specs' => '24mm (6+12A+6) Sun Shield', 'price_delta' => 0],
                        ['id' => 'low_e_clear', 'name' => 'Clear Low-E Acoustic Double Glass', 'tint' => '#d6eaf8', 'specs' => '38dB soundproof insulation', 'price_delta' => 40],
                        ['id' => 'tinted_grey', 'name' => 'Smoky Tinted Grey Solar-Shield', 'tint' => '#4a4a4a', 'specs' => 'Anti-glare 85% heat rejection', 'price_delta' => 30],
                        ['id' => 'frosted_privacy', 'name' => 'Frosted Acid-Etched Privacy', 'tint' => '#e5e7eb', 'specs' => '100% privacy diffused light', 'price_delta' => 20],
                        ['id' => 'triple_acoustic', 'name' => 'Triple-Glazed Extreme Acoustic', 'tint' => '#aed6f1', 'specs' => '42dB Royal Majlis rating', 'price_delta' => 110],
                    ],
                    'color_options' => [
                        ['id' => 'black', 'name' => 'Matte Architectural Black', 'hex' => '#1e1e1e'],
                        ['id' => 'gold', 'name' => 'Champagne Gold / Bronze', 'hex' => '#c5a059'],
                        ['id' => 'anthracite', 'name' => 'Metallic Anthracite Charcoal', 'hex' => '#3b3e40'],
                        ['id' => 'sand_white', 'name' => 'Desert Sand Warm White', 'hex' => '#f4ede2'],
                    ],
                    'addons' => [
                        ['id' => 'fly_screen', 'name' => 'Stainless Steel Insect / Fly Screen', 'price' => 120, 'selected' => true],
                        ['id' => 'german_lock', 'name' => 'German Multi-Point Security Lock', 'price' => 180, 'selected' => false],
                        ['id' => 'motorized', 'name' => 'Motorized Smart Automation Ready', 'price' => 450, 'selected' => false],
                        ['id' => 'dust_seal', 'name' => 'Hermetic Sandstorm Dust Weatherseal', 'price' => 0, 'selected' => true],
                    ],
                ],
            ],
            [
                'name' => 'Acoustic Triple-Glazed Shutter Window',
                'slug' => 'acoustic-triple-glazed-shutter-window',
                'tagline' => '42dB sound dampening · Heavy-duty multi-shutter · Riyadh villa grade',
                'description' => 'Engineered for street-facing facades and master suites requiring complete acoustic silence. Triple-pane laminated security glazing with acoustic interlayers reduces traffic and ambient desert noise by 42 decibels. Reinforced structural sash accommodates high wind loads.',
                'product_type' => 'custom_fit',
                'price' => 950.00,
                'compare_at_price' => 1200.00,
                'dimensions' => '200cm H × 160cm W (Customizable)',
                'materials' => 'Heavy Structural Aluminum 2.2mm, Acoustic Laminate Glass',
                'color' => 'Anthracite Charcoal / Champagne Gold',
                'stock' => 80,
                'is_featured' => true,
                'rating' => 4.92,
                'reviews_count' => 24,
                'image_url' => 'https://images.unsplash.com/photo-1600566753376-12c8ab7fb75b?auto=format&fit=crop&w=1200&q=85',
                'gallery' => [
                    'https://images.unsplash.com/photo-1600566753376-12c8ab7fb75b?auto=format&fit=crop&w=1200&q=85',
                    'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=85',
                    'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1200&q=85',
                    'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=1200&q=85',
                ],
                'customization_options' => [
                    'min_price' => 950.00,
                    'max_price' => 1200.00,
                    'default_height' => 200,
                    'default_width' => 160,
                    'min_height' => 80,
                    'max_height' => 300,
                    'min_width' => 80,
                    'max_width' => 400,
                    'measurement_unit' => 'cm',
                    'shutters_options' => [
                        ['id' => '2_sliding', 'name' => '2 Shutters (Double Sliding)', 'description' => 'Heavy duty track', 'price_delta' => 0],
                        ['id' => '3_sliding', 'name' => '3 Shutters (Tri-Glide)', 'description' => 'Wide opening clearance', 'price_delta' => 110],
                        ['id' => '4_bifold', 'name' => '4 Shutters (Quad Multi-Fold)', 'description' => 'Full balcony opening', 'price_delta' => 180],
                    ],
                    'aluminum_options' => [
                        ['id' => 'alupco_2_2', 'name' => 'Alupco Structural Alloy 2.2mm', 'badge' => 'Heavy Duty 42dB', 'thickness' => '2.2mm', 'price_delta' => 0],
                        ['id' => 'royal_2_5', 'name' => 'Royal Gulf Extreme 2.5mm', 'badge' => 'Commercial Grade', 'thickness' => '2.5mm', 'price_delta' => 70],
                    ],
                    'glass_options' => [
                        ['id' => 'triple_acoustic', 'name' => 'Triple-Glazed Extreme Acoustic', 'tint' => '#aed6f1', 'specs' => '42dB Royal Majlis rating', 'price_delta' => 0],
                        ['id' => 'bronze_refl', 'name' => 'Double Glazed Reflective Bronze', 'tint' => '#8c6239', 'specs' => '24mm (6+12A+6)', 'price_delta' => -40],
                        ['id' => 'tinted_grey', 'name' => 'Smoky Tinted Grey Sun-Shield', 'tint' => '#4a4a4a', 'specs' => 'Anti-glare privacy', 'price_delta' => 20],
                    ],
                    'color_options' => [
                        ['id' => 'anthracite', 'name' => 'Metallic Anthracite Charcoal', 'hex' => '#3b3e40'],
                        ['id' => 'black', 'name' => 'Matte Architectural Black', 'hex' => '#1e1e1e'],
                        ['id' => 'gold', 'name' => 'Champagne Gold / Bronze', 'hex' => '#c5a059'],
                    ],
                    'addons' => [
                        ['id' => 'german_lock', 'name' => 'German Multi-Point Security Lock', 'price' => 180, 'selected' => true],
                        ['id' => 'fly_screen', 'name' => 'Stainless Steel Insect / Fly Screen', 'price' => 120, 'selected' => true],
                    ],
                ],
            ],
            [
                'name' => 'Minimalist Slim Aluminum Sliding Patio System',
                'slug' => 'minimalist-slim-aluminum-sliding-patio-system',
                'tagline' => 'Ultra-thin 18mm sightlines · Floor-to-ceiling panoramic glass · 2 to 4 shutters',
                'description' => 'Ultra-contemporary minimalist sliding glass door and window system. Sightline interlock width of only 18mm maximizes natural desert daylight and courtyard views. Concealed sub-floor drainage and flush threshold transition.',
                'product_type' => 'custom_fit',
                'price' => 1100.00,
                'compare_at_price' => 1400.00,
                'dimensions' => '240cm H × 200cm W (Custom height up to 3.5m)',
                'materials' => 'Thermally broken aviation grade aluminum, 28mm Low-E insulated glass',
                'color' => 'Matte Architectural Black / Desert Bronze',
                'stock' => 60,
                'is_featured' => true,
                'rating' => 4.98,
                'reviews_count' => 19,
                'image_url' => 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1200&q=85',
                'gallery' => [
                    'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1200&q=85',
                    'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=85',
                    'https://images.unsplash.com/photo-1600566753376-12c8ab7fb75b?auto=format&fit=crop&w=1200&q=85',
                ],
                'customization_options' => [
                    'min_price' => 1100.00,
                    'max_price' => 1400.00,
                    'default_height' => 240,
                    'default_width' => 200,
                    'min_height' => 120,
                    'max_height' => 350,
                    'min_width' => 120,
                    'max_width' => 600,
                    'measurement_unit' => 'cm',
                    'shutters_options' => [
                        ['id' => '2_sliding', 'name' => '2 Shutters (Double Slider)', 'description' => 'Sleek minimalist glide', 'price_delta' => 0],
                        ['id' => '3_sliding', 'name' => '3 Shutters (Tri-Rail Panoramic)', 'description' => 'Wide villa garden access', 'price_delta' => 160],
                        ['id' => '4_bifold', 'name' => '4 Shutters (Quad Pocket Slide)', 'description' => 'Walls disappear into pocket', 'price_delta' => 250],
                    ],
                    'aluminum_options' => [
                        ['id' => 'slim_1_8', 'name' => 'Ultra-Slim Minimalist 1.8mm Profile', 'badge' => '18mm Sightline', 'thickness' => '1.8mm', 'price_delta' => 0],
                        ['id' => 'alupco_2_0', 'name' => 'Alupco Reinforced Slim 2.0mm', 'badge' => 'High Wind Rating', 'thickness' => '2.0mm', 'price_delta' => 90],
                    ],
                    'glass_options' => [
                        ['id' => 'low_e_clear', 'name' => 'Clear Low-E Acoustic Double Glass', 'tint' => '#d6eaf8', 'specs' => '38dB clarity', 'price_delta' => 0],
                        ['id' => 'bronze_refl', 'name' => 'Double Glazed Reflective Bronze', 'tint' => '#8c6239', 'specs' => 'Privacy sun shield', 'price_delta' => 40],
                        ['id' => 'tinted_grey', 'name' => 'Smoky Tinted Grey Sun-Shield', 'tint' => '#4a4a4a', 'specs' => 'Modern luxury tint', 'price_delta' => 30],
                    ],
                    'color_options' => [
                        ['id' => 'black', 'name' => 'Matte Architectural Black', 'hex' => '#1e1e1e'],
                        ['id' => 'gold', 'name' => 'Champagne Gold / Bronze', 'hex' => '#c5a059'],
                    ],
                    'addons' => [
                        ['id' => 'motorized', 'name' => 'Motorized Smart Automation Ready', 'price' => 450, 'selected' => true],
                        ['id' => 'german_lock', 'name' => 'German Multi-Point Security Lock', 'price' => 180, 'selected' => true],
                    ],
                ],
            ],
            [
                'name' => 'Royal Saudi Villa Mashrabiya Glass Bay Window',
                'slug' => 'royal-saudi-villa-mashrabiya-glass-bay-window',
                'tagline' => 'Traditional Saudi geometric motif · Thermal double glazing · Majlis focal point',
                'description' => 'A statement architectural feature connecting rich Saudi heritage with high-performance modern glazing. Laser-cut geometric Mashrabiya lattice integrated between double-glazed panels provides shade, privacy, and cooling shadow play.',
                'product_type' => 'custom_fit',
                'price' => 850.00,
                'compare_at_price' => 1150.00,
                'dimensions' => '190cm H × 150cm W (Customizable)',
                'materials' => 'Laser-cut Anodized Aluminum lattice, Double insulated safety glass',
                'color' => 'Champagne Gold / Matte Black / Warm Sand',
                'stock' => 45,
                'is_featured' => true,
                'rating' => 4.96,
                'reviews_count' => 31,
                'image_url' => 'https://images.unsplash.com/photo-1600585154526-990dced4db0d?auto=format&fit=crop&w=1200&q=85',
                'gallery' => [
                    'https://images.unsplash.com/photo-1600585154526-990dced4db0d?auto=format&fit=crop&w=1200&q=85',
                    'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1200&q=85',
                    'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=85',
                ],
                'customization_options' => [
                    'min_price' => 850.00,
                    'max_price' => 1150.00,
                    'default_height' => 190,
                    'default_width' => 150,
                    'min_height' => 80,
                    'max_height' => 300,
                    'min_width' => 80,
                    'max_width' => 360,
                    'measurement_unit' => 'cm',
                    'shutters_options' => [
                        ['id' => '1_fixed', 'name' => '1 Fixed Mashrabiya Bay', 'description' => 'Intricate heritage pattern', 'price_delta' => 0],
                        ['id' => '2_sliding', 'name' => '2 Shutters (Mashrabiya Sliding)', 'description' => 'Dual operable lattice panels', 'price_delta' => 70],
                        ['id' => '3_sliding', 'name' => '3 Shutters (Tri-Panel Majlis Feature)', 'description' => 'Grand reception hall installation', 'price_delta' => 140],
                    ],
                    'aluminum_options' => [
                        ['id' => 'alupco_2_0', 'name' => 'Alupco Architectural Thermal 2.0mm', 'badge' => 'SASO Certified', 'thickness' => '2.0mm', 'price_delta' => 0],
                        ['id' => 'royal_2_5', 'name' => 'Royal Gulf Heavy Duty 2.5mm', 'badge' => 'Reinforced Framing', 'thickness' => '2.5mm', 'price_delta' => 80],
                    ],
                    'glass_options' => [
                        ['id' => 'bronze_refl', 'name' => 'Double Glazed Reflective Bronze', 'tint' => '#8c6239', 'specs' => 'Golden sunlight tone', 'price_delta' => 0],
                        ['id' => 'low_e_clear', 'name' => 'Clear Low-E Acoustic Double Glass', 'tint' => '#d6eaf8', 'specs' => '38dB acoustic barrier', 'price_delta' => 50],
                        ['id' => 'frosted_privacy', 'name' => 'Frosted Acid-Etched Privacy', 'tint' => '#e5e7eb', 'specs' => 'Diffused soft glow', 'price_delta' => 30],
                    ],
                    'color_options' => [
                        ['id' => 'gold', 'name' => 'Champagne Gold / Bronze', 'hex' => '#c5a059'],
                        ['id' => 'black', 'name' => 'Matte Architectural Black', 'hex' => '#1e1e1e'],
                        ['id' => 'sand_white', 'name' => 'Desert Sand Warm White', 'hex' => '#f4ede2'],
                    ],
                    'addons' => [
                        ['id' => 'dust_seal', 'name' => 'Hermetic Sandstorm Dust Weatherseal', 'price' => 0, 'selected' => true],
                        ['id' => 'fly_screen', 'name' => 'Stainless Steel Insect / Fly Screen', 'price' => 120, 'selected' => true],
                    ],
                ],
            ],
        ];

        foreach ($customProducts as $p) {
            Product::updateOrCreate(
                ['slug' => $p['slug']],
                array_merge($p, [
                    'category_id' => $category->id,
                    'brand_id' => $brandId,
                ])
            );
        }
    }
}
