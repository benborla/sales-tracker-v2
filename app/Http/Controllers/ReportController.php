<?php

namespace App\Http\Controllers;

use App\Exports\OrdersExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function exportOrders(Request $request)
    {
        return Excel::download(new OrdersExport, 'orders.xlsx');
    }
}
