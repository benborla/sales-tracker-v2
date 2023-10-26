<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInformation extends Model
{
    use HasFactory;

    public const USER_TYPE_CUSTOMER = 'customer';
    public const USER_TYPE_STAFF = 'staff';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'first_name',
        'middle_name',
        'last_name',
        'shipping_address',
        'shipping_address_city',
        'shipping_address_state',
        'shipping_address_zipcode',
        'shipping_address_country',
        'billing_address',
        'billing_address_city',
        'billing_address_state',
        'billing_address_zipcode',
        'billing_address_country',
        'credit_card_type',
        'credit_card_number',
        'credit_card_expiration_date',
        'credit_card_cvv'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'date',
        'updated_at' => 'date',
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function customers()
    {
        return $this->query()->where('type', '=', self::USER_TYPE_CUSTOMER);
    }

    /**
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return $this->attributes['first_name'] . ' ' . $this->attributes['last_name'];
    }

    private function selectedAddress(string $addressType = 'shipping')
    {
        return $this->attributes[$addressType . '_address'] .
            $this->attributes[$addressType . '_address_city'] . ', ' .
            $this->attributes[$addressType . '_address_state'] . ', ' .
            $this->attributes[$addressType . '_address_zipcode'] . ' ' .
            $this->attributes[$addressType . '_address_country'];
    }

    public function getShippingAddressInfoAttribute(): string
    {
        return $this->selectedAddress();
    }

    public function getBillingAddressInfoAttribute(): string
    {
        return $this->selectedAddress('billing');
    }
}
