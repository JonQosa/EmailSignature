<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */


     public function handle($request, Closure $next)
     {
         $response = $next($request);
 
         // Allow requests from all domains during development
         // Replace '*' with your actual frontend domain in production
        
 
         return $response;
     }

    }