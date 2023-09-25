<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class OrderItem extends Model
{
    use HasFactory;

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [];

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'order_id',
        'quantity',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo;
     */
    public function order()
    {
        return $this->belongsTo(\App\Models\Order::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo;
     */
    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class);
    }

    /**
     * @return float
     */
    public function getTotalPriceAttribute(): float
    {
        return $this->product->retail_price * $this->quantity;
    }

    /**
     * @return string
     */
    public function getFormattedTotalPriceAttribute(): string
    {
        return '$ ' . number_format($this->total_price, 2);
    }

    public function getProductRemainingQuantityAttribute()
    {
        return $this->product->total_inventory_remaining ?? 0;
    }
}
