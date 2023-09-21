<?php

namespace App\Observers;

use App\Models\OrderItem;
use Illuminate\Support\Arr;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;
use App\Models\Product;

class OrderItemObserver
{
    public function saving(OrderItem $orderItem)
    {
        $order = $orderItem->order;
        Nova::whenServing(function (NovaRequest $request) use ($order) {
            $productId = $request->request->get('product') ?: null;
            $quantity = $request->request->get('quantity') ?: 1;

            if (!is_null($productId)) {
                $orderItems = $order->orderItems->toArray();
                $orderItems[] = [
                    'product_id' => (int) $productId,
                    'quantity' => (int) $quantity
                ];

                $items = collect($orderItems)->groupBy('product_id')
                    ->map(function ($group) {
                        return [
                            'product_id' => $group[0]['product_id'],
                            'quantity' => $group->sum('quantity'),
                        ];
                    })->values()->all();

                $productsPayable = $this->getTotalPayable($items);

                $order->total_payable = ($productsPayable + $order->shipping_fee)
                    - ($order->tax_fee + $order->intermediary_fees);

                $order->save();
            }
        });
    }

    public function updating(OrderItem $orderItem)
    {
        $this->saving($orderItem);
    }

    public function deleted(OrderItem $orderItem)
    {
        // @INFO: Re-compute total payable
        $order = $orderItem->order;
        $orderItems = $order->orderItems->toArray();
        $productsPayable = $this->getTotalPayable($orderItems);
        $order->total_payable = ($productsPayable + $order->shipping_fee)
            - ($order->tax_fee + $order->intermediary_fees);

        $order->save();
    }

    private function getTotalPayable(array $orderItems)
    {
        if (!count($orderItems)) {
            return [];
        }

        $orderItems = collect($orderItems);
        $productIds = Arr::pluck($orderItems, 'product_id');

        return Product::whereIn('id', array_values($productIds))->get()->map(function ($product, $key) use ($orderItems) {
            $quantity = $orderItems->where('product_id', $product->id)->first()['quantity'] ?? 1;
            $product->ordered_quantity = $quantity;
            $product->total_quantity_price = $product->retail_price * $quantity;

            return $product;
        })->sum('total_quantity_price');
    }
}
