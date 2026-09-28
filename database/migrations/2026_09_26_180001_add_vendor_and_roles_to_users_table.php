<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('vendor_id')->nullable()->after('id')->constrained('vendors')->nullOnDelete();
            $table->string('phone')->nullable()->after('email');
            $table->enum('role', ['SuperAdmin', 'VendorAdmin', 'ProductionManager', 'SalesStaff', 'Customer'])->default('Customer')->after('password');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->after('role');

            $table->index(['vendor_id', 'role']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']);
            $table->dropColumn(['vendor_id', 'phone', 'role', 'status']);
        });
    }
};
