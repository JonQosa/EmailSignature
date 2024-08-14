<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CanUpdateUser
{
    public function handle($request, Closure $next)
    {
        $currentUser = Auth::user();
        $userId = $request->route('id');

        if (! $currentUser->isAdmin() && $currentUser->id != $userId) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}