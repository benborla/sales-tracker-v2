<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    public function scopeGetStore($query, string $storeName)
    {
        return $query->where('domain', '=', $storeName)->firstOrFail();
    }
}
