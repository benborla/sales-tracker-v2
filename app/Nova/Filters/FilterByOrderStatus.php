<?php

namespace App\Nova\Filters;

use App\Models\Order;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\BooleanFilter;

class FilterByOrderStatus extends BooleanFilter
{
    /**
     * @var string
     */
    public $name = 'Order Status';

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
            return $query->where('order_status', $value);
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
            'New' => Order::ORDER_STATUS_NEW,
            'Processing' => Order::ORDER_STATUS_PROCESSING,
            'In Transit' => Order::ORDER_STATUS_IN_TRANSIT,
            'Fulfilled' => Order::ORDER_STATUS_FULFILLED,
            'Failed' => Order::ORDER_STATUS_FAILED,
        ];
    }
}
