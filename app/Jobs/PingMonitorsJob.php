<?php

namespace App\Jobs;

use App\Models\Monitor;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PingMonitorsJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $uniqueFor = 55;

    public function uniqueId(): string
    {
        return 'ping-monitors';
    }

    public function handle(): void
    {
        Monitor::query()
            ->where('is_active', true)
            ->chunkById(250, function ($monitors): void {
                foreach ($monitors as $monitor) {
                    $dueAt = $monitor->last_checked_at?->addMinutes($monitor->interval);

                    if ($dueAt === null || $dueAt->lte(now())) {
                        PingMonitorJob::dispatch($monitor->id);
                    }
                }
            });
    }
}
