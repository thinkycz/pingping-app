<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Monitor>
 */
class MonitorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'url' => $this->faker->url(),
            'alias' => $this->faker->words(2, true),
            'ssl_status' => 'None',
            'status' => 'Up',
            'uptime_30d' => 100,
            'response_time_ms' => $this->faker->numberBetween(80, 1500),
            'last_checked_at' => $this->faker->dateTimeBetween('-1 hour', 'now'),
            'is_active' => true,
        ];
    }
}
