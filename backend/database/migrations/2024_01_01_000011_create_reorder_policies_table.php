<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reorder_policies', function (Blueprint $table) {
            $table->id();
            $table->string('sku');
            $table->enum('item_type', ['material', 'product']);
            $table->integer('reorder_point');
            $table->integer('reorder_qty');
            $table->integer('min_level')->default(0);
            $table->integer('max_level')->nullable();
            $table->enum('planning_strategy', ['ROP', 'lot-for-lot'])->default('ROP');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['sku', 'item_type']);
            $table->index(['item_type', 'is_active']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reorder_policies');
    }
};