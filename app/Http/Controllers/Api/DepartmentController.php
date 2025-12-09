<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->integer('per_page', 10);

        $paginator = Department::orderBy('name')->paginate($perPage);

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
            'name' => 'required|unique:departments',
            'code' => 'required|unique:departments',
            'logo_url' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        return Department::create($validated);
    }

    public function show(Department $department)
    {
        return $department;
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'sometimes|unique:departments,name,' . $department->id,
            'code' => 'sometimes|unique:departments,code,' . $department->id,
            'logo_url' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $department->update($validated);

        return $department;
    }

    public function destroy(Department $department)
    {
        $department->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
