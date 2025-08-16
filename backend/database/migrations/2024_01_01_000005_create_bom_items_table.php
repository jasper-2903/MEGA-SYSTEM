<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bom_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bom_id');
            $table->unsignedBigInteger('material_id');
            $table->decimal('qty_per', 10, 4);
            $table->decimal('scrap_factor', 5, 4)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('bom_id')->references('id')->on('boms')->onDelete('cascade');
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade');
            $table->unique(['bom_id', 'material_id']);
            $table->index('bom_id');
            $table->index('material_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bom_items');
    }
};