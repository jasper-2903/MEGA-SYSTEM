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
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('sku');
            $table->enum('item_type', ['material', 'product']);
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->foreignId('location_id')->constrained('locations')->onDelete('cascade');
            $table->enum('txn_type', ['receipt', 'issue', 'adjust+', 'adjust-', 'consume', 'produce', 'transfer']);
            $table->decimal('qty', 12, 4);
            $table->enum('reference_type', ['SO', 'PO', 'WO', 'COUNT', 'TRANSFER', 'MANUAL'])->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Indexes
            $table->index(['sku', 'item_type']);
            $table->index('warehouse_id');
            $table->index('location_id');
            $table->index('txn_type');
            $table->index('reference_type');
            $table->index('reference_id');
            $table->index('created_by');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
