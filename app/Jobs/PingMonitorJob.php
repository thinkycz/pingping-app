<?php

namespace App\Jobs;

use App\Models\Monitor;
use App\Monitoring\RunMonitorCheck;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\WithoutOverlapping;

class PingMonitorJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $uniqueFor = 600;

    public function __construct(public int $monitorId) {}

    public function handle(RunMonitorCheck $runner): void
    {
        $monitor = Monitor::query()->with('user')->find($this->monitorId);

        if ($monitor === null || ! $monitor->is_active) {
            return;
        }

        $runner->run($monitor);
    }

    public function uniqueId(): string
    {
        return "monitor:{$this->monitorId}";
    }

    public function middleware(): array
    {
        return [
            (new WithoutOverlapping($this->uniqueId()))
                ->releaseAfter(30)
                ->expireAfter(120),
        ];
    }
}
