<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function pendingApprovals()
    {
        return response()->json([
            'events'        => Event::where('status', 'pending_approval')->get(),
            'announcements' => Announcement::where('status', 'pending_approval')->get(),
        ]);
    }

    public function checkConflicts(Request $request)
    {
        $data = $request->validate([
            'location_id' => 'required|exists:locations,id',
            'start_date'  => 'required|date_format:Y-m-d',
            'end_date'    => 'required|date_format:Y-m-d',
        ]);

        $events = Event::where('status', 'published')
            ->where('location_id', $data['location_id'])
            ->whereDate('start_date', '<=', $data['end_date'])
            ->whereDate('end_date', '>=', $data['start_date'])
            ->get();

        $conflicts = $events->map(function ($e) {
            return [
                'id'       => $e->id,
                'title'    => $e->title,
                'start'    => $e->start_date,
                'end'      => $e->end_date,
                'location' => $e->location->name ?? 'Unknown',
            ];
        })->values()->all();

        return response()->json([
            'has_conflicts' => !empty($conflicts),
            'conflicts'     => $conflicts,
        ]);
    }

    public function approveEvent(Event $event)
    {
        $events = Event::where('status', 'published')
            ->where('location_id', $event->location_id)
            ->where('id', '!=', $event->id)
            ->whereDate('start_date', '<=', $event->end_date)
            ->whereDate('end_date', '>=', $event->start_date)
            ->get();

        $conflicts = $events->map(function ($e) {
            return [
                'id'       => $e->id,
                'title'    => $e->title,
                'start'    => $e->start_date,
                'end'      => $e->end_date,
                'location' => $e->location->name ?? 'Unknown',
            ];
        })->values()->all();

        if (!empty($conflicts)) {
            return response()->json([
                'message'   => 'Cannot approve - schedule conflicts detected',
                'conflicts' => $conflicts,
            ], 422);
        }

        $event->update([
            'status'       => 'published',
            'published_at' => now(),
        ]);

        return response()->json(['message' => 'Approved']);
    }

    public function rejectEvent(Request $request, Event $event)
    {
        $event->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->input('rejection_reason'),
        ]);

        return response()->json(['message' => 'Rejected']);
    }

    public function approveAnnouncement(Announcement $announcement)
    {
        $announcement->update([
            'status'       => 'published',
            'published_at' => now(),
        ]);

        return response()->json(['message' => 'Approved']);
    }

    public function rejectAnnouncement(Request $request, Announcement $announcement)
    {
        $announcement->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->input('rejection_reason'),
        ]);

        return response()->json(['message' => 'Rejected']);
    }
}
