<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SpatiePermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Usage:
     *   'check.permission'              → auth-only (any authenticated user)
     *   'check.permission:manage_election' → auth + must have that permission
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission = null): Response
    {
        if (auth()->guest()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        if ($permission !== null && !auth()->user()->can($permission)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden. You do not have the required permission.'], 403);
            }
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
