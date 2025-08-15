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
        Schema::create('forecasts', function (Blueprint $table) {
            $table->id();
            $table->string('sku');
            $table->enum('item_type', ['material', 'product']);
            $table->date('horizon_start');
            $table->date('horizon_end');
            $table->enum('method', ['SMA3', 'SMA6', 'consumption_rate']);
            $table->decimal('forecast_qty', 12, 4);
            $table->decimal('mad', 12, 4)->nullable(); // Mean Absolute Deviation
            $table->timestamps();

            // Indexes
            $table->index(['sku', 'item_type']);
            $table->index('method');
            $table->index(['horizon_start', 'horizon_end']);
            $table->unique(['sku', 'item_type', 'method', 'horizon_start']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forecasts');
    }
};
