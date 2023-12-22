@php
/** @var \App\Models\Order $order **/
@endphp
<table>
    <thead style="background: orange;">
    <tr>
        <th>Store</th>
        <th>Invoice ID</th>
        <th>Reference ID</th>
        <th>Customer</th>
        <th>Email</th>
        <th>Billing Adddress</th>
        <th>Order Status</th>
        <th>Payment Status</th>
        <th>Orders</th>
        <th>Product Price</th>
        <th>Shiping Fee</th>
        <th>Tax</th>
        <th>Intermediary Fee</th>
        <th>Total</th>
        <th>Shipping Address</th> <!-- shipper -->
        <th>Shipping Method</th> <!-- shipper -->
        <th>Item Type</th>
        <th>Number of items to ship</th>
        <th>Tracking Reference</th>
        <th>Order Created By</th>
        <th>Order Created At</th>
        <th>Order Updated At</th>
        <th>Notes</th>
    </tr>
    </thead>
    <tbody>
    @foreach($orders as $order)
        <tr>
            <td>{{ $order->store->name }}</td>
            <td>{{ $order->invoice_id }}</td>
            <td>{{ $order->reference_id }}</td>
            <td>{{ $order->user->information->fullName }}</td>
            <td>{{ $order->user->email }}</td>
            <td>{{ $order->user->information->billingAddressInfo }}</td>
            <td>{{ $order->order_status }}</td>
            <td>{{ $order->payment_status }}</td>
            <td>
                @foreach($order->orderItems as $orderItem)
                    {{ $orderItem->product->name }} (x{{ $orderItem->quantity }})
                @endforeach
            </td>
            <td>{{ $order->product_payable }}</td>
            <td>{{ $order->shipping_fee }}</td>
            <td>{{ $order->tax_fee }}</td>
            <td>{{ $order->intermediary_fees }}</td>
            <td>{{ $order->total_sales }}</td>
            <td>{{ $order->user->information->shippingAddressInfo }}</td>
            <td>{{ $order->shipper }}</td>
            <td>{{ $order->item_type }}</td>
            <td>{{ $order->num_of_boxes_shipped }}</td>
            <td>{{ $order->tracking_reference }}</td>
            <td>{{ $order->orderCreatedBy->name }}</td>
            <td>{{ $order->created_at->format('Y-m-d') }}</td>
            <td>{{ $order->updated_at->format('Y-m-d') }}</td>
            <td>{{ $order->notes }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
