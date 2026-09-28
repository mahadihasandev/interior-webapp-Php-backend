<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_timeline_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_order_id')->constrained('custom_orders')->cascadeOnDelete();
            $table->string('stage');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('photo_evidence_url')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['custom_order_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_timeline_events');
    }
};
