<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->user()) {
            return response()->json(['error' => 'Unauthorized. Please log in.'], 401);
        }

        if (! $request->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized. Only admins can access this resource.'], 403);
        }

        return $next($request);
    }
}
