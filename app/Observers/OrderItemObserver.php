<?php

namespace App\Observers;

use App\Models\OrderItem;
use Illuminate\Support\Arr;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;
use App\Models\Product;

class OrderItemObserver
{
    public function created(OrderItem $orderItem)
    {
        deduct_from_product_inventory($orderItem);
        update_total_payable($orderItem->order);
    }

    public function updated(OrderItem $orderItem)
    {
        deduct_from_product_inventory($orderItem);
        update_total_payable($orderItem->order);
    }

    public function saving(OrderItem $orderItem)
    {
        $order = $orderItem->order;
        Nova::whenServing(function (NovaRequest $request) use ($order) {
            if (! $this->isActionAllowed($request)) {
                return;
            }

            $productId = $request->request->get('product') ?: null;

            if (!is_null($productId)) {
                update_total_payable($order);
            }
        });
    }

    public function updating(OrderItem $orderItem)
    {
        // @INFO: Put back the product quantity back to products
        // for re-computation
        $product = $orderItem->product;
        $product->total_inventory_remaining += $orderItem->quantity;
        $product->save();

        // @INFO: Re-compute
        $this->saving($orderItem);
    }

    public function deleted(OrderItem $orderItem)
    {
        // @INFO: Put back the quantity of the product
        add_to_product_inventory($orderItem);
        update_total_payable($orderItem->order);
    }

    private function isActionAllowed(NovaRequest $request)
    {
        return in_array($request->method(), ['PUT']);
    }
}
