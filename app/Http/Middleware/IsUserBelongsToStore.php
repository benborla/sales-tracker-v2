<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsUserBelongsToStore
{
    private const ENDPOINT_LOGIN = '/login';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // @INFO: Ignore login route
        if ($request->getRequestUri() === self::ENDPOINT_LOGIN) {
            return $next($request);
        }

        $store = $request->query->get('store') ?: null;

        if (is_null($store)) {
            abort(404);
        }

        if (auth()->check() &&
            $userStore = auth()->user()->query()->withStore($store)->first()
        ) {
            return $next($request);
        }

        abort(404);
    }
}
