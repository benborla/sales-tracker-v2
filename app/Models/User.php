<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\UserStore;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Store;
use App\Models\UserInformation;
use Silvanite\Brandenburg\Traits\HasRoles;
use App\Models\GroupTeamMember;

/**
 * @class User
 * @method getUserTeamByStoreId(int $storeId, $userId = null)
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stores(): HasMany
    {
        return $this->hasMany(UserStore::class);
    }

    public function scopeWithStore($query, Store $store, int $userId)
    {
        return $query->whereHas('stores', function ($q) use ($store, $userId) {
            $q->where('store_id', '=', $store->id);
            $q->where('user_id', '=', $userId);
        })->with(['stores']);
    }

    public function scopeGetUsersInStore($query, Store $store)
    {
        return $query->whereHas('stores', function ($q) use ($store) {
            $q->where('store_id', '=', $store->id);
        })->with(['stores']);
    }

    /**
     * Returns the available stores for the user, if no user id is provided
     * it will return the currently logged-in user available stores
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param ?int $userId
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetStores($query, int $userId = null)
    {
        $userId = $userId ?? auth()->user()->id;

        /** @var \Illuminate\Database\Eloquent\Builder $query **/
        return $query->join('user_stores', 'user_stores.user_id', '=', 'users.id')
            ->join('stores', 'stores.id', '=', 'user_stores.store_id')
            ->where('user_stores.user_id', $userId)
            ->get();
    }

    /**
     * Returns the users based on role
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param string $role
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetUserByRole($query, string $role)
    {
        return $query->join('role_user AS ru', 'ru.user_id', '=', 'users.id')
            ->join('roles AS r', 'r.id', '=', 'ru.role_id')
            ->join('user_information AS ui', 'ui.user_id', '=', 'users.id')
            ->where('r.name', $role)
            ->where('ui.is_active', '=', true);
    }

    /**
     * Returns the users that has a Team Leader role
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetTeamLeads($query)
    {
        return $this->scopeGetUserByRole($query, 'Team Leader');
    }

    public function scopeGetUsersByType($query, string $type, bool $activeOnly = true)
    {
        /** @var \Illuminate\Database\Eloquent\Builder $query **/
        return $query
            ->select('users.*')
            ->join('user_information', 'user_information.user_id', '=', 'users.id')
            ->where('user_information.is_active', '=', $activeOnly)
            ->where('user_information.type', '=', $type);
    }

    public function scopeGetCustomers($query, bool $activeOnly = true)
    {
        return $this->scopeGetUsersByType($query, UserInformation::USER_TYPE_CUSTOMER, $activeOnly);
    }

    public function scopeGetStaffs($query, bool $activeOnly = true)
    {
        return $this->scopeGetUsersByType($query, UserInformation::USER_TYPE_STAFF, $activeOnly);
    }

    public function scopeGetTeam($query)
    {
        /** @var \Illuminate\Database\Query\Builder $query **/
        return $query
            ->leftJoin('group_team_members', 'group_team_members.user_id', '=', 'users.id')
            ->leftJoin('group_teams', 'group_teams.id', '=', 'group_team_members.group_teams_id');
    }

    public function scopeGetUserTeamByStoreId($query, int $storeId, $userId = null)
    {
        $userId = $userId ?: auth()->user()->id;

        return $this->scopeGetTeam($query)
            ->where('group_teams.store_id', '=', $storeId)
            ->where('users.id', '=', $userId);
    }

    public function customers()
    {
        return $this->information->whereHas('user_information', function ($query) {
            $query->where('type', '=', UserInformation::USER_TYPE_CUSTOMER);
        })->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOneOrMany
     */
    public function information(): hasOne
    {
        return $this->hasOne(UserInformation::class);
    }

    /**
     * return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function teams(): HasMany
    {
        return $this->hasMany(GroupTeamMember::class);
    }
}
