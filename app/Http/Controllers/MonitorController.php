<?php

namespace App\Http\Controllers;

use App\Models\Monitor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MonitorController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $monitors = Monitor::where('user_id', $request->user()->id)
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('url', 'like', "%{$search}%")
                        ->orWhere('alias', 'like', "%{$search}%");
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Dashboard', [
            'monitors' => $monitors,
            'filters' => ['search' => $search],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required|url|max:255',
            'alias' => 'nullable|string|max:255',
            'interval' => 'required|integer|in:5,15,30,60',
        ]);

        $request->user()->monitors()->create([
            'url' => $validated['url'],
            'alias' => $validated['alias'],
            'interval' => $validated['interval'],
            'ssl_status' => 'None',
            'status' => 'Up',
            'uptime_percentage' => 100.00,
            'is_active' => true,
        ]);

        return redirect()->route('dashboard');
    }

    public function show(Monitor $monitor, Request $request)
    {
        if ($monitor->user_id !== $request->user()->id) {
            abort(403);
        }

        $thirtyDaysAgo = Carbon::now()->subDays(30);

        $totalLogs30d = $monitor->pingLogs()
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->count();

        $upLogs30d = $monitor->pingLogs()
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->where('status', 'Up')
            ->count();

        $uptime30d = $totalLogs30d > 0 ? round(($upLogs30d / $totalLogs30d) * 100, 2) : 100.00;

        $chartLogs = $monitor->pingLogs()
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->orderBy('created_at', 'asc')
            ->get(['created_at', 'response_time', 'status']);

        $chartData = $chartLogs->map(function ($log) {
            return [
                'x' => $log->created_at->format('Y-m-d H:i:s'),
                'y' => $log->response_time,
                'status' => $log->status
            ];
        });

        $recentLogs = $monitor->pingLogs()
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get(['id', 'status', 'response_time', 'ssl_status', 'created_at'])
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'status' => $log->status,
                    'response_time' => $log->response_time,
                    'ssl_status' => $log->ssl_status,
                    'created_at' => $log->created_at->diffForHumans(),
                    'date' => $log->created_at->format('Y-m-d H:i:s'),
                ];
            });

        return Inertia::render('Monitor/Show', [
            'monitor' => $monitor,
            'uptime30d' => $uptime30d,
            'chartData' => $chartData,
            'recentLogs' => $recentLogs,
        ]);
    }

    public function update(Request $request, Monitor $monitor)
    {
        if ($monitor->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'url' => 'required|url|max:255',
            'alias' => 'nullable|string|max:255',
            'interval' => 'required|integer|in:5,15,30,60',
        ]);

        $monitor->update($validated);

        return redirect()->back();
    }

    public function destroy(Monitor $monitor, Request $request)
    {
        if ($monitor->user_id !== $request->user()->id) {
            abort(403);
        }

        $monitor->delete();

        return redirect()->route('dashboard');
    }

    public function toggle(Monitor $monitor, Request $request)
    {
        if ($monitor->user_id !== $request->user()->id) {
            abort(403);
        }

        $monitor->update([
            'is_active' => !$monitor->is_active,
        ]);

        return redirect()->back();
    }
}