<?php

namespace App\Jobs;

use App\Models\Monitor;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PingMonitorsJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $monitors = Monitor::where('is_active', true)->get();

        foreach ($monitors as $monitor) {
            $lastChecked = $monitor->last_checked_at;
            $intervalMinutes = $monitor->interval;

            // Allow a small grace period (e.g., 30 seconds) to account for slight delays in queue processing
            if (!$lastChecked || now()->diffInSeconds($lastChecked) >= ($intervalMinutes * 60) - 30) {
                PingMonitorJob::dispatch($monitor);
            }
        }
    }
}