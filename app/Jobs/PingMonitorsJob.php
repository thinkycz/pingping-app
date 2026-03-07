<?php

namespace App\Jobs;

use App\Models\Monitor;
use App\Models\PingLog;
use App\Notifications\MonitorStatusChanged;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PingMonitorsJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $monitors = Monitor::where('is_active', true)->get();

        foreach ($monitors as $monitor) {
            $lastChecked = $monitor->last_checked_at;
            $intervalMinutes = $monitor->interval;

            if (!$lastChecked || now()->diffInMinutes($lastChecked) >= $intervalMinutes) {
                $this->ping($monitor);
            }
        }
    }

    protected function ping(Monitor $monitor)
    {
        $startTime = microtime(true);
        $status = 'Down';
        $sslStatus = 'None';
        $sslExpiration = null;
        $responseTime = null;
        $previousStatus = $monitor->status;

        try {
            $response = Http::timeout(10)->get($monitor->url);

            $endTime = microtime(true);
            $responseTime = ($endTime - $startTime) * 1000;

            if ($response->successful()) {
                $status = 'Up';
            }

            $urlParts = parse_url($monitor->url);
            if (isset($urlParts['scheme']) && $urlParts['scheme'] === 'https') {
                $host = $urlParts['host'];
                try {
                    $context = stream_context_create([
                        'ssl' => [
                            'capture_peer_cert' => true,
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                        ]
                    ]);
                    $client = stream_socket_client(
                        "ssl://{$host}:443",
                        $errorNumber,
                        $errorString,
                        5,
                        STREAM_CLIENT_CONNECT,
                        $context
                    );

                    if ($client) {
                        $params = stream_context_get_params($client);
                        if (isset($params['options']['ssl']['peer_certificate'])) {
                            $cert = openssl_x509_parse($params['options']['ssl']['peer_certificate']);
                            if ($cert && isset($cert['validTo_time_t'])) {
                                $sslExpiration = Carbon::createFromTimestamp($cert['validTo_time_t']);
                                $sslStatus = $sslExpiration->isPast() ? 'Invalid' : 'Valid';
                            }
                        }
                        fclose($client);
                    }
                } catch (\Exception $e) {
                    $sslStatus = 'Invalid';
                    Log::error("SSL check failed for {$monitor->url}: " . $e->getMessage());
                }
            }

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $responseTime = ($endTime - $startTime) * 1000;
            Log::error("Ping failed for {$monitor->url}: " . $e->getMessage());
        }

        PingLog::create([
            'monitor_id' => $monitor->id,
            'status' => $status,
            'response_time' => $responseTime,
            'ssl_status' => $sslStatus,
        ]);

        $totalLogs = PingLog::where('monitor_id', $monitor->id)->count();
        $upLogs = PingLog::where('monitor_id', $monitor->id)->where('status', 'Up')->count();
        $uptimePercentage = $totalLogs > 0 ? ($upLogs / $totalLogs) * 100 : 100.00;

        $monitor->update([
            'status' => $status,
            'ssl_status' => $sslStatus,
            'ssl_expiration_date' => $sslExpiration,
            'response_time' => $responseTime,
            'last_checked_at' => now(),
            'uptime_percentage' => $uptimePercentage,
        ]);

        if ($previousStatus !== $status && $previousStatus !== 'disabled') {
            $monitor->user->notify(new MonitorStatusChanged($monitor, $status));
        }
    }
}
