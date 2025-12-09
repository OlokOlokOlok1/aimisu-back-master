<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Announcement;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function adminDashboard()
    {
        $today     = Carbon::today();
        $weekStart = $today->copy()->startOfWeek();
        $weekEnd   = $today->copy()->endOfWeek();

        $eventsToday = Event::where('status', 'published')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->count();

        $eventsThisWeek = Event::where('status', 'published')
            ->whereDate('start_date', '<=', $weekEnd)
            ->whereDate('end_date', '>=', $weekStart)
            ->count();

        $pendingEvents        = Event::where('status', 'pending_approval')->count();
        $pendingAnnouncements = Announcement::where('status', 'pending_approval')->count();

        $totalRegistrations = Event::where('status', 'published')->sum('registration');

        $recentEvents = Event::where('status', 'published')
            ->orderByDesc('start_date')
            ->take(12)
            ->get(['id', 'title', 'start_date', 'registration'])
            ->reverse()
            ->values();

        $recentRegistrations = $recentEvents->pluck('registration')->toArray();
        $labels              = $recentEvents->pluck('title')->toArray();

        // Moving average (3)
        $ma = null;
        if (count($recentRegistrations) >= 3) {
            $slice = array_slice($recentRegistrations, -3);
            $ma    = array_sum($slice) / 3;
        }

        // Exponential smoothing (alpha=0.5)
        $esForecast = null;
        if (!empty($recentRegistrations)) {
            $alpha = 0.5;
            $s     = $recentRegistrations[0];
            for ($i = 1; $i < count($recentRegistrations); $i++) {
                $s = $alpha * $recentRegistrations[$i] + (1 - $alpha) * $s;
            }
            $esForecast = $s;
        }

        // Week events for the admin calendar
        $weekEvents = Event::where('status', 'published')
            ->whereDate('start_date', '<=', $weekEnd)
            ->whereDate('end_date', '>=', $weekStart)
            ->orderBy('start_date')
            ->get([
                'id',
                'title',
                'description',
                'start_date',
                'end_date',
                'registration',
            ]);

        return response()->json([
            'events_today'          => $eventsToday,
            'events_this_week'      => $eventsThisWeek,
            'pending_events'        => $pendingEvents,
            'pending_announcements' => $pendingAnnouncements,

            'total_registrations'   => $totalRegistrations,
            'registration_labels'   => $labels,
            'registration_values'   => $recentRegistrations,
            'registration_ma_3'     => $ma,
            'registration_es'       => $esForecast,

            'week_events'           => $weekEvents,
        ]);
    }

    // orgDashboard unchanged
    public function orgDashboard(Request $request)
    {
        $user  = $request->user();
        $orgId = $user->organization_id;

        $today      = Carbon::today();
        $weekStart  = $today->copy()->startOfWeek();
        $weekEnd    = $today->copy()->endOfWeek();
        $monthStart = $today->copy()->startOfMonth();
        $monthEnd   = $today->copy()->endOfMonth();

        $eventBase = Event::where('organization_id', $orgId)
            ->where('status', 'published');

        $upcomingEvents = (clone $eventBase)
            ->whereDate('start_date', '>=', $today)
            ->count();

        $monthlyRegistrations = (clone $eventBase)
            ->whereBetween('start_date', [$monthStart, $monthEnd])
            ->sum('registration');

        $recentEvents = (clone $eventBase)
            ->orderByDesc('start_date')
            ->take(5)
            ->get(['id', 'title', 'start_date', 'registration'])
            ->values();

        $avgRegistrations = $recentEvents->count()
            ? round($recentEvents->avg('registration'))
            : 0;

        $weekEvents = (clone $eventBase)
            ->whereDate('start_date', '<=', $weekEnd)
            ->whereDate('end_date', '>=', $weekStart)
            ->orderBy('start_date')
            ->get([
                'id',
                'title',
                'description',
                'start_date',
                'end_date',
                'registration',
            ]);

        $recentAnnouncements = Announcement::where('organization_id', $orgId)
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->take(5)
            ->get(['id', 'title', 'published_at'])
            ->values();

        return response()->json([
            'upcoming_events'       => $upcomingEvents,
            'monthly_registrations' => $monthlyRegistrations,
            'avg_registrations'     => $avgRegistrations,
            'recent_events'         => $recentEvents,
            'recent_announcements'  => $recentAnnouncements,
            'week_events'           => $weekEvents,
        ]);
    }
}
