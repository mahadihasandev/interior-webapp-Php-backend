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
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->string('email');
            $table->string('phone');
            $table->string('room_type');
            $table->string('budget_range');
            $table->string('style_preference')->nullable();
            $table->text('notes')->nullable();
            $table->date('preferred_date')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            // Performance Indexes
            $table->index(['status', 'preferred_date']);
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
