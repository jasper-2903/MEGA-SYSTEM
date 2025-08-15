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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('name');
            $table->string('category');
            $table->decimal('length', 8, 2)->nullable(); // L dimension in cm
            $table->decimal('width', 8, 2)->nullable();  // W dimension in cm
            $table->decimal('height', 8, 2)->nullable(); // H dimension in cm
            $table->string('finish')->nullable();
            $table->string('unit_of_measure')->default('EACH');
            $table->decimal('unit_weight', 8, 2)->nullable(); // kg
            $table->decimal('price', 10, 2);
            $table->integer('lead_time_days')->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('sku');
            $table->index('category');
            $table->index('is_active');
            $table->index(['category', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
