<?php

namespace App\Monitoring;

use App\Models\Monitor;
use App\Notifications\MonitorStatusChanged;
use Illuminate\Support\Facades\DB;

class RunMonitorCheck
{
    public function __construct(private readonly HttpMonitorProbe $probe) {}

    public function run(Monitor $monitor): void
    {
        if (! $monitor->is_active) {
            return;
        }

        $result = $this->probe->run($monitor->url);
        $hadPreviousCheck = $monitor->last_checked_at !== null;
        $previousStatus = $monitor->status;

        DB::transaction(function () use ($monitor, $result): void {
            $monitor->pingLogs()->create([
                'status' => $result->status,
                'response_time_ms' => $result->responseTimeMs,
                'http_status' => $result->httpStatus,
                'ssl_status' => $result->sslStatus,
                'failure_code' => $result->failureCode,
                'failure_detail' => $result->failureDetail,
            ]);

            $statistics = $monitor->pingLogs()
                ->where('created_at', '>=', now()->subDays(30))
                ->selectRaw(
                    'COUNT(*) AS total_count, SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) AS up_count',
                    ['Up'],
                )
                ->first();

            $total = (int) ($statistics?->total_count ?? 0);
            $up = (int) ($statistics?->up_count ?? 0);

            $monitor->update([
                'status' => $result->status,
                'ssl_status' => $result->sslStatus,
                'ssl_expiration_date' => $result->sslExpirationDate,
                'response_time_ms' => $result->responseTimeMs,
                'last_http_status' => $result->httpStatus,
                'failure_code' => $result->failureCode,
                'failure_detail' => $result->failureDetail,
                'last_checked_at' => now(),
                'uptime_30d' => $total > 0 ? round(($up / $total) * 100, 2) : 100,
            ]);
        });

        $isInitialFailure = ! $hadPreviousCheck && $result->status === 'Down';
        $isTransition = $hadPreviousCheck && $previousStatus !== $result->status;

        if ($isInitialFailure || $isTransition) {
            $monitor->user->notify(new MonitorStatusChanged($monitor->fresh(), $result->status));
        }
    }
}
