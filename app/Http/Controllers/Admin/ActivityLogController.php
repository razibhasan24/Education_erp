<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with(['causer', 'subject']);

        if ($request->filled('log_name')) {
            $query->where('log_name', $request->log_name);
        }
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }
        if ($request->filled('causer_id')) {
            $query->where('causer_type', \App\Models\User::class)
                ->where('causer_id', $request->causer_id);
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $logs = $query->latest()->paginate(50);

        // ফিল্টার অপশন
        $logNames = Activity::select('log_name')->distinct()->pluck('log_name');
        $events = Activity::select('event')->distinct()->pluck('event');
        $users = \App\Models\User::orderBy('name')->get();

        $summary = [
            'total' => Activity::count(),
            'today' => Activity::whereDate('created_at', now()->toDateString())->count(),
            'this_week' => Activity::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
        ];

        return view('admin.activity-log.index', compact('logs', 'logNames', 'events', 'users', 'summary'));
    }

    public function show(Activity $activity)
    {
        $activity->load(['causer', 'subject']);
        return view('admin.activity-log.show', compact('activity'));
    }

    public function cleanup(Request $request)
    {
        $data = $request->validate([
            'days' => 'required|integer|min:1|max:3650',
        ]);

        $deleted = Activity::where('created_at', '<', now()->subDays($data['days']))->delete();

        return back()->with('success', "{$deleted}টি পুরোনো লগ মুছে ফেলা হয়েছে।");
    }

    /**
     * একটি নির্দিষ্ট subject এর সব activity
     */
    public function forSubject($type, $id)
    {
        $activities = Activity::where('subject_type', $type)
            ->where('subject_id', $id)
            ->with('causer')
            ->latest()
            ->paginate(30);

        return view('admin.activity-log.for-subject', compact('activities', 'type', 'id'));
    }
}