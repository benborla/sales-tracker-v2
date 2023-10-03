<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Permissions\Admin;
use App\Models\Store;

class IsUserBelongsToATeam
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
        if (!is_main_store()) {
            $team = auth()->user()->getUserTeamByStoreId(get_store_id())
                ->first()
                ->only('name', 'group_teams_id', 'team_lead_user_id', 'store_id');

            $request->merge(['team' => $team]);
        }

        return $next($request);
    }
}