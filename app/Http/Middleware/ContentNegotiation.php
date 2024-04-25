<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ContentNegotiation
{
    public function handle(Request $request, Closure $next)
    {
        // Set the default response content type to application/json
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
