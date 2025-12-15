<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Announcement;
use App\Models\EventRegistration;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function adminDashboard()
    {
        $today = Carbon::today();
        $weekEnd = $today;
        $weekStart = $today->copy()->subDays(30);

        $eventsToday = Event::published()
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->count();

        $eventsThisWeek = Event::published()
            ->whereDate('start_date', '<=', $weekEnd)
            ->whereDate('end_date', '>=', $weekStart)
            ->count();

        $pendingEvents = Event::where('status', 'pending_approval')->count();
        $pendingAnnouncements = Announcement::where('status', 'pending_approval')->count();
        $totalRegistrations = EventRegistration::count();

        $events = Event::published()
            ->withCount('registrations')
            ->orderBy('start_date', 'desc')
            ->get();

        $labels = $events->pluck('title')->toArray();
        $values = $events->pluck('registrations_count')->toArray();

        $weekEvents = Event::published()
            ->withCount('registrations')
            ->whereDate('start_date', '<=', $weekEnd)
            ->whereDate('end_date', '>=', $weekStart)
            ->orderBy('start_date')
            ->get(['id', 'title', 'description', 'start_date', 'end_date']);

        return response()->json([
            'events_today' => $eventsToday,
            'events_this_week' => $eventsThisWeek,
            'pending_events' => $pendingEvents,
            'pending_announcements' => $pendingAnnouncements,
            'total_registrations' => $totalRegistrations,
            'chart_labels' => $labels,
            'chart_values' => $values,
            'week_events' => $weekEvents,
        ]);
    }

    public function orgDashboard(Request $request)
    {
        $orgId = $request->user()->organization_id;
        $today = Carbon::today();
        $monthStart = $today->copy()->startOfMonth();
        $monthEnd = $today->copy()->endOfMonth();

        $upcomingEvents = Event::where('organization_id', $orgId)
            ->where('status', 'published')
            ->whereDate('start_date', '>=', $today)
            ->count();

        $monthlyRegistrations = EventRegistration::whereHas('event', function ($q) use ($orgId, $monthStart, $monthEnd) {
            $q->where('organization_id', $orgId)
                ->where('status', 'published')
                ->whereBetween('start_date', [$monthStart, $monthEnd]);
        })->count();

        $events = Event::where('organization_id', $orgId)
            ->where('status', 'published')
            ->withCount('registrations')
            ->orderBy('start_date', 'desc')
            ->get();

        $avgRegistrations = $events->isNotEmpty() ? round($events->avg('registrations_count')) : 0;

        $labels = $events->pluck('title')->toArray();
        $values = $events->pluck('registrations_count')->toArray();

        $weekEnd = $today;
        $weekStart = $today->copy()->subDays(30);

        $weekEvents = Event::where('organization_id', $orgId)
            ->where('status', 'published')
            ->withCount('registrations')
            ->whereDate('start_date', '<=', $weekEnd)
            ->whereDate('end_date', '>=', $weekStart)
            ->orderBy('start_date')
            ->get(['id', 'title', 'description', 'start_date', 'end_date']);

        $recentAnnouncements = Announcement::where('organization_id', $orgId)
            ->where('status', 'published')
            ->latest('published_at')
            ->take(5)
            ->get(['id', 'title', 'published_at']);

        return response()->json([
            'upcoming_events' => $upcomingEvents,
            'monthly_registrations' => $monthlyRegistrations,
            'avg_registrations' => $avgRegistrations,
            'chart_labels' => $labels,
            'chart_values' => $values,
            'week_events' => $weekEvents,
            'recent_announcements' => $recentAnnouncements,
        ]);
    }
}
