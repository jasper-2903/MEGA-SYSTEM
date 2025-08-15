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
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->string('sku');
            $table->enum('item_type', ['material', 'product']);
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->foreignId('location_id')->constrained('locations')->onDelete('cascade');
            $table->decimal('on_hand', 12, 4)->default(0);
            $table->decimal('allocated', 12, 4)->default(0);
            $table->decimal('on_order', 12, 4)->default(0);
            $table->timestamp('last_counted_at')->nullable();
            $table->timestamps();

            // Indexes and constraints
            $table->unique(['sku', 'item_type', 'warehouse_id', 'location_id']);
            $table->index('sku');
            $table->index('item_type');
            $table->index('warehouse_id');
            $table->index('location_id');
            $table->index(['sku', 'item_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};
