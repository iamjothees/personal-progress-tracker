<?php

namespace Database\Factories;

use App\Models\User;
use App\Timer\Models\Timer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Timer\Models\Timer>
 */
class TimerFactory extends Factory
{
    protected $model = Timer::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'owner_id' => User::factory(),
            'started_at' => now(),
        ];
    }

    public function stopped(?Carbon $at = null): Factory{
        return $this->state(function (array $attributes) use ($at) {
            $at ??= Carbon::make($attributes['started_at'])->addMinutes(rand(15, 10080)); // 15 mins to 7 days
            return [
                'stopped_at' => $at,
            ];
        });
    }
}
