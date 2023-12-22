<?php

namespace App\Http\Controllers;

use App\Exports\OrdersExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function exportOrders(Request $request)
    {
        $store = $request->get('store');
        $createdBy = $request->get('created_by');
        $salesChannel = $request->get('sales_channel', '');
        $createdAtFrom = $request->get('created_at_from', '');
        $createdAtTo = $request->get('created_at_to', '');
        $orderStatus = $request->get('order_status');
        $paymentStatus = $request->get('payment_status');

        return Excel::download(new OrdersExport(
            $store,
            $createdBy,
            $salesChannel,
            $createdAtFrom,
            $createdAtTo,
            $orderStatus,
            $paymentStatus,
        ), 'sales-tracker-orders-export-' . now()->format('Y-m-d') . '.xlsx');
    }
}
