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
    }

    public function updated(OrderItem $orderItem)
    {
        deduct_from_product_inventory($orderItem);
    }

    public function saving(OrderItem $orderItem)
    {
        $order = $orderItem->order;
        Nova::whenServing(function (NovaRequest $request) use ($order) {

            $productId = $request->request->get('product_id') ?: null;
            $quantity = $request->request->get('quantity') ?: 1;
            $priceBasedOn = $request->request->get('price_based_on') ?: \App\Models\Order::PRICE_BASED_ON_RETAIL;

            if (!is_null($productId)) {
                $orderItems = $order->orderItems->toArray();
                $orderItems[] = [
                    'product_id' => (int) $productId,
                    'quantity' => (int) $quantity
                ];

                /** @INFO: Combine all the same products and sum up their total quantity **/
                $items = collect($orderItems)->groupBy('product_id')
                    ->map(function ($group) {
                        return [
                            'product_id' => $group[0]['product_id'],
                            'quantity' => $group->sum('quantity'),
                        ];
                    })->values()->all();

                $productsPayable = get_total_payable($items, $priceBasedOn);

                $order->total_sales = ($productsPayable + $order->shipping_fee)
                    - ($order->tax_fee + $order->intermediary_fees);

                $order->saveQuietly();
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
        /** @INFO: refresh current page **/
        /** @TODO: update this into Laravel code **/
        header('Location: '.$_SERVER['REQUEST_URI']);
    }
}
