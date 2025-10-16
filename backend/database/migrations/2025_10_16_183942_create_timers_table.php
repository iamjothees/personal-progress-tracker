<?php

use App\Models\Timer;
use App\Models\TimerActivity;
use App\Models\User;
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
        Schema::create('timers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->restrictOnDelete();
            $table->timestamps();
        });

        Schema::create('timer_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Timer::class)->constrained()->cascadeOnDelete();
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
        });

        Schema::create('timer_activity_pauses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(TimerActivity::class)->constrained()->cascadeOnDelete();
            $table->timestamp('paused_at');
            $table->timestamp('resumed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timer_activity_pauses');
        Schema::dropIfExists('timer_activities');
        Schema::dropIfExists('timers');
    }
};
