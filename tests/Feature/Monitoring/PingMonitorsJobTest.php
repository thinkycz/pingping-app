<?php

namespace Tests\Feature\Monitoring;

use App\Jobs\PingMonitorJob;
use App\Jobs\PingMonitorsJob;
use App\Models\Monitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class PingMonitorsJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_dispatches_only_active_due_monitors(): void
    {
        Queue::fake();
        $due = Monitor::factory()->create(['interval' => 5, 'last_checked_at' => now()->subMinutes(5)]);
        Monitor::factory()->create(['interval' => 5, 'last_checked_at' => now()->subMinutes(4)]);
        Monitor::factory()->create(['is_active' => false, 'last_checked_at' => now()->subHour()]);
        $pending = Monitor::factory()->create(['last_checked_at' => null]);

        (new PingMonitorsJob)->handle();

        Queue::assertPushed(PingMonitorJob::class, 2);
        Queue::assertPushed(PingMonitorJob::class, fn (PingMonitorJob $job): bool => $job->monitorId === $due->id);
        Queue::assertPushed(PingMonitorJob::class, fn (PingMonitorJob $job): bool => $job->monitorId === $pending->id);
    }
}
