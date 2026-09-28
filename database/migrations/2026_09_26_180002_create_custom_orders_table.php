<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();

            $table->string('title');
            $table->jsonb('dimensions'); // { height: 84, width: 96, depth: 3, unit: "inches", area_sqft: 56.0 }
            $table->jsonb('material_specs'); // { profile_gauge: "1.8mm", alloy_grade: "6063-T6", glass_type: "Tempered Fluted", glass_thickness: "10mm" }
            $table->string('color_finish'); // e.g. "Matte Charcoal Black", "Champagne Gold Anodized"
            $table->jsonb('addon_features')->nullable(); // ["Acoustic Interlayer", "Heavy Duty Soft-Close Dampers"]
            $table->text('customer_notes')->nullable();

            // Pricing & Terms set by Vendor
            $table->decimal('quoted_total_price', 12, 2)->nullable();
            $table->decimal('advance_amount_required', 12, 2)->nullable();
            $table->timestamp('advance_paid_at')->nullable();
            $table->timestamp('full_paid_at')->nullable();
            $table->integer('estimated_completion_days')->nullable();

            // Production & Approval Lifecycle
            $table->string('current_stage')->default('order_placed');
            // Stages: order_placed, raw_material_sourcing, cutting_welding, powder_coating, assembly_glass_fitting, quality_check, ready_for_dispatch, delivered

            $table->string('status')->default('pending_review');
            // Statuses: pending_review, reviewed_quoted, accepted, rejected, in_production, ready, completed, cancelled

            $table->text('seller_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            // Composite indexes for fast seller queue queries & user order lookups
            $table->index(['vendor_id', 'status']);
            $table->index(['customer_id', 'status']);
            $table->index(['vendor_id', 'current_stage']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_orders');
    }
};
