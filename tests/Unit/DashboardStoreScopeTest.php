<?php

namespace Tests\Unit;

use App\Nova\Metrics\Concerns\ScopesToUserStores;
use App\Permissions\Admin;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Guards the store-scoping rule the dashboard metrics rely on:
 * admins see every store, everyone else only their own, guests nothing.
 * Compiles the query to SQL only — never touches the database.
 */
class DashboardStoreScopeTest extends TestCase
{
    private function scoper(): object
    {
        return new class {
            use ScopesToUserStores;

            public function build($request)
            {
                return $this->storeScopedOrders($request);
            }
        };
    }

    private function request(?object $user): object
    {
        return new class($user) {
            public function __construct(private ?object $user) {}

            public function user()
            {
                return $this->user;
            }
        };
    }

    private function user(bool $admin, array $storeIds): object
    {
        return new class($admin, $storeIds) {
            public Collection $stores;

            public function __construct(private bool $admin, array $storeIds)
            {
                $this->stores = collect($storeIds)->map(fn ($id) => (object) ['store_id' => $id]);
            }

            public function hasRoleWithPermission(string $permission): bool
            {
                return $this->admin && in_array($permission, [
                    Admin::ADMIN_ALL_ACCESS->value,
                    Admin::ADMIN_CAN_ACCESS_SALES_TRACKER->value,
                ], true);
            }
        };
    }

    public function test_non_admin_is_scoped_to_their_stores(): void
    {
        $sql = $this->scoper()->build($this->request($this->user(false, [3, 7])))->toSql();

        $this->assertStringContainsString('store_id', $sql);
        $this->assertStringContainsString('in (?, ?)', $sql);
    }

    public function test_admin_sees_every_store(): void
    {
        $sql = $this->scoper()->build($this->request($this->user(true, [])))->toSql();

        $this->assertStringNotContainsString('store_id', $sql);
    }

    public function test_guest_gets_empty_result_set(): void
    {
        $sql = $this->scoper()->build($this->request(null))->toSql();

        $this->assertStringContainsString('1 = 0', $sql);
    }
}
