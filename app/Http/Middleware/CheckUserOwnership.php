<?php
namespace App\Http\Middleware;

use Closure;

class CheckUserOwnership
{
    private const ADMIN_ID = 1;
    public function handle($request, Closure $next)
    {
        $requestedUserId = $request->route('userId'); 
        $authenticatedUserId = $request->user()->id;
        
        if ($requestedUserId !== self::ADMIN_ID && $requestedUserId != $authenticatedUserId) {
            return response()->json(['error' => 'Unauthorized. You can only update your own information.'], 403);
        }

        return $next($request);
    }
}
