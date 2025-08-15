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
        Schema::create('consumption_history', function (Blueprint $table) {
            $table->id();
            $table->string('sku');
            $table->enum('item_type', ['material', 'product']);
            $table->date('date');
            $table->decimal('qty_issued', 12, 4)->default(0); // for materials
            $table->decimal('qty_sold', 12, 4)->default(0);   // for products
            $table->timestamps();

            // Indexes
            $table->index(['sku', 'item_type']);
            $table->index('date');
            $table->unique(['sku', 'item_type', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumption_history');
    }
};
