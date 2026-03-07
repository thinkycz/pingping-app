<?php

namespace Tests\Feature;

use App\Jobs\PingMonitorJob;
use App\Models\Monitor;
use App\Models\User;
use App\Notifications\MonitorStatusChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PingMonitorJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_ping_monitor_job_updates_status_on_success(): void
    {
        Notification::fake();
        Http::fake([
            'https://example.com' => Http::response('OK', 200),
        ]);

        $user = User::factory()->create();
        $monitor = Monitor::factory()->create([
            'user_id' => $user->id,
            'url' => 'https://example.com',
            'status' => 'Down', // Start as down
        ]);

        $job = new PingMonitorJob($monitor);
        $job->handle();

        $monitor->refresh();
        $this->assertEquals('Up', $monitor->status);

        $this->assertDatabaseHas('ping_logs', [
            'monitor_id' => $monitor->id,
            'status' => 'Up',
        ]);

        Notification::assertSentTo(
            $user,
            MonitorStatusChanged::class,
            function ($notification, $channels) use ($monitor) {
                return $notification->monitor->id === $monitor->id && $notification->newStatus === 'Up';
            }
        );
    }

    public function test_ping_monitor_job_updates_status_on_failure(): void
    {
        Notification::fake();
        Http::fake([
            'https://example.com' => Http::response('Not Found', 404),
        ]);

        $user = User::factory()->create();
        $monitor = Monitor::factory()->create([
            'user_id' => $user->id,
            'url' => 'https://example.com',
            'status' => 'Up', // Start as up
        ]);

        $job = new PingMonitorJob($monitor);
        $job->handle();

        $monitor->refresh();
        $this->assertEquals('Down', $monitor->status);

        $this->assertDatabaseHas('ping_logs', [
            'monitor_id' => $monitor->id,
            'status' => 'Down',
        ]);

        Notification::assertSentTo(
            $user,
            MonitorStatusChanged::class,
            function ($notification, $channels) use ($monitor) {
                return $notification->monitor->id === $monitor->id && $notification->newStatus === 'Down';
            }
        );
    }
}
