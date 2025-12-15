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
            'events'       => Event::where('status', 'pending_approval')->get(),
            'announcements'=> Announcement::where('status', 'pending_approval')->get(),
        ]);
    }

    public function approveEvent(Event $event)
    {
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
