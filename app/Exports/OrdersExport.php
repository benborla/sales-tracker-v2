<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithProperties;

class OrdersExport implements FromView, WithProperties
{
    public function view(): View
    {
        return view('exports.orders', [
            'orders' => $this->query()
        ]);
    }

    private function query()
    {
        return Order::all();
    }

    public function properties(): array
    {
        return [
            'creator'        => 'Indie',
            'lastModifiedBy' => 'Indie',
            'title'          => 'Order Report',
            'description'    => 'Order Generated Report via Sales Tracker',
            'subject'        => 'Orders',
            'keywords'       => 'orders,exports,spreadsheet',
            'category'       => 'Orders',
            'manager'        => 'Indie',
            'company'        => 'Zeta',
        ];
    }
}
