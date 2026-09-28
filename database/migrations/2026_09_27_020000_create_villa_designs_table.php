<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('villa_designs', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('type')->default('fitting'); // 'fitting' | 'sofa'
            $table->string('category_key'); // 'majlis' | 'thermal_window' | 'privacy_partition' | 'family_living'
            $table->string('category_name_en');
            $table->string('category_name_ar');
            $table->string('title_en');
            $table->string('title_ar');
            $table->text('tagline');
            $table->string('location_tag'); // e.g. 'Riyadh Villa · Hittin District'
            $table->text('photo_url');
            $table->text('detail_photo_url')->nullable();
            $table->decimal('price_sar', 12, 2);
            $table->decimal('price_usd', 12, 2);
            $table->decimal('advance_deposit_sar', 12, 2);
            $table->decimal('advance_deposit_usd', 12, 2);
            $table->json('features')->nullable(); // array of 3 bullet points with green checkmarks
            $table->json('specs')->nullable(); // dimensions, finishOrFabric, coreMaterial, hardware
            $table->json('config_data')->nullable(); // preset for interactive canvas
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('villa_designs');
    }
};
