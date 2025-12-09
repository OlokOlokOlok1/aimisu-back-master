<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user');

        if ($request->search) {
            $search = "%{$request->search}%";
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'LIKE', $search);
            });
        }

        if ($request->action) {
            $query->where('action', $request->action);
        }

        if ($request->table) {
            $query->where('table_name', $request->table);
        }

        return $query->latest()->paginate(10);
    }
}
