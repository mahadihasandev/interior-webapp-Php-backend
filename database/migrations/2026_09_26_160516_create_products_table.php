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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('tagline')->nullable();
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->decimal('compare_at_price', 10, 2)->nullable();
            $table->string('dimensions')->nullable();
            $table->string('materials')->nullable();
            $table->string('color')->nullable();
            $table->integer('stock')->default(10);
            $table->string('image_url');
            $table->json('gallery')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->decimal('rating', 3, 2)->default(4.90);
            $table->integer('reviews_count')->default(12);
            $table->timestamps();

            // Performance Indexes
            $table->index(['category_id', 'is_featured', 'price']);
            $table->index('price');
            $table->index('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
