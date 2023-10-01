<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Permissions\Admin;
use App\Models\Store;

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

        // @INFO: if store is equal to null, we assume that the user is accessing
        // the main sales tracker, now we need to check if the user is allowed to
        // access the main sales tracker
        if (auth()->user()->hasRoleWithPermission(Admin::ADMIN_CAN_ACCESS_SALES_TRACKER->value)) {
            return $next($request);
        }

        if (! $store instanceof Store) {
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
