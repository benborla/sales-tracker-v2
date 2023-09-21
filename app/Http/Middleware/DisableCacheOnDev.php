<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DisableCacheOnDev
{
    private const APP_DEV = 'local';

    public function handle(Request $request, Closure $next)
    {
        if (!config()->get('env') === self::APP_DEV) {
            $next($request);
        }

        $response = $next($request);
        $response->header('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->header('Pragma', 'no-cache');
        $response->header('Expires', '0');

        return $response;
    }
}
