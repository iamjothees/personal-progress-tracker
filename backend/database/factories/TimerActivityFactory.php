<?php

namespace Database\Factories;

use App\Timer\Models\Timer;
use App\Timer\Models\TimerActivity;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Timer\Models\TimerActivity>
 */
class TimerActivityFactory extends Factory
{
    protected $model = TimerActivity::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'timer_id' => Timer::factory(),
            'paused_at' => function (array $attributes){
                $timer = Timer::first($attributes['timer_id']);
                $at = (match(true){
                    $timer->latestActivity === null => $timer->started_at,
                    $timer->latestActivity->resumed_at !== null => $timer->latestActivity->resumed_at,
                    default => $timer->latestActivity?->paused_at ?? $timer->started_at,
                })->addMinutes(rand(15, 10080)); // 15 mins to 7 days
            }
        ];
    }

    public function resumed(?Carbon $at = null): Factory{
        return $this->state(function (array $attributes) use ($at) {
            $at ??= Carbon::make($attributes['paused_at'])->addMinutes(rand(15, 10080)); // 15 mins to 7 days
            return [
                'resumed_at' => $at,
            ];
        });
    }
}
