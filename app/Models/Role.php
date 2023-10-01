<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Silvanite\Brandenburg\Role as BaseRole;
use App\Models\Store;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Role extends BaseRole
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'slug',
        'name',
        'permissions',
        'store_id',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($role) {
            /** @var \App\Models\Store $store **/
            $store = request()->get('store');

            if (! $store instanceof Store) {
                return;
            }

            $role->store_id = $store->id;
        });
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
