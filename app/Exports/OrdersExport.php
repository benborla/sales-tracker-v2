<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithProperties;

class OrdersExport implements FromView, WithProperties
{
    private const ALL = '*';

    public function __construct(
        public ?string $store = '',
        public ?string $createdBy = '',
        public ?string $salesChannel = '',
        public ?string $createdAtFrom = '',
        public ?string $createdAtTo = '',
        public ?string $orderStatus = '',
        public ?string $paymentStatus = '',
    )
    {
    }

    public function view(): View
    {
        return view('exports.orders', [
            'orders' => $this->query()
        ]);
    }

    private function query()
    {
        $query = Order::query();

        $query->with(['user', 'orderCreatedBy', 'orderUpdatedBy', 'orderItems', 'store']);

        if (! $this->isAllOrNull($this->createdBy)) {
            $query->where('created_by', $this->createdBy);
        }

        if (! $this->isAllOrNull($this->store)) {
            $query->where('store_id', $this->store);
        }

        if (! $this->isAllOrNull($this->salesChannel)) {
            $query->where('sales_channel', $this->salesChannel);
        }

        if (! $this->isAllOrNull($this->createdAtFrom)) {
            $query->whereDate('created_at', '>=', $this->createdAtFrom);
        }

        if (! $this->isAllOrNull($this->createdAtTo)) {
            $query->whereDate('created_at', '<=', $this->createdAtTo);
        }

        if (! $this->isAllOrNull($this->orderStatus)) {
            $query->where('order_status', $this->orderStatus);
        }

        if (! $this->isAllOrNull($this->paymentStatus)) {
            $query->where('payment_status', $this->paymentStatus);
        }

        return $query->get();
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

    private function isAllOrNull($value): bool
    {
        return $value === self::ALL || $value === null || trim($value) === '';
    }
}
