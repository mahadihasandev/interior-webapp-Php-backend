<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('vendor_id')->nullable()->after('id')->constrained('vendors')->cascadeOnDelete();
            $table->string('sku')->nullable()->after('slug');
            $table->enum('product_type', ['ready_made', 'custom_fit'])->default('ready_made')->after('name');
            $table->jsonb('customization_options')->nullable()->after('description'); 
            // { min_height: 48, max_height: 120, min_width: 36, max_width: 144, finish_options: ["Matte Black", "Champagne Gold"], gauge_options: ["1.5mm", "2.0mm"] }

            $table->index(['vendor_id', 'product_type']);
            $table->index('product_type');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']);
            $table->dropColumn(['vendor_id', 'sku', 'product_type', 'customization_options']);
        });
    }
};
