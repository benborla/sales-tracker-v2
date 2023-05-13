<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsUserBelongsToStore
{
    private const ENDPOINT_LOGIN = '/login';
    private const MAIN_SALES_TRACKER_ACCESS = 'mainSalesTracker';

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

        // @INFO: if store is equal to null, we assume that the user is accessing
        // the main sales tracker, now we need to check if the user is allowed to 
        // access the main sales tracker
        if (auth()->user()->hasRoleWithPermission(self::MAIN_SALES_TRACKER_ACCESS)) {
            return $next($request);
        }

        if (is_null($store)) {
            abort(403);
        }

        // @INFO: Check whether the user is allowed to access the store
        if (auth()->check() &&
            auth()->user()->query()->withStore($store, auth()->user()->id)->get()->count()
        ) {
            return $next($request);
        }

        auth()->logout();

        return redirect('/')
            ->with('redirectMessage', 'Redirecting...')
            ->with('redirectStatusCode', 403)
            ->with('redirectDelay', 2);
    }
}
