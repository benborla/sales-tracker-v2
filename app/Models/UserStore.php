<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserStore extends Model
{
    use HasFactory;

    protected $with = ['user', 'store'];

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'store_id',
    ];

    /**
     * Retrieve the user model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Retrieve the store model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Retrieve the stores model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function retrieveStores()
    {
        return $this->belongsToMany(Store::class);
    }
}
