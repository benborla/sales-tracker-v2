<?php

use App\Models\Store;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Support\Arr;

if (!function_exists('is_main_store')) {
    function is_main_store()
    {
        if (request()->request->get('is_main_store')) {
            return true;
        }

        return false;
    }
}

if (!function_exists('get_store_id')) {
    function get_store_id()
    {
        $store = request()->request->get('store') ?? null;

        if ($store instanceof Store) {
            return $store->id;
        }
    }
}

if (!function_exists('update_total_payable')) {
    function update_total_payable(Order $order)
    {
        $orderItems = $order->orderItems->toArray();
        $productsPayable = getTotalPayable($orderItems);

        $order->total_sales = ($productsPayable + $order->shipping_fee)
            - ($order->tax_fee + $order->intermediary_fees);
        $order->save();
    }

    function getTotalPayable(array $orderItems)
    {
        if (!count($orderItems)) {
            return 0;
        }

        $orderItems = collect($orderItems);
        $productIds = Arr::pluck($orderItems, 'product_id');

        return Product::whereIn('id', array_values($productIds))->get()->map(function ($product, $key) use ($orderItems) {
            $quantity = $orderItems->where('product_id', $product->id)->first()['quantity'] ?? 1;
            $product->ordered_quantity = $quantity;
            $product->total_quantity_price = $product->retail_price * $quantity;

            return $product;
        })->sum('total_quantity_price') ?: 0;
    }
}

if (!function_exists('deduct_from_product_inventory')) {
    function deduct_from_product_inventory(OrderItem $orderItem)
    {
        $product = $orderItem->product;
        $product->total_inventory_remaining -= $orderItem->quantity;
        $product->save();
    }
}

if (!function_exists('add_to_product_inventory')) {
    function add_to_product_inventory(OrderItem $orderItem)
    {
        $product = $orderItem->product;
        $product->total_inventory_remaining += $orderItem->quantity;
        $product->save();
    }
}
