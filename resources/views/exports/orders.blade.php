@php
/** @var \App\Models\Order $order **/
@endphp
<table>
    <thead style="background: orange;">
    <tr>
        <th>Store</th>
        <th>Invoice ID</th>
    </tr>
    </thead>
    <tbody>
    @foreach($orders as $order)
        <tr>
            <td>{{ $order->store->name }}</td>
            <td>{{ $order->invoice_id }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
