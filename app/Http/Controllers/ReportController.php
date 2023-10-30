<?php

namespace App\Http\Controllers;

use App\Exports\OrdersExport;
use App\Http\Requests\OrderFormRequest;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function exportOrders(OrderFormRequest $request)
    {
        return Excel::download(new OrdersExport, 'orders.xlsx');
    }
}
