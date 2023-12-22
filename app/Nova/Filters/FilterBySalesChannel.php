<?php

namespace App\Nova\Filters;

use App\Models\Order;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\BooleanFilter;

class FilterBySalesChannel extends BooleanFilter
{
    /**
     * @var string
     */
    public $name = 'Sales Channel';

    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'select-filter';

    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        if ($value) {
            return $query->where('sales_channel', $value);
        }

        return $query;
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        return [
            'Office' => Order::ORDER_SALES_CHANNEL_OFFICE,
            'Amazon' => Order::ORDER_SALES_CHANNEL_AMAZON,
            'eBay' => Order::ORDER_SALES_CHANNEL_EBAY
        ];
    }
}
