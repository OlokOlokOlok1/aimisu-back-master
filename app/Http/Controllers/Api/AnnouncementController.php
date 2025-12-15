<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::where('status', 'published')
            ->latest('published_at')
            ->paginate(10);

        return response()->json([
            'data' => $announcements,
        ]);
    }

    public function mySubmissions(Request $request)
    {
        $announcements = Announcement::where('created_by', $request->user()->id)
            ->whereIn('status', ['draft', 'pending_approval', 'rejected'])
            ->latest('created_at')
            ->paginate(10);

        return response()->json([
            'data' => $announcements,
            'org_name' => $request->user()->organization->name ?? 'Your Organization',
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'published_at' => 'required|date_format:Y-m-d',
        ]);

        $data['created_by'] = $request->user()->id;
        $data['organization_id'] = $request->user()->organization_id;
        $data['status'] = 'pending_approval';

        $announcement = Announcement::create($data);

        return response()->json([
            'message' => 'Announcement created',
            'data'    => $announcement->load('organization', 'createdBy'),
        ], 201);
    }

    public function show(Announcement $announcement)
    {
        if ($announcement->status !== 'published' && $announcement->created_by !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return $announcement->load('organization', 'createdBy');
    }

    public function update(Request $request, Announcement $announcement)
    {
        if ($announcement->created_by !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (in_array($announcement->status, ['published'], true)) {
            return response()->json(['message' => 'Cannot edit published announcements'], 400);
        }

        $validator = Validator::make($request->all(), [
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'published_at' => 'required|date',
        ]);

        if ($validator->fails()) {
            Log::error('Announcement update validation failed', $validator->errors()->toArray());
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        if ($announcement->status === 'rejected') {
            $data['status'] = 'pending_approval';
            $data['rejection_reason'] = null;
        } else {
            $data['status'] = 'draft';
        }

        $announcement->update($data);

        return response()->json([
            'message' => 'Announcement updated',
            'data'    => $announcement->fresh()->load('organization', 'createdBy'),
        ]);
    }

    public function destroy(Request $request, Announcement $announcement)
    {
        if ($announcement->created_by !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!in_array($announcement->status, ['draft', 'rejected', 'pending_approval'], true)) {
            return response()->json([
                'message' => 'Can only delete draft, rejected, or pending announcements',
            ], 400);
        }

        $announcement->delete();

        return response()->json(['message' => 'Announcement deleted']);
    }

    public function submitForApproval(Request $request, Announcement $announcement)
    {
        if ($announcement->created_by !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!in_array($announcement->status, ['pending_approval', 'draft'], true)) {
            return response()->json(['message' => 'Announcement cannot be submitted'], 400);
        }

        $announcement->update(['status' => 'pending_approval']);

        return response()->json([
            'message' => 'Announcement submitted for approval',
            'data'    => $announcement->fresh()->load('organization', 'createdBy'),
        ]);
    }
}
