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
        Schema::create('routing_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('routing_id')->constrained('routings')->onDelete('cascade');
            $table->integer('seq'); // Sequence number
            $table->foreignId('work_center_id')->constrained('work_centers')->onDelete('cascade');
            $table->string('name'); // prep, assembly, finishing, qc
            $table->decimal('std_time_minutes', 8, 2)->default(0);
            $table->decimal('move_time', 8, 2)->default(0);
            $table->decimal('wait_time', 8, 2)->default(0);
            $table->timestamps();

            // Indexes and constraints
            $table->unique(['routing_id', 'seq']);
            $table->index('routing_id');
            $table->index('work_center_id');
            $table->index('seq');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routing_steps');
    }
};
