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
            $productId = $request->request->get('product') ?: null;
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

                $productsPayable = $this->getTotalPayable($items, $priceBasedOn);

                $order->total_payable = ($productsPayable + $order->shipping_fee)
                    - ($order->tax_fee + $order->intermediary_fees);

                $order->save();
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
    }

    private function getTotalPayable(array $orderItems, string $priceBasedOn)
    {
        if (!count($orderItems)) {
            return 0;
        }

        $orderItems = collect($orderItems);
        $productIds = Arr::pluck($orderItems, 'product_id');

        return Product::whereIn('id', array_values($productIds))->get()->map(function ($product, $key) use ($orderItems, $priceBasedOn) {
            $quantity = $orderItems->where('product_id', $product->id)->first()['quantity'] ?? 1;
            $product->ordered_quantity = $quantity;
            $product->total_quantity_price = $product->$priceBasedOn * $quantity;

            return $product;
        })->sum('total_quantity_price');
    }
}
