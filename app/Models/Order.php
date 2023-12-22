<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @class Order
 * @property int $id
 * @property int $store_id
 * @property string $email
 * @property int $user_id
 * @property int $num_of_boxes_shipped
 * @property string $shipper
 * @property float $shipping_fee
 * @property string $item_type
 * @property float $tax_fee
 * @property float $intermediary_fees
 * @property string $tracking_type
 * @property string $tracking_reference
 * @property float $total_sales
 * @property string $notes
 * @property int $invoice_id
 * @property int $reference_id
 * @property string $sales_channel
 * @property int $created_by
 * @property int $updated_by
 * @property bool $is_approved
 * @property string $price_based_on
 * @property string $payment_payload
 * @property string $order_status
 * @property string $payment_status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\User $user
 * @property-read \App\Models\User $orderCreatedBy
 * @property-read \App\Models\User $orderUpdatedBy
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OrderItem[] $orderItems
 * @property-read \App\Models\Store $store
 */
class Order extends Model
{
    public const ORDER_STATUS_NEW = 'new';
    public const ORDER_STATUS_PROCESSING = 'processing';
    public const ORDER_STATUS_IN_TRANSIT = 'in_transit';
    public const ORDER_STATUS_FULFILLED = 'fulfilled';
    public const ORDER_STATUS_FAILED = 'failed';
    public const ORDER_STATUS_BLACKLIST = 'blacklist';

    public const PAYMENT_STATUS_AWAITING_PAYMENT = 'awaiting_payment';
    public const PAYMENT_STATUS_SUCCESS = 'payment_received';
    public const PAYMENT_STATUS_FAILED = 'payment_failed';
    public const PAYMENT_STATUS_REFUND = 'payment_refund';

    public const PRICE_BASED_ON_RETAIL = 'retail_price';
    public const PRICE_BASED_ON_RESELLER = 'reseller_price';

    public const ORDER_SALES_CHANNEL_OFFICE = 'office';
    public const ORDER_SALES_CHANNEL_AMAZON = 'amazon';
    public const ORDER_SALES_CHANNEL_EBAY = 'ebay';

    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'store_id',
        'email',
        'user_id',
        'num_of_boxes_shipped',
        'shipper',
        'shipping_fee',
        'item_type',
        'tax_fee',
        'intermediary_fees',
        'tracking_type',
        'tracking_reference',
        'total_sales',
        'notes',
        'invoice_id',
        'reference_id',
        'sales_channel',
        'created_by',
        'updated_by',
        'is_approved',
        'price_based_on',
        'payment_payload',
        'order_status',
        'payment_status',
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
        'total_sales' => 'float'
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

    public function orderCreatedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by', 'id');
    }

    public function orderUpdatedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by', 'id');
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

    public function getIsOrderApprovedAttribute()
    {
        return $this->is_approved ? 'yes' : 'no';
    }

    public function getProductPayableAttribute()
    {
        return $this->orderItems->sum('total_price');
    }

    public function saveQuietly(array $options = [])
    {
        return static::withoutEvents(function () use ($options) {
            return $this->save($options);
        });
    }

    public static function orderStatuses(): array
    {
        return [
            self::ORDER_STATUS_NEW,
            self::ORDER_STATUS_PROCESSING,
            self::ORDER_STATUS_IN_TRANSIT,
            self::ORDER_STATUS_FULFILLED,
            self::ORDER_STATUS_FAILED,
            self::ORDER_STATUS_BLACKLIST
        ];
    }

    public static function paymentStatuses(): array
    {
        return [
            self::PAYMENT_STATUS_AWAITING_PAYMENT,
            self::PAYMENT_STATUS_SUCCESS,
            self::PAYMENT_STATUS_FAILED,
            self::PAYMENT_STATUS_REFUND
        ];
    }
}
