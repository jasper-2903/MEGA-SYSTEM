<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routing_steps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('routing_id');
            $table->integer('seq');
            $table->unsignedBigInteger('work_center_id');
            $table->string('name'); // prep, assembly, finishing, qc
            $table->integer('std_time_minutes');
            $table->integer('move_time')->default(0);
            $table->integer('wait_time')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('routing_id')->references('id')->on('routings')->onDelete('cascade');
            $table->foreign('work_center_id')->references('id')->on('work_centers')->onDelete('cascade');
            $table->unique(['routing_id', 'seq']);
            $table->index('routing_id');
            $table->index('work_center_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routing_steps');
    }
};