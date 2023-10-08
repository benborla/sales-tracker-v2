<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Silvanite\Brandenburg\Traits\ValidatesPermissions;
use App\Permissions\GetPermissions;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        'App\Models\User' => 'App\Policies\UserPolicy',
        'App\Models\OrderItem' => 'App\Policies\OrderItemPolicy',
        'App\Models\Role' => 'App\Policies\RolePolicy',
        'App\Models\GroupTeam' => 'App\Policies\GroupTeamPolicy',
        'App\Models\Store' => 'App\Policies\StorePolicy',
        'App\Models\UserStore' => 'App\Policies\UserStorePolicy',
        'App\Models\GroupTeamMember' => 'App\Policies\GroupTeamMemberPolicy',
        'App\Models\Product' => 'App\Policies\ProductPolicy',
        'App\Models\ProductImage' => 'App\Policies\ProductImagePolicy',
        'App\Models\UserInformationPolicy' => 'App\Policies\UserInformationPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        // @INFO: Register your custom action for roles here
        collect(GetPermissions::all())->each(function ($permission) {
            Gate::define($permission, function ($user) use ($permission) {
                if ($this->nobodyHasAccess($permission)) {
                    return true;
                }

                return $user->hasRoleWithPermission($permission);
            });
        });
        $this->registerPolicies();
    }
}
