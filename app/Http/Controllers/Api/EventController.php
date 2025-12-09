<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    public function index()
    {
        return Event::where('status', 'published')
            ->with('organization', 'createdBy', 'location')
            ->latest('start_date')
            ->paginate(10);
    }

    public function mySubmissions(Request $request)
    {
        $events = Event::where('created_by', $request->user()->id)
            ->with('organization', 'createdBy', 'location')
            ->latest('created_at')
            ->paginate(10);

        return response()->json([
            'data' => $events,
            'org_name' => $request->user()->organization->name ?? 'Your Organization',
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'required|string',
            'category'       => 'required|string',
            'start_date'     => 'required|date_format:Y-m-d',
            'end_date'       => 'required|date_format:Y-m-d|after_or_equal:start_date',
            'daily_times'    => 'nullable|array',
            'location_id'    => 'nullable|exists:locations,id',
            'location_name'  => 'nullable|string',
        ]);

        $data['created_by'] = $request->user()->id;
        $data['organization_id'] = $request->user()->organization_id;
        $data['status'] = 'pending_approval';

        $event = Event::create($data);

        return response()->json([
            'message' => 'Event created',
            'data'    => $event->load('organization', 'createdBy', 'location'),
        ], 201);
    }

    public function show(Event $event)
    {
        if ($event->status !== 'published' && $event->created_by !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return $event->load('organization', 'createdBy', 'location');
    }

    public function update(Request $request, Event $event)
    {
        if ($event->created_by !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (in_array($event->status, ['published'], true)) {
            return response()->json(['message' => 'Cannot edit published events'], 400);
        }

        $validator = Validator::make($request->all(), [
            'title'          => 'required|string|max:255',
            'description'    => 'required|string',
            'category'       => 'required|string',
            'start_date'     => 'required|date',
            'end_date'       => 'required|date|after_or_equal:start_date',
            'daily_times'    => 'nullable|array',
            'location_id'    => 'nullable|exists:locations,id',
            'location_name'  => 'nullable|string',
        ]);

        if ($validator->fails()) {
            Log::error('Event update validation failed', $validator->errors()->toArray());
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        if ($event->status === 'rejected') {
            $data['status'] = 'pending_approval';
            $data['rejection_reason'] = null;
        } else {
            $data['status'] = 'draft';
        }

        $event->update($data);

        return response()->json([
            'message' => 'Event updated',
            'data'    => $event->fresh()->load('organization', 'createdBy', 'location'),
        ]);
    }

    public function destroy(Request $request, Event $event)
    {
        if ($event->created_by !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!in_array($event->status, ['draft', 'rejected', 'pending_approval'], true)) {
            return response()->json([
                'message' => 'Can only delete draft, rejected, or pending events',
            ], 400);
        }

        $event->delete();

        return response()->json(['message' => 'Event deleted']);
    }

    public function submitForApproval(Request $request, Event $event)
    {
        if ($event->created_by !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!in_array($event->status, ['pending_approval', 'draft'], true)) {
            return response()->json(['message' => 'Event cannot be submitted'], 400);
        }

        $event->update(['status' => 'pending_approval']);

        return response()->json([
            'message' => 'Event submitted for approval',
            'data'    => $event->fresh()->load('organization', 'createdBy', 'location'),
        ]);
    }
}
