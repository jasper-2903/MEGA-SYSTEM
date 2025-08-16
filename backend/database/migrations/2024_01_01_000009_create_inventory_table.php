<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->string('sku');
            $table->enum('item_type', ['material', 'product']);
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('location_id');
            $table->integer('on_hand')->default(0);
            $table->integer('allocated')->default(0);
            $table->integer('on_order')->default(0);
            $table->timestamp('last_counted_at')->nullable();
            $table->timestamps();

            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
            $table->unique(['sku', 'warehouse_id', 'location_id']);
            $table->index(['sku', 'item_type']);
            $table->index(['warehouse_id', 'location_id']);
            $table->index('item_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};