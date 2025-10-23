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
        Schema::create('timer_matrix', function (Blueprint $table) {
            $table->primary(['time_trackable_id', 'time_trackable_type', 'timer_id']);
            $table->morphs('time_trackable');
            $table->foreignId('timer_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timer_matrix');
    }
};
