<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('contact_email')->unique();
            $table->string('phone');
            $table->string('city');
            $table->text('address')->nullable();
            $table->decimal('commission_rate', 5, 2)->default(10.00); // 10.00%
            $table->enum('status', ['active', 'suspended', 'pending'])->default('active');
            $table->jsonb('business_details')->nullable(); // Tax ID, Trade License, Workshop Location
            $table->timestamps();

            $table->index(['status', 'city']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
