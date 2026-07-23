<?php

namespace Tests\Unit;

use App\Nova\Metrics\AwaitingPayment;
use App\Nova\Metrics\OrdersToShip;
use App\Permissions\Admin;
use Tests\TestCase;

/**
 * Guards the extra filters the two order-ops metrics layer on top of the
 * store scope: AwaitingPayment only counts unpaid orders, OrdersToShip only
 * counts open (unfulfilled) orders. Compiles to SQL only — never hits the DB.
 * Uses an admin request so the store scope is a no-op and the assertions
 * isolate each metric's own filter.
 */
class OrderMetricsQueryTest extends TestCase
{
    /** Admin request: store scope returns every store, no pivot needed. */
    private function adminRequest(): object
    {
        $user = new class {
            public function hasRoleWithPermission(string $permission): bool
            {
                return in_array($permission, [
                    Admin::ADMIN_ALL_ACCESS->value,
                    Admin::ADMIN_CAN_ACCESS_SALES_TRACKER->value,
                ], true);
            }
        };

        return new class($user) {
            public function __construct(private object $user) {}

            public function user()
            {
                return $this->user;
            }
        };
    }

    public function test_awaiting_payment_filters_to_unpaid_orders(): void
    {
        $metric = new class extends AwaitingPayment {
            public function build($request)
            {
                return $this->orders($request);
            }
        };

        $sql = $metric->build($this->adminRequest())->toSql();

        $this->assertStringContainsString('payment_status', $sql);
    }

    public function test_orders_to_ship_filters_to_open_statuses(): void
    {
        $metric = new class extends OrdersToShip {
            public function build($request)
            {
                return $this->orders($request);
            }
        };

        $sql = $metric->build($this->adminRequest())->toSql();

        $this->assertStringContainsString('order_status', $sql);
        $this->assertStringContainsString('in (?, ?, ?)', $sql);
    }
}
