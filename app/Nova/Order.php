<?php

namespace App\Nova;

use App\Models\User;
use App\Models\Order as OrderModel;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Yassi\NestedForm\NestedForm;
use App\Nova\Filters\FilterByOrderStatus;
use App\Nova\Filters\FilterByPaymentStatus;
use App\Nova\Filters\FilterByCreatedAt;
use App\Nova\Filters\FilterByUpdatedAt;
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;
use Vyuldashev\NovaMoneyField\Money;

class Order extends Resource
{
    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Product & Orders';

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Order::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'invoice_id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'invoice_id',
        'reference_id',
        'order_status',
        'payment_status',
        'total_sales',
        'tracking_reference'
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        $customers = User::getCustomers()
            ->mapWithKeys(function ($user) {
                return [$user->id => "$user->last_name, $user->first_name $user->middle_name"];
            })->toArray();

        $staff = User::getStaffs()
            ->mapWithKeys(function ($user) {
                return [$user->id => "$user->last_name, $user->first_name $user->middle_name"];
            })->toArray();

        return [
            ID::make(__('ID'), 'id')->sortable()->hideFromIndex()->hideFromDetail(),
            Text::make('Invoice ID', function () {
                $url = "/resources/{$this->uriKey()}/{$this->id}";
                return "<a class='no-underline dim text-primary font-bold text-xs' href='{$url}'>{$this->invoice_id}</a>";
            })->asHtml()->exceptOnForms(),
            Text::make('Reference ID'),
            Text::make('Created At', function () {
                $createdAt = \Carbon\Carbon::parse($this->created_at)->diffForHumans();
                return "<p class='text-xs'>$createdAt</p>";
            })
                ->asHtml()
                ->onlyOnIndex(),
            DateTime::make('Date Created', 'created_at')->exceptOnForms()->onlyOnDetail(),
            DateTime::make('Last Update', 'updated_at')->exceptOnForms()->onlyOnDetail(),

            new Panel('Customer Information', array_merge([
                Select::make('Customer', 'user_id')
                    ->withMeta(['data-field' => 'user-field'])
                    ->required()
                    ->options($customers)
                    ->searchable()
                    ->onlyOnForms(),
                BelongsTo::make('Customer', 'user', \App\Nova\User::class)
                    ->exceptOnForms(),
            ], $this->getStoreField($request))),

            new Panel('Agent', [
                Select::make('Agent', 'handled_by_agent_user_id')->options($staff)
                    ->required()
                    ->searchable()
                    ->onlyOnForms(),

                BelongsTo::make('Agent', 'user', \App\Nova\User::class)
                    ->onlyOnDetail(),
            ]),

            new Panel('Status', [
                Badge::make('Order Status')->map([
                    OrderModel::ORDER_STATUS_NEW => 'info',
                    OrderModel::ORDER_STATUS_PROCESSING => 'warning',
                    OrderModel::ORDER_STATUS_IN_TRANSIT => 'warning',
                    OrderModel::ORDER_STATUS_FULFILLED => 'success',
                    OrderModel::ORDER_STATUS_FAILED => 'danger',
                ]),

                Badge::make('Payment Status')->map([
                    OrderModel::PAYMENT_STATUS_AWAITING_PAYMENT => 'warning',
                    OrderModel::PAYMENT_STATUS_SUCCESS => 'success',
                    OrderModel::PAYMENT_STATUS_FAILED => 'danger',
                    OrderModel::PAYMENT_STATUS_REFUND => 'danger',
                ]),

                Select::make('Order Status')->options([
                    OrderModel::ORDER_STATUS_NEW => 'New',
                    OrderModel::ORDER_STATUS_PROCESSING => 'Processing',
                    OrderModel::ORDER_STATUS_IN_TRANSIT => 'In Transit',
                    OrderModel::ORDER_STATUS_FULFILLED => 'Fulfilled',
                    OrderModel::ORDER_STATUS_FAILED => 'Failed',
                ])->default(OrderModel::ORDER_STATUS_NEW)->onlyOnForms(),

                Select::make('Payment Status')->options([
                    OrderModel::PAYMENT_STATUS_AWAITING_PAYMENT => 'Awaiting Payment',
                    OrderModel::PAYMENT_STATUS_SUCCESS => 'Success',
                    OrderModel::PAYMENT_STATUS_FAILED => 'Payment Failed',
                    OrderModel::PAYMENT_STATUS_REFUND => 'Payment Refund',
                ])->default(OrderModel::PAYMENT_STATUS_AWAITING_PAYMENT)->onlyOnForms(),
            ]),

            new Panel('Shipping Information', [
                Number::make('Total Boxes To Ship', 'num_of_boxes_shipped'),
                Select::make('Shipper')->options([
                    'UPS' => 'UPS',
                    'USPS' => 'USPS',
                    'FedEx' => 'FedEx',
                    'Pickup' => 'Pickup',
                ]),
                Text::make('Tracking Reference'),

            ]),

            new Panel('Products', [
                NestedForm::make('OrderItem', 'orderItems')
                    ->required()
                    ->heading('Product'),
                HasMany::make('Products', 'orderItems', \App\Nova\OrderItem::class)->required()->sortable(),
            ]),

            new Panel('Fees', [

                Money::make('Shipping Fee', 'USD', 'shipping_fee')
                    ->required()
                    ->hideFromIndex(),
                Money::make('Tax Fee', 'USD', 'tax_fee')
                    ->required()
                    ->hideFromIndex(),
                Money::make('Intermediary Fee', 'USD', 'intermediary_fees')
                    ->required()
                    ->hideFromIndex(),

                Number::make('Total Payable', 'total_sales')
                    ->step(0.01)
                    ->displayUsing(function ($fee) {
                        return '<p class="font-bold text-xs text-danger">$ ' . number_format($fee, 2) . '</p>';
                    })->asHtml()
                    ->exceptOnForms(),
            ]),

            new Panel('Misc', [
                Text::make('Sales Channel')
                    ->rules('required', 'string', 'max:20')
                    ->required(),
                Textarea::make('Notes')->alwaysShow()
            ])
        ];
    }

    private function getStoreField(NovaRequest $request): array
    {
        if ($request->is_main_store) {
            return [
                BelongsTo::make('Store', 'store', \App\Nova\Store::class)->display('name')
                    ->sortable()
            ];
        }

        return [];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [
            new FilterByOrderStatus,
            new FilterByPaymentStatus,
            new FilterByCreatedAt,
            new FilterByUpdatedAt,
        ];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [
            new DownloadExcel(),
        ];
    }

    /**
     * Build a "detail" query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function detailQuery(NovaRequest $request, $query)
    {
        $order = parent::detailQuery($request, $query);
        $orderToUpdate = $order->first();

        if ($orderToUpdate instanceof OrderModel) {
            update_total_payable($orderToUpdate);
        }

        return $order;
    }

}
