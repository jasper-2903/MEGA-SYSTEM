<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consumption_history', function (Blueprint $table) {
            $table->id();
            $table->string('sku');
            $table->enum('item_type', ['material', 'product']);
            $table->date('date');
            $table->integer('qty_issued')->default(0); // for materials
            $table->integer('qty_sold')->default(0); // for products
            $table->timestamps();

            $table->index(['sku', 'item_type', 'date']);
            $table->index(['item_type', 'date']);
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consumption_history');
    }
};