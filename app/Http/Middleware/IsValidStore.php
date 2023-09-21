<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Store;

class IsValidStore
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $subdomain = explode('.', $request->getHost());
        $subdomain = current($subdomain);

        if ($subdomain === config('app.main_store')) {
            $request->merge(['is_main_store' => true]);
            return $next($request);
        }

        $store = Store::query()->getStore($subdomain);

        // @INFO: throw 404 if store is not active
        if (! $store->is_active) {
            abort(404);
        }

        $request->merge(['store' => $store]);

        return match($subdomain) {
            $store->domain => $next($request),
            default => abort(404)
        };
    }
}
