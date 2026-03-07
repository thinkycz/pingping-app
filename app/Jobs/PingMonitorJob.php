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

class PingMonitorJob implements ShouldQueue
{
    use Queueable;

    public Monitor $monitor;

    /**
     * Create a new job instance.
     */
    public function __construct(Monitor $monitor)
    {
        $this->monitor = $monitor;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $startTime = microtime(true);
        $status = 'Down';
        $sslStatus = 'None';
        $sslExpiration = null;
        $responseTime = null;
        $previousStatus = $this->monitor->status;

        try {
            $response = Http::timeout(10)->get($this->monitor->url);

            $endTime = microtime(true);
            $responseTime = ($endTime - $startTime) * 1000;

            if ($response->successful()) {
                $status = 'Up';
            }

            $urlParts = parse_url($this->monitor->url);
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
                    Log::error("SSL check failed for {$this->monitor->url}: " . $e->getMessage());
                }
            }

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $responseTime = ($endTime - $startTime) * 1000;
            Log::error("Ping failed for {$this->monitor->url}: " . $e->getMessage());
        }

        PingLog::create([
            'monitor_id' => $this->monitor->id,
            'status' => $status,
            'response_time' => $responseTime,
            'ssl_status' => $sslStatus,
        ]);

        // Optimizing uptime percentage calculation
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        $totalLogs = PingLog::where('monitor_id', $this->monitor->id)
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->count();

        $upLogs = PingLog::where('monitor_id', $this->monitor->id)
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->where('status', 'Up')
            ->count();

        $uptimePercentage = $totalLogs > 0 ? ($upLogs / $totalLogs) * 100 : 100.00;

        $this->monitor->update([
            'status' => $status,
            'ssl_status' => $sslStatus,
            'ssl_expiration_date' => $sslExpiration,
            'response_time' => $responseTime,
            'last_checked_at' => now(),
            'uptime_percentage' => $uptimePercentage,
        ]);

        if ($previousStatus !== $status && $previousStatus !== 'disabled') {
            $this->monitor->user->notify(new MonitorStatusChanged($this->monitor, $status));
        }
    }
}