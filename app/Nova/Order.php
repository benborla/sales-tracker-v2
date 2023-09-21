<?php

namespace App\Nova;

use App\Models\User;
use App\Models\Order as OrderModel;
use App\Models\GroupTeamMember;
use Illuminate\Database\Query\Builder;
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
            Text::make('Invoice ID')->exceptOnForms(),
            Text::make('Reference ID')->exceptOnForms(),
            DateTime::make('Created At')->format('Y-m-d H:i:s')->exceptOnForms(),
            DateTime::make('Updated At')->format('DD MMM YYYY - H:i:s A')->exceptOnForms()->hideFromIndex(),

            new Panel('Customer Information', array_merge([
                Select::make('Customer', 'user_id')
                    ->withMeta(['data-field' => 'user-field'])
                    ->options($customers)
                    ->searchable()
                    ->onlyOnForms(),

                BelongsTo::make('Customer', 'user', \App\Nova\User::class)
                    ->fillUsing(function ($request, $model, $attribute, $requestAttribute) {
                        dd($model);
                    })
                    ->exceptOnForms(),
            ], $this->getStoreField($request))),

            new Panel('Agent', [
                Select::make('Agent', 'handled_by_agent_user_id')->options($staff)
                    ->searchable()
                    ->onlyOnForms(),

                BelongsTo::make('Agent', 'user', \App\Nova\User::class)
                    ->fillUsing(function ($request, $model, $attribute, $requestAttribute) {
                        $model->{$attribute} = 'fucker';
                    })
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
                    ->heading('Product'),

                HasMany::make('Products', 'orderItems', \App\Nova\OrderItem::class)->sortable(),
            ]),

            new Panel('Fees', [
                Number::make('Shipping Fee', 'shipping_fee')
                    ->step(0.01)
                    ->displayUsing(function ($fee) {
                        return '$ ' . number_format($fee, 2);
                    })->hideFromIndex(),
                Number::make('Tax Fee', 'tax_fee')
                    ->step(0.01)
                    ->displayUsing(function ($fee) {
                        return '$ ' . number_format($fee, 2);
                    })->hideFromIndex(),
                Number::make('Intermediary Fees', 'intermediary_fees')
                    ->step(0.01)
                    ->displayUsing(function ($fee) {
                        return '$ ' . number_format($fee, 2);
                    })->hideFromIndex(),

                Number::make('Total Payable', 'total_sales')
                    ->step(0.01)
                    ->displayUsing(function ($fee) {
                        return '$ ' . number_format($fee, 2);
                    })
                    ->exceptOnForms(),

            ]),

            new Panel('Misc', [
                Textarea::make('Notes')
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
        return [];
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
        return [];
    }
}
