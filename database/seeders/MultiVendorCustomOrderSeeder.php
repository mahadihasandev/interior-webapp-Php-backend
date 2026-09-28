<?php

namespace Database\Seeders;

use App\Models\CustomOrder;
use App\Models\OrderTimelineEvent;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MultiVendorCustomOrderSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Vendors
        $apexVendor = Vendor::updateOrCreate(
            ['slug' => 'apex-architectural-glass'],
            [
                'name' => 'Apex Architectural Glass & Aluminum Ltd.',
                'contact_email' => 'sales@apexglass.com',
                'phone' => '+1 (555) 890-1234',
                'city' => 'New York, NY',
                'address' => '120 Industrial Parkway, Queens, NY',
                'commission_rate' => 10.00,
                'status' => 'active',
                'business_details' => [
                    'specialties' => ['Partition Glass', 'Custom Aluminum Windows', 'Fluted Privacy Glass'],
                    'workshop_capacity_sqft' => 15000,
                ],
            ]
        );

        $ironCraftVendor = Vendor::updateOrCreate(
            ['slug' => 'ironcraft-fittings'],
            [
                'name' => 'IronCraft Architectural Gates & Canopy Systems',
                'contact_email' => 'contact@ironcraft.com',
                'phone' => '+1 (555) 789-5678',
                'city' => 'Chicago, IL',
                'address' => '84 Steel Mill Rd, Chicago, IL',
                'commission_rate' => 8.50,
                'status' => 'active',
                'business_details' => [
                    'specialties' => ['Iron Gates', 'Sunshade Canopies', 'Heavy Duty Display Racks'],
                ],
            ]
        );

        // 2. Create Staff & Customer Users
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@interior.com'],
            [
                'name' => 'Alexander Wright (SuperAdmin)',
                'phone' => '+1 (555) 100-0001',
                'password' => Hash::make('password123'),
                'role' => 'SuperAdmin',
                'status' => 'active',
            ]
        );

        $customer = User::updateOrCreate(
            ['email' => 'client@atelier.design'],
            [
                'name' => 'Julian Thorne',
                'phone' => '+1 (555) 321-7890',
                'password' => Hash::make('password123'),
                'role' => 'Customer',
                'status' => 'active',
            ]
        );

        $vendorAdmin = User::updateOrCreate(
            ['email' => 'admin@apexglass.com'],
            [
                'vendor_id' => $apexVendor->id,
                'name' => 'Marcus Vance',
                'phone' => '+1 (555) 890-1111',
                'password' => Hash::make('password123'),
                'role' => 'VendorAdmin',
                'status' => 'active',
            ]
        );

        $prodManager = User::updateOrCreate(
            ['email' => 'workshop@apexglass.com'],
            [
                'vendor_id' => $apexVendor->id,
                'name' => 'Elena Rostova',
                'phone' => '+1 (555) 890-2222',
                'password' => Hash::make('password123'),
                'role' => 'ProductionManager',
                'status' => 'active',
            ]
        );

        $salesStaff = User::updateOrCreate(
            ['email' => 'sales@apexglass.com'],
            [
                'vendor_id' => $apexVendor->id,
                'name' => 'Liam Sterling',
                'phone' => '+1 (555) 890-3333',
                'password' => Hash::make('password123'),
                'role' => 'SalesStaff',
                'status' => 'active',
            ]
        );

        $ironAdmin = User::updateOrCreate(
            ['email' => 'admin@ironcraft.com'],
            [
                'vendor_id' => $ironCraftVendor->id,
                'name' => 'Damon Cole',
                'phone' => '+1 (555) 789-9999',
                'password' => Hash::make('password123'),
                'role' => 'VendorAdmin',
                'status' => 'active',
            ]
        );

        // 3. Create Sample Custom Orders
        $order1 = CustomOrder::updateOrCreate(
            ['order_number' => 'CUST-2026-X9B21'],
            [
                'customer_id' => $customer->id,
                'vendor_id' => $apexVendor->id,
                'title' => 'Series 900 Architectural Aluminum Partition & Fluted Glass',
                'dimensions' => [
                    'height' => 96,
                    'width' => 72,
                    'depth' => 3.5,
                    'unit' => 'inches',
                    'area_sqft' => 48.0,
                ],
                'material_specs' => [
                    'profile_gauge' => '2.0mm Heavy Duty',
                    'alloy_grade' => '6063-T6 Architectural Grade',
                    'glass_type' => '10mm Toughened Fluted Ribbed',
                    'glass_thickness' => '10mm',
                ],
                'color_finish' => 'Champagne Gold Anodized',
                'addon_features' => [
                    'Acoustic Sound-Dampening Glass Interlayer',
                    'Dual-Direction Hydraulic Soft-Close Dampers',
                ],
                'customer_notes' => 'Recessed ceiling track installation for master suite walk-in divider.',
                'quoted_total_price' => 3450.00,
                'advance_amount_required' => 1380.00,
                'advance_paid_at' => now()->subDays(2),
                'estimated_completion_days' => 14,
                'current_stage' => 'powder_coating',
                'status' => 'in_production',
                'seller_notes' => 'Alloy extrusions calibrated to ±0.5mm tolerance. Ready for surface electro-curing.',
            ]
        );

        // Order 1 Timeline Events
        OrderTimelineEvent::updateOrCreate(
            ['custom_order_id' => $order1->id, 'stage' => 'order_placed'],
            [
                'title' => 'Order Evaluated & Quoted by Apex Engineering',
                'description' => 'Feasibility verified. Quoted $3,450.00 with $1,380.00 advance requirement.',
                'updated_by' => $vendorAdmin->id,
                'created_at' => now()->subDays(5),
            ]
        );

        OrderTimelineEvent::updateOrCreate(
            ['custom_order_id' => $order1->id, 'stage' => 'raw_material_sourcing'],
            [
                'title' => 'Advance Payment Confirmed & Materials Reserved',
                'description' => 'Client 40% advance deposit received. 6063-T6 aluminum ingots and fluted glass reserved.',
                'updated_by' => $prodManager->id,
                'created_at' => now()->subDays(2),
            ]
        );

        OrderTimelineEvent::updateOrCreate(
            ['custom_order_id' => $order1->id, 'stage' => 'cutting_welding'],
            [
                'title' => 'Precision CNC Cutting & TIG Structural Welding',
                'description' => 'Aluminum perimeter frame cut to 96" x 72". Corner miters precision joined.',
                'updated_by' => $prodManager->id,
                'created_at' => now()->subDay(),
            ]
        );

        OrderTimelineEvent::updateOrCreate(
            ['custom_order_id' => $order1->id, 'stage' => 'powder_coating'],
            [
                'title' => 'Surface Treatment: Champagne Gold Anodization',
                'description' => 'Frames placed in 25-micron chemical anodization tanks.',
                'photo_evidence_url' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&w=600&q=80',
                'updated_by' => $prodManager->id,
                'created_at' => now()->subHours(6),
            ]
        );

        // Order 2: In Pending Review Queue (for seller quote demo)
        CustomOrder::updateOrCreate(
            ['order_number' => 'CUST-2026-M4F89'],
            [
                'customer_id' => $customer->id,
                'vendor_id' => $apexVendor->id,
                'title' => 'Custom Narrow-Profile Thermal Break Casement Window',
                'dimensions' => [
                    'height' => 84,
                    'width' => 48,
                    'depth' => 4.0,
                    'unit' => 'inches',
                    'area_sqft' => 28.0,
                ],
                'material_specs' => [
                    'profile_gauge' => '2.5mm Extreme Weather',
                    'alloy_grade' => '6063-T6',
                    'glass_type' => 'Low-E Double Glazed Argon Filled',
                    'glass_thickness' => '24mm Unit',
                ],
                'color_finish' => 'Matte Architectural Black',
                'addon_features' => ['Integrated Thermal Break', 'Heavy Duty Multi-Point Lock'],
                'customer_notes' => 'Requires wind load certification for high-rise apartment.',
                'quoted_total_price' => null,
                'advance_amount_required' => null,
                'current_stage' => 'order_placed',
                'status' => 'pending_review',
            ]
        );

        // Order 3: Gate & Canopy order
        CustomOrder::updateOrCreate(
            ['order_number' => 'CUST-2026-G1A03'],
            [
                'customer_id' => $customer->id,
                'vendor_id' => $ironCraftVendor->id,
                'title' => 'Geometric Laser-Cut Cantilever Driveway Gate',
                'dimensions' => [
                    'height' => 78,
                    'width' => 144,
                    'unit' => 'inches',
                ],
                'material_specs' => [
                    'profile_gauge' => '10-Gauge Solid Steel Plate',
                    'alloy_grade' => 'Structural Carbon Steel',
                    'glass_type' => 'None',
                ],
                'color_finish' => 'Textured Charcoal Metallic',
                'quoted_total_price' => 5200.00,
                'advance_amount_required' => 2600.00,
                'advance_paid_at' => now()->subDays(6),
                'estimated_completion_days' => 21,
                'current_stage' => 'assembly_glass_fitting',
                'status' => 'in_production',
            ]
        );
    }
}
