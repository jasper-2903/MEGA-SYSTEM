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
        Schema::create('purchase_order_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->onDelete('cascade');
            $table->foreignId('material_id')->constrained('materials')->onDelete('cascade');
            $table->decimal('qty_ordered', 12, 4);
            $table->decimal('qty_received', 12, 4)->default(0);
            $table->decimal('unit_cost', 10, 2);
            $table->date('expected_date_line')->nullable();
            $table->enum('status', ['open', 'partial', 'received', 'cancelled'])->default('open');
            $table->timestamps();

            // Indexes
            $table->index('purchase_order_id');
            $table->index('material_id');
            $table->index('status');
            $table->index('expected_date_line');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_lines');
    }
};
