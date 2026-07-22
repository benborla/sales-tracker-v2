<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Observers\OrderItemObserver;
use App\Observers\OrderObserver;
use App\Observers\ProductObserver;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use Laravel\Nova\Observable;
use App\Models\User;
use App\Observers\UserObserver;
use App\Permissions\Dashboard;
use App\Nova\Metrics\TotalSales;
use App\Nova\Metrics\SalesTrend;
use App\Nova\Metrics\NewOrders;
use App\Nova\Metrics\OrdersByStatus;
use App\Nova\Metrics\OrdersByChannel;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        Observable::make(Order::class, OrderObserver::class);
        Observable::make(OrderItem::class, OrderItemObserver::class);
        Observable::make(Product::class, ProductObserver::class);
        Observable::make(User::class, UserObserver::class);
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
                ->withAuthenticationRoutes()
                ->withPasswordResetRoutes()
                ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        // Gate::define('viewNova', function ($user) {
        //     return in_array($user->email, [
        //         'aromaguera@example.org'
        //     ]);
        // });
    }

    /**
     * Get the cards that should be displayed on the default Nova dashboard.
     *
     * @return array
     */
    protected function cards()
    {
        return [
            (new TotalSales)->canSee($this->canViewCard(Dashboard::DASHBOARD_CAN_VIEW_TOTAL_SALES)),
            (new NewOrders)->canSee($this->canViewCard(Dashboard::DASHBOARD_CAN_VIEW_NEW_ORDERS)),
            (new SalesTrend)->canSee($this->canViewCard(Dashboard::DASHBOARD_CAN_VIEW_SALES_TREND)),
            (new OrdersByStatus)->canSee($this->canViewCard(Dashboard::DASHBOARD_CAN_VIEW_ORDERS_BY_STATUS)),
            (new OrdersByChannel)->canSee($this->canViewCard(Dashboard::DASHBOARD_CAN_VIEW_ORDERS_BY_CHANNEL)),
        ];
    }

    /**
     * Build a canSee callback that checks a single dashboard permission.
     */
    protected function canViewCard(Dashboard $permission): callable
    {
        return fn ($request) => $request->user()?->hasRoleWithPermission($permission->value) ?? false;
    }

    /**
     * Get the extra dashboards that should be displayed on the Nova dashboard.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [
            // new \Silvanite\NovaToolPermissions\NovaToolPermissions()
        ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
