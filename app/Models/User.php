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

    public function information(): HasOne
    {
        return $this->hasOne(UserInformation::class);
    }
}
