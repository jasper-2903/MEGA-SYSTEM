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
        Schema::create('reorder_policies', function (Blueprint $table) {
            $table->id();
            $table->string('sku');
            $table->enum('item_type', ['material', 'product']);
            $table->integer('reorder_point');
            $table->integer('reorder_qty');
            $table->integer('min_level');
            $table->integer('max_level');
            $table->enum('planning_strategy', ['ROP', 'lot-for-lot'])->default('ROP');
            $table->timestamps();

            // Indexes and constraints
            $table->unique(['sku', 'item_type']);
            $table->index('sku');
            $table->index('item_type');
            $table->index('planning_strategy');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reorder_policies');
    }
};
