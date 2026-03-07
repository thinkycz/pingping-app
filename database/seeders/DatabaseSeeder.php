<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $monitors = [
            ['alias' => 'Google', 'url' => 'https://google.com', 'ssl_status' => 'None', 'status' => 'Down', 'uptime_percentage' => 99.45, 'response_time' => 0.1429, 'last_checked_at' => now()->subSeconds(5)],
            ['alias' => 'Another Competitor', 'url' => 'https://competitor.com', 'ssl_status' => 'Invalid', 'status' => 'Up', 'uptime_percentage' => 90.37, 'response_time' => 0.2906, 'last_checked_at' => now()->subSeconds(7)],
            ['alias' => 'My Blog', 'url' => 'https://blog.com', 'ssl_status' => 'Valid', 'status' => 'Down', 'uptime_percentage' => 89.45, 'response_time' => 0.4517, 'last_checked_at' => now()->subMinutes(2)],
            ['alias' => 'inertiajs.com', 'url' => 'https://inertiajs.com', 'ssl_status' => 'Valid', 'status' => 'Up', 'uptime_percentage' => 100, 'response_time' => 0.2324, 'last_checked_at' => now()->subMinute()],
            ['alias' => 'laravel-livewire.com', 'url' => 'https://laravel-livewire.com', 'ssl_status' => 'disabled', 'status' => 'disabled', 'uptime_percentage' => 100, 'response_time' => 0.3929, 'last_checked_at' => now()->subHour(), 'is_active' => false],
            ['alias' => 'Laravel Website', 'url' => 'https://laravel.com', 'ssl_status' => 'Valid', 'status' => 'Up', 'uptime_percentage' => 100, 'response_time' => 0.1080, 'last_checked_at' => now()->subSeconds(2)],
            ['alias' => 'PingPing', 'url' => 'https://pingping.localhost', 'ssl_status' => 'Valid', 'status' => 'Up', 'uptime_percentage' => 100, 'response_time' => 0.1896, 'last_checked_at' => now()->subSeconds(10)],
            ['alias' => 'tailwindcss.com', 'url' => 'https://tailwindcss.com', 'ssl_status' => 'disabled', 'status' => 'disabled', 'uptime_percentage' => 100, 'response_time' => 0.2375, 'last_checked_at' => now()->subMinutes(30), 'is_active' => false],
            ['alias' => 'Tailwind UI', 'url' => 'https://tailwindui.com', 'ssl_status' => 'Valid', 'status' => 'Up', 'uptime_percentage' => 100, 'response_time' => 0.2350, 'last_checked_at' => now()->subMinute()],
            ['alias' => 'Uptime Competitor', 'url' => 'https://uptime.com', 'ssl_status' => 'Valid', 'status' => 'Down', 'uptime_percentage' => 92.80, 'response_time' => 0.3861, 'last_checked_at' => now()->subSeconds(8)],
        ];

        foreach ($monitors as $monitor) {
            \App\Models\Monitor::create(array_merge($monitor, ['user_id' => $user->id]));
        }

        \App\Models\Monitor::factory(7)->create(['user_id' => $user->id]);
    }
}
