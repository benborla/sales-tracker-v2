<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\GroupTeam;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupTeamMember extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'group_teams_id',
        'user_id',
    ];

    /**
     * return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function groupTeam(): BelongsTo
    {
        return $this->belongsTo(GroupTeam::class, 'group_teams_id');
    }

    /**
     * return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
