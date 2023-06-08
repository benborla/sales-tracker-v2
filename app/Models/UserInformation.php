<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInformation extends Model
{
    use HasFactory;

    /**
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return $this->attributes['first_name'] . ' ' . $this->attributes['last_name'];
    }
}
