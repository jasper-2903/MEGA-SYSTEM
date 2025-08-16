<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forecasts', function (Blueprint $table) {
            $table->id();
            $table->string('sku');
            $table->enum('item_type', ['material', 'product']);
            $table->date('horizon_start');
            $table->date('horizon_end');
            $table->enum('method', ['SMA3', 'SMA6', 'consumption_rate']);
            $table->integer('forecast_qty');
            $table->decimal('mad', 8, 2)->nullable(); // Mean Absolute Deviation
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['sku', 'item_type']);
            $table->index(['method', 'horizon_start']);
            $table->index(['item_type', 'is_active']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forecasts');
    }
};