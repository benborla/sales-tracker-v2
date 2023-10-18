<?php

namespace App\Observers;

use App\Models\Order;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;

class OrderObserver
{
    public function creating(Order $order)
    {
        Nova::whenServing(function (NovaRequest $request) use ($order) {
            if ($this->isActionAllowed($request)) {
                $uniqueReference = strtoupper(substr(uniqid(date('mdHis')), 1, 16));
                $order->is_approved = i('can approve', Order::class);
                $order->created_by = auth()->user()->id;
                $order->updated_by = auth()->user()->id;
                $order->invoice_id = "INV-$uniqueReference";
                $order->total_sales = get_total_sales(
                    (array) $request->get('orderItems'),
                    (float) $request->get('shipping_fee'),
                    (float) $request->get('tax_fee'),
                    (float) $request->get('intermediary_fees'),
                    $request->get('price_based_on')
                );
                $order->payment_payload = '{}';

                if (!is_main_store()) {
                    /** @INFO: automatically fill if sites is in tenant mode **/
                    $order->store_id = get_store_id();
                }
            }
        });
    }

    public function updated(Order $order)
    {
        $order->updated_by = auth()->user()->id;

        update_total_payable($order);
    }

    private function isActionAllowed(NovaRequest $request)
    {
        return in_array($request->method(), ['PUT', 'POST']);
    }
}
