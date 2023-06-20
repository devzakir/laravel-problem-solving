<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Closure;

class RecipienterAuthenticate
{
    public function handle($request, Closure $next)
    {
        if (!Auth::guard('recipienter')->check()) {
            return response()->json([
              'flag' => false
            ], 401);
        }

        return $next($request);
    }
}
