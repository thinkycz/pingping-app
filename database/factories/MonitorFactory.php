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
            'ssl_status' => $this->faker->randomElement(['None', 'Invalid', 'Valid', 'disabled']),
            'status' => $this->faker->randomElement(['Up', 'Down', 'disabled']),
            'uptime_percentage' => $this->faker->randomFloat(2, 80, 100),
            'response_time' => $this->faker->randomFloat(4, 0.1, 1.5),
            'last_checked_at' => $this->faker->dateTimeBetween('-1 hour', 'now'),
            'is_active' => $this->faker->boolean(80),
        ];
    }
}
