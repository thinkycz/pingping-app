<?php

namespace App\Http\Controllers;

use App\Http\Requests\MonitorRequest;
use App\Jobs\PingMonitorJob;
use App\Models\Monitor;
use App\Models\PingLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MonitorController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim((string) $request->input('search', ''));
        $status = in_array($request->input('status'), ['up', 'down', 'paused', 'pending'], true)
            ? $request->input('status')
            : 'all';

        $accountMonitors = $request->user()->monitors();
        $stats = [
            'total' => (clone $accountMonitors)->count(),
            'up' => (clone $accountMonitors)->where('is_active', true)->whereNotNull('last_checked_at')->where('status', 'Up')->count(),
            'down' => (clone $accountMonitors)->where('is_active', true)->whereNotNull('last_checked_at')->where('status', 'Down')->count(),
            'paused' => (clone $accountMonitors)->where('is_active', false)->count(),
            'pending' => (clone $accountMonitors)->where('is_active', true)->whereNull('last_checked_at')->count(),
        ];

        $monitors = $request->user()->monitors()
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('url', 'like', "%{$search}%")
                        ->orWhere('alias', 'like', "%{$search}%");
                });
            })
            ->when($status !== 'all', fn (Builder $query) => $this->applyStatusFilter($query, $status))
            ->latest('id')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Monitor $monitor): array => $this->summary($monitor));

        return Inertia::render('Dashboard', [
            'monitors' => $monitors,
            'stats' => $stats,
            'filters' => ['search' => $search, 'status' => $status],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Monitor/Create');
    }

    public function store(MonitorRequest $request): RedirectResponse
    {
        $monitor = $request->user()->monitors()->create([
            ...$request->validated(),
            'ssl_status' => 'None',
            'status' => 'Up',
            'uptime_30d' => 100,
            'is_active' => true,
        ]);

        PingMonitorJob::dispatch($monitor->id);

        return redirect()
            ->route('monitors.show', $monitor)
            ->with('success', __('Monitor created. The first check is queued.'));
    }

    public function show(Monitor $monitor): Response
    {
        $this->authorize('view', $monitor);

        $history = $monitor->pingLogs()
            ->where('created_at', '>=', now()->subDays(30))
            ->oldest('created_at')
            ->get(['created_at', 'response_time_ms', 'status'])
            ->map(fn (PingLog $log): array => [
                'checked_at' => $log->created_at->toIso8601String(),
                'response_time_ms' => $log->response_time_ms,
                'status' => strtolower($log->status),
            ]);

        $recentChecks = $monitor->pingLogs()
            ->latest('created_at')
            ->limit(50)
            ->get()
            ->map(fn (PingLog $log): array => [
                'id' => $log->id,
                'status' => strtolower($log->status),
                'response_time_ms' => $log->response_time_ms,
                'http_status' => $log->http_status,
                'ssl_status' => strtolower($log->ssl_status),
                'failure_code' => $log->failure_code,
                'failure_message' => $this->failureMessage($log->failure_code, $log->failure_detail),
                'checked_at' => $log->created_at->toIso8601String(),
            ]);

        return Inertia::render('Monitor/Show', [
            'monitor' => [
                ...$this->summary($monitor),
                'interval' => $monitor->interval,
                'http_status' => $monitor->last_http_status,
                'failure_code' => $monitor->failure_code,
                'failure_message' => $this->failureMessage($monitor->failure_code, $monitor->failure_detail),
                'ssl' => [
                    'status' => strtolower($monitor->ssl_status),
                    'expires_at' => $monitor->ssl_expiration_date?->toDateString(),
                ],
            ],
            'history' => $history,
            'recentChecks' => $recentChecks,
        ]);
    }

    public function update(MonitorRequest $request, Monitor $monitor): RedirectResponse
    {
        $this->authorize('update', $monitor);
        $validated = $request->validated();
        $targetChanged = $validated['url'] !== $monitor->url;

        $monitor->update([
            ...$validated,
            ...($targetChanged ? [
                'status' => 'Up',
                'uptime_30d' => 100,
                'response_time_ms' => null,
                'last_http_status' => null,
                'failure_code' => null,
                'failure_detail' => null,
                'last_checked_at' => null,
                'ssl_status' => 'None',
                'ssl_expiration_date' => null,
            ] : []),
        ]);

        if ($targetChanged && $monitor->is_active) {
            PingMonitorJob::dispatch($monitor->id);
        }

        return redirect()->back()->with('success', __('Monitor settings saved.'));
    }

    public function destroy(Monitor $monitor): RedirectResponse
    {
        $this->authorize('delete', $monitor);
        $monitor->delete();

        return redirect()->route('dashboard')->with('success', __('Monitor deleted.'));
    }

    public function toggle(Monitor $monitor): RedirectResponse
    {
        $this->authorize('update', $monitor);
        $monitor->update(['is_active' => ! $monitor->is_active]);

        if ($monitor->is_active) {
            PingMonitorJob::dispatch($monitor->id);
        }

        $message = $monitor->is_active ? __('Monitor resumed.') : __('Monitor paused.');

        return redirect()->back()->with('success', $message);
    }

    private function applyStatusFilter(Builder $query, string $status): Builder
    {
        return match ($status) {
            'paused' => $query->where('is_active', false),
            'pending' => $query->where('is_active', true)->whereNull('last_checked_at'),
            'up' => $query->where('is_active', true)->whereNotNull('last_checked_at')->where('status', 'Up'),
            'down' => $query->where('is_active', true)->whereNotNull('last_checked_at')->where('status', 'Down'),
        };
    }

    private function summary(Monitor $monitor): array
    {
        return [
            'id' => $monitor->id,
            'url' => $monitor->url,
            'alias' => $monitor->alias,
            'display_state' => $monitor->displayState(),
            'uptime_30d' => (float) $monitor->uptime_30d,
            'response_time_ms' => $monitor->response_time_ms,
            'last_checked_at' => $monitor->last_checked_at?->toIso8601String(),
            'is_active' => $monitor->is_active,
        ];
    }

    private function failureMessage(?string $code, ?string $detail): ?string
    {
        if ($code === null) {
            return null;
        }

        $key = "monitoring.failures.{$code}";
        $translated = __($key);

        return $translated === $key ? $detail : $translated;
    }
}
