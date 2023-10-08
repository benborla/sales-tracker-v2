<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\UserInformation;

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
        if (!auth()->check()) {
            return $next($request);
        }
        /** @INFO: Ignore if the logged-in user is an admin **/
        if (admin_all_access()) {
            return $next($request);
        }

        /** @INFO: add team info **/
        if (!is_main_store() && is_staff()) {
            try {
                /** @var \App\Models\User $user **/
                $user = auth()->user();
                $team = $user->getUserTeamByStoreId(get_store_id())
                    ->firstOrFail()
                    ->only('name', 'group_teams_id', 'team_lead_user_id', 'store_id');

                $request->merge(['team' => $team]);

                return $next($request);
            } catch (\Exception) {
                auth()->logout();
                abort(403, 'Your account has no assigned team. Please contact your immediate supervisor.', [
                    'Refresh' => '3, url=' . $request->getHttpHost()
                ]);
            }
        }

        /** @INFO: proceed if customer **/
        if (is_customer()) {
            return $next($request);
        }

        /** @INFO: Redirect if user is unidentified **/
        abort(403, 'Who are you?', [
            'Refresh' => '3, url=' . $request->getHttpHost()
        ]);
    }
}
