<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_orders', function (Blueprint $table) {
            $table->id();
            $table->string('wo_number')->unique();
            $table->unsignedBigInteger('product_id');
            $table->integer('qty_planned');
            $table->integer('qty_completed')->default(0);
            $table->enum('status', ['planned', 'released', 'in_process', 'on_hold', 'completed', 'closed', 'cancelled'])->default('planned');
            $table->date('start_date');
            $table->date('due_date');
            $table->integer('priority')->default(5); // 1=highest, 10=lowest
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->index(['wo_number', 'status']);
            $table->index(['product_id', 'status']);
            $table->index(['status', 'due_date']);
            $table->index(['priority', 'due_date']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_orders');
    }
};