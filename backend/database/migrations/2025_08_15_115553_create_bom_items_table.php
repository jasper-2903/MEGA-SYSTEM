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
        Schema::create('bom_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bom_id')->constrained('boms')->onDelete('cascade');
            $table->foreignId('material_id')->constrained('materials')->onDelete('cascade');
            $table->decimal('qty_per', 10, 4); // Quantity of material per unit of product
            $table->decimal('scrap_factor', 5, 4)->default(0.0000); // 0.0500 = 5% scrap
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes and constraints
            $table->unique(['bom_id', 'material_id']);
            $table->index('bom_id');
            $table->index('material_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bom_items');
    }
};
