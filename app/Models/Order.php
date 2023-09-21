<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public const ORDER_STATUS_NEW = 'new';
    public const ORDER_STATUS_PROCESSING = 'processing';
    public const ORDER_STATUS_IN_TRANSIT = 'in_transit';
    public const ORDER_STATUS_FULFILLED = 'fulfilled';
    public const ORDER_STATUS_FAILED = 'failed';

    public const PAYMENT_STATUS_AWAITING_PAYMENT = 'awaiting_payment';
    public const PAYMENT_STATUS_SUCCESS = 'payment_received';
    public const PAYMENT_STATUS_FAILED = 'payment_failed';
    public const PAYMENT_STATUS_REFUND = 'payment_refund';

    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'user_id',
        'num_of_boxes_shipped',
        'shipper',
        'shipping_fee',
        'tax_fee',
        'intermediary_fees',
        'tracking_type',
        'tracking_reference',
        'total_sales',
        'notes',
        'handled_by_agent_id',
        'invoice_id',
        'reference_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'shipping_fee' => 'float',
        'tax_fee' => 'float',
        'intermediary_fees' => 'float',
    ];

    public function setTotalPayableAttribute($totalSales)
    {
        $this->attributes['total_sales'] = $totalSales;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function handledByAgent()
    {
        return $this->belongsTo(\App\Nodels\User::class, 'handled_by_agent_user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany | OrderItem[]
     */
    public function orderItems()
    {
        return $this->hasMany(\App\Models\OrderItem::class, 'order_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function store()
    {
        return $this->belongsTo(\App\Models\Store::class);
    }
}
