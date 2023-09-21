<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Store;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'store_id',
        'name',
        'upc',
        'asin',
        'retail_price',
        'reseller_price',
        'product_image', // <-- image link url
        'weight_value',
        'weight_unit',
        'shipper',
        'shipping_fee',
        'tracking_number',
        'notes',
        'active'
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function scopeGetActive($query)
    {
        /** @var \Illuminate\Database\Eloquent\Builder $query **/
        $query->where('active', '=', true);
    }
}
