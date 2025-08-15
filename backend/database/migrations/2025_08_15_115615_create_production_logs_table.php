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
        Schema::create('production_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_order_id')->constrained('production_orders')->onDelete('cascade');
            $table->foreignId('routing_step_id')->constrained('routing_steps')->onDelete('cascade');
            $table->date('date');
            $table->decimal('qty_started', 12, 4)->default(0);
            $table->decimal('qty_completed', 12, 4)->default(0);
            $table->decimal('qty_scrap', 12, 4)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Indexes
            $table->index('production_order_id');
            $table->index('routing_step_id');
            $table->index('date');
            $table->index('recorded_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_logs');
    }
};
