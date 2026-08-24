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
            'email' => 'testovaci@admin.cz',
            'password' => bcrypt('password'),
        ]);

        $monitors = [
            ['alias' => 'Google', 'url' => 'https://google.com', 'ssl_status' => 'None', 'status' => 'Down', 'uptime_30d' => 99.45, 'response_time_ms' => 143, 'last_checked_at' => now()->subSeconds(5)],
            ['alias' => 'Another Competitor', 'url' => 'https://competitor.com', 'ssl_status' => 'Invalid', 'status' => 'Up', 'uptime_30d' => 90.37, 'response_time_ms' => 291, 'last_checked_at' => now()->subSeconds(7)],
            ['alias' => 'My Blog', 'url' => 'https://blog.com', 'ssl_status' => 'Valid', 'status' => 'Down', 'uptime_30d' => 89.45, 'response_time_ms' => 452, 'last_checked_at' => now()->subMinutes(2)],
            ['alias' => 'inertiajs.com', 'url' => 'https://inertiajs.com', 'ssl_status' => 'Valid', 'status' => 'Up', 'uptime_30d' => 100, 'response_time_ms' => 232, 'last_checked_at' => now()->subMinute()],
            ['alias' => 'laravel-livewire.com', 'url' => 'https://laravel-livewire.com', 'ssl_status' => 'None', 'status' => 'Up', 'uptime_30d' => 100, 'response_time_ms' => 393, 'last_checked_at' => now()->subHour(), 'is_active' => false],
            ['alias' => 'Laravel Website', 'url' => 'https://laravel.com', 'ssl_status' => 'Valid', 'status' => 'Up', 'uptime_30d' => 100, 'response_time_ms' => 108, 'last_checked_at' => now()->subSeconds(2)],
            ['alias' => 'PingPing', 'url' => 'https://example.com', 'ssl_status' => 'Valid', 'status' => 'Up', 'uptime_30d' => 100, 'response_time_ms' => 190, 'last_checked_at' => now()->subSeconds(10)],
            ['alias' => 'tailwindcss.com', 'url' => 'https://tailwindcss.com', 'ssl_status' => 'None', 'status' => 'Up', 'uptime_30d' => 100, 'response_time_ms' => 238, 'last_checked_at' => now()->subMinutes(30), 'is_active' => false],
            ['alias' => 'Tailwind UI', 'url' => 'https://tailwindui.com', 'ssl_status' => 'Valid', 'status' => 'Up', 'uptime_30d' => 100, 'response_time_ms' => 235, 'last_checked_at' => now()->subMinute()],
            ['alias' => 'Uptime Competitor', 'url' => 'https://uptime.com', 'ssl_status' => 'Valid', 'status' => 'Down', 'uptime_30d' => 92.80, 'response_time_ms' => 386, 'last_checked_at' => now()->subSeconds(8)],
        ];

        foreach ($monitors as $monitor) {
            \App\Models\Monitor::create(array_merge($monitor, ['user_id' => $user->id]));
        }

        \App\Models\Monitor::factory(7)->create(['user_id' => $user->id]);
    }
}
