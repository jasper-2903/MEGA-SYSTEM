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
        Schema::create('production_orders', function (Blueprint $table) {
            $table->id();
            $table->string('wo_number')->unique();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->decimal('qty_planned', 12, 4);
            $table->decimal('qty_completed', 12, 4)->default(0);
            $table->enum('status', ['planned', 'released', 'in_process', 'on_hold', 'completed', 'closed', 'cancelled'])->default('planned');
            $table->date('start_date');
            $table->date('due_date');
            $table->integer('priority')->default(5); // 1-10 scale
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('wo_number');
            $table->index('product_id');
            $table->index('status');
            $table->index('start_date');
            $table->index('due_date');
            $table->index('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_orders');
    }
};
