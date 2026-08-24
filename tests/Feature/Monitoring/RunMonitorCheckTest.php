<?php

namespace Tests\Feature\Monitoring;

use App\Models\Monitor;
use App\Models\PingLog;
use App\Monitoring\HttpMonitorProbe;
use App\Monitoring\ProbeResult;
use App\Monitoring\RunMonitorCheck;
use App\Notifications\MonitorStatusChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Mockery\MockInterface;
use Tests\TestCase;

class RunMonitorCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_initial_success_is_recorded_without_a_notification(): void
    {
        Notification::fake();
        $monitor = Monitor::factory()->create([
            'last_checked_at' => null,
            'status' => 'Up',
        ]);

        $this->runWith($monitor, new ProbeResult('Up', 125, 204, 'Valid', now()->addMonth()));

        $monitor->refresh();
        $this->assertSame('Up', $monitor->status);
        $this->assertSame(125, $monitor->response_time_ms);
        $this->assertSame(204, $monitor->last_http_status);
        $this->assertSame('100.00', $monitor->uptime_30d);
        $this->assertNotNull($monitor->last_checked_at);
        $this->assertDatabaseHas('ping_logs', [
            'monitor_id' => $monitor->id,
            'status' => 'Up',
            'response_time_ms' => 125,
            'http_status' => 204,
        ]);
        Notification::assertNothingSent();
    }

    public function test_initial_failure_notifies_and_stores_safe_failure_context(): void
    {
        Notification::fake();
        $monitor = Monitor::factory()->create([
            'last_checked_at' => null,
            'status' => 'Up',
        ]);

        $this->runWith($monitor, ProbeResult::failure(5100, 'timeout', 'The request timed out.'));

        $monitor->refresh();
        $this->assertSame('Down', $monitor->status);
        $this->assertSame('timeout', $monitor->failure_code);
        $this->assertSame('The request timed out.', $monitor->failure_detail);
        Notification::assertSentTo($monitor->user, MonitorStatusChanged::class);
    }

    public function test_transition_recalculates_uptime_from_only_the_last_thirty_days(): void
    {
        Notification::fake();
        $monitor = Monitor::factory()->create([
            'last_checked_at' => now()->subMinutes(5),
            'status' => 'Down',
        ]);
        $recentLog = new PingLog([
            'monitor_id' => $monitor->id,
            'status' => 'Down',
            'response_time_ms' => 500,
            'ssl_status' => 'Valid',
        ]);
        $recentLog->created_at = now()->subDay();
        $recentLog->save();

        $oldLog = new PingLog([
            'monitor_id' => $monitor->id,
            'status' => 'Down',
            'response_time_ms' => 500,
            'ssl_status' => 'Valid',
        ]);
        $oldLog->created_at = now()->subDays(31);
        $oldLog->save();

        $this->runWith($monitor, new ProbeResult('Up', 80, 200, 'Valid', now()->addMonth()));

        $this->assertSame('50.00', $monitor->fresh()->uptime_30d);
        Notification::assertSentTo(
            $monitor->user,
            MonitorStatusChanged::class,
            fn (MonitorStatusChanged $notification): bool => $notification->newStatus === 'Up',
        );
    }

    private function runWith(Monitor $monitor, ProbeResult $result): void
    {
        $probe = $this->mock(HttpMonitorProbe::class, function (MockInterface $mock) use ($monitor, $result): void {
            $mock->shouldReceive('run')->once()->with($monitor->url)->andReturn($result);
        });

        (new RunMonitorCheck($probe))->run($monitor);
    }
}
