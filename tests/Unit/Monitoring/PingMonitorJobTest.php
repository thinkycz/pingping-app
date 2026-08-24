<?php

namespace Tests\Unit\Monitoring;

use App\Jobs\PingMonitorJob;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use PHPUnit\Framework\TestCase;

class PingMonitorJobTest extends TestCase
{
    public function test_job_is_unique_per_monitor_and_prevents_overlap(): void
    {
        $job = new PingMonitorJob(42);

        $this->assertInstanceOf(ShouldBeUnique::class, $job);
        $this->assertSame('monitor:42', $job->uniqueId());
        $this->assertSame(600, $job->uniqueFor);
        $this->assertContainsOnlyInstancesOf(WithoutOverlapping::class, $job->middleware());
    }
}
