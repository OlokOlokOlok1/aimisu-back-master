<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->integer('per_page', 10);

        $paginator = Organization::with('department')
            ->orderBy('name')
            ->paginate($perPage);

        return response()->json([
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'last_page'    => $paginator->lastPage(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'code' => 'required|unique:organizations',
            'department_id' => 'required|exists:departments,id',
            'logo_url' => 'nullable|string',
            'description' => 'nullable|string',
            'head_user_id' => 'nullable|exists:users,id',
        ]);

        return Organization::create($validated);
    }

    public function show(Organization $organization)
    {
        return $organization->load('department');
    }

    public function update(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string',
            'logo_url' => 'nullable|string',
            'description' => 'nullable|string',
            'head_user_id' => 'nullable|exists:users,id',
        ]);

        $organization->update($validated);

        return $organization;
    }

    public function destroy(Organization $organization)
    {
        $organization->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
