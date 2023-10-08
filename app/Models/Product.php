<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Store;
use App\Models\OrderItem;

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
        'weight_value',
        'weight_unit',
        'notes',
        'active',
        'size',
        'made_from',
        'manufactured_date',
        'sku',
        'total_inventory_remaining',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'manufactured_date' => 'date',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function scopeGetActive($query)
    {
        /** @var \Illuminate\Database\Eloquent\Builder $query **/
        return $query->where('active', '=', true);
    }

    public function scopeHasEnoughInventory($query)
    {
        /** @var \Illuminate\Database\Eloquent\Builder $query **/
        return $query->where('total_inventory_remaining', '>', '0');
    }

    public function scopeGetProductsBasedOnStore($query)
    {
        if (admin_all_access()) {
            return $query;
        }

        return $query->where('store_id', '=', get_store_id());
    }

    public function orders()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by', 'id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by', 'id');
    }

    public function images()
    {
        return $this->hasMany(\App\Models\ProductImage::class);
    }
}
