<?php

namespace App\Http\Controllers;

use App\Models\Monitor;
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
            ->latest('id')
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
        ]);

        $request->user()->monitors()->create([
            'url' => $validated['url'],
            'alias' => $validated['alias'],
            'ssl_status' => 'None',
            'status' => 'Up',
            'uptime_percentage' => 100.00,
            'is_active' => true,
        ]);

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
