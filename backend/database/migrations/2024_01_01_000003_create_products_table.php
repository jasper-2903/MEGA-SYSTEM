<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('name');
            $table->string('category');
            $table->string('dimensions')->nullable(); // L/W/H format
            $table->string('finish')->nullable();
            $table->string('unit_of_measure')->default('PCS');
            $table->decimal('unit_weight', 8, 2)->nullable(); // in kg
            $table->decimal('price', 10, 2);
            $table->integer('lead_time_days')->default(5);
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['sku', 'is_active']);
            $table->index(['category', 'is_active']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};