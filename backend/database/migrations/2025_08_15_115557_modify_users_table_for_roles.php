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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'planner', 'warehouse', 'production', 'customer'])->default('customer');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            
            // Add indexes
            $table->index('role');
            $table->index('customer_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn(['role', 'customer_id', 'is_active']);
        });
    }
};
