<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;

class HandleDownloadables
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function handle(Request $request, Closure $next)
    {
        return $next($request);
        // $response = $next($request);
        // dd('here');

        // $response->headers->set('Access-Control-Allow-Origin', '*');
        // $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE');
        // $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With, Application');

        // return $response;
    }
}
