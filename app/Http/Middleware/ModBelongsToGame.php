<?php

namespace App\Http\Middleware;

use App\Models\Game;
use App\Models\Mod;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ModBelongsToGame extends Middleware
{
    public function handle($request, \Closure $next, ...$guards)
    {
        if (!$mod = $request->route()->parameter('mod')) {
            return $next($request);
        } elseif (!$game = $request->route()->parameter('game')) {
            return $next($request);
        }

        if(Mod::find($mod)->game_id !== intval($game)) {
            return response()->json(['error' => 'Not Found'], 404);
        }

        return $next($request);
    }
}
