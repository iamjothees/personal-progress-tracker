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
        Schema::create('project_timer', function (Blueprint $table) {
            $table->primary(['project_id', 'timer_id']);
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('timer_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_timer');
    }
};
