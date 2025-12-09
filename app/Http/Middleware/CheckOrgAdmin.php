<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckOrgAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()->role !== 'org_admin' && $request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized - Org Admin only'], 403);
        }

        return $next($request);
    }
}
