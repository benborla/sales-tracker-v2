<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Product;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;
use Illuminate\Support\Arr;

class OrderObserver
{
    public function saving(Order $order)
    {
        Nova::whenServing(function (NovaRequest $request) use ($order) {
            $form = $request->request->all();

            if (!isset($form['orderItems'])) {
                return;
            }

            $productsPayable = $this->getTotalPayable(
                $form['orderItems'],
                Arr::pluck($form['orderItems'], 'product')
            );

            $totalPayable = ($productsPayable + (float) $form['shipping_fee'])
                - (float) $form['tax_fee'] - (float) $form['intermediary_fees'];

            $order->total_payable = $totalPayable;
            $order->payment_payload = '{}';
        });
    }

    public function creating(Order $order)
    {
        $uniqueReference = strtoupper(substr(uniqid(date('mdHis')), 1, 16));
        //$order->is_approved = auth()->
        $order->created_by = auth()->user()->id;
        $order->updated_by = auth()->user()->id;
        $order->invoice_id = "INV-$uniqueReference";
   }

    public function updating(Order $order)
    {
        $order->updated_by = auth()->user()->id;
    }

    private function getTotalPayable(array $orderItems, array $productIds)
    {
        if (!count($productIds) || !count($orderItems)) {
            return [];
        }

        $orderItems = collect($orderItems);

        return Product::whereIn('id', array_values($productIds))->get()->map(function ($product, $key) use ($orderItems) {
            $quantity = $orderItems->where('product', $product->id)->first()['quantity'] ?? 1;
            $product->ordered_quantity = $quantity;
            $product->total_quantity_price = $product->retail_price * $quantity;

            return $product;
        })->sum('total_quantity_price');
    }
}
