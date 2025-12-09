<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }

        return $query->with(['department', 'organization'])->paginate(10);
    }

    public function show(User $user)
    {
        return $user->load(['department', 'organization']);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'role' => 'sometimes|in:admin,org_admin,user',
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update($validated);

        return $user->load(['department', 'organization']);
    }
}
