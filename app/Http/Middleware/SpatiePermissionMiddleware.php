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
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if(auth()->guest()){
            abort(401, 'Unauthorized access.');
        }

        $permission = $request->route()->getName();

        if(!$permission || !auth()->user()->hasPermissionTo($permission)){
            abort(403, 'Forbidden access.');
        }

        return $next($request);
    }
}
