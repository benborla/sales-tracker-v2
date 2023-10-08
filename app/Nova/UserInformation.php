<?php

namespace App\Nova;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\TextArea;
use Laravel\Nova\Fields\Heading;
use Enmaboya\CountrySelect\CountrySelect;
use Digitalcloud\ZipCodeNova\ZipCode;
use Bissolli\NovaPhoneField\PhoneNumber;
use Dniccum\StateSelect\StateSelect;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Panel;

class UserInformation extends Resource
{
    /**
     * Indicates if the resource should be displayed in the sidebar.
     *
     * @var bool
     */
    public static $displayInNavigation = false;

    /**
     * Indicates whether to show the polling toggle button inside Nova.
     *
     * @var bool
     */
    public static $showPollingToggle = true;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\UserInformation::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'first_name',
        'last_name',
        'middle_name',
        'telephone_number',
        'mobile_number',
        'billing_address',
        'billing_address_city',
        'billing_address_state',
        'billing_address_zipcode',
        'billing_address_country',
        'shipping_address',
        'shipping_address_city',
        'shipping_address_state',
        'shipping_address_zipcode',
        'shipping_address_country',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        $resourceId = (int) $request->get('viaResourceId') || null;
        $user = $resourceId ? User::query()->where('id', '=', $resourceId)->first() : null;

        return [
            Panel::make('Details', [
                ID::make(__('ID'), 'id')->sortable(),

                BelongsTo::make('User', 'user', \App\Nova\User::class)->onlyOnDetail(),

                Select::make('Type')->options([
                    'customer' => 'Customer',
                    'staff' => 'Staff'
                ])->rules('required')->canSee(function () {
                    return admin_all_access();
                }),

                Boolean::make('Is Active')
                    ->canSee(function () {
                        return i('can update user status', \App\Models\UserInformation::class);
                    }),

                Heading::make('Full Name'),

                Text::make('First Name')
                    ->sortable()
                    ->rules('required', 'max:50'),

                Text::make('Last Name')
                    ->sortable()
                    ->rules('required', 'max:50'),

                Text::make('Middle Name (Optional)', 'middle_name')
                    ->nullable()
                    ->sortable()
                    ->rules('max:50'),

                Heading::make('Contact Numbers'),

                PhoneNumber::make('Telephone Number')
                    ->onlyCountries('US')
                    ->nullable()
                    ->sortable()
                    ->rules('max:50'),

                PhoneNumber::make('Mobile Number')
                    ->onlyCountries('US')
                    ->nullable()
                    ->sortable()
                    ->rules('max:50'),
            ]),

            Panel::make('Billing Address', [
                Heading::make('Billing Address'),
                Text::make('Address', 'billing_address')
                    ->nullable()
                    ->sortable()
                    ->rules('max:255')
                    ->hideFromIndex(),

                Text::make('City', 'billing_address_city')
                    ->nullable()
                    ->sortable()
                    ->rules('max:255')
                    ->hideFromIndex(),


                StateSelect::make('State', 'billing_address_state')
                    ->useFullNames()
                    ->nullable()
                    ->sortable()
                    ->hideFromIndex(),

                ZipCode::make('Zipcode', 'billing_address_zipcode')
                    ->nullable()
                    ->setCountry('US')
                    ->hideFromIndex(),

                CountrySelect::make('Country', 'billing_address_country')
                    ->only(['US'])
                    ->nullable()
                    ->hideFromIndex()
            ]),

            Panel::make('Shiping Address', [
                Heading::make('Shipping Address'),
                Text::make('Address', 'shipping_address')
                    ->nullable()
                    ->sortable()
                    ->rules('max:255')
                    ->hideFromIndex(),

                Text::make('City', 'shipping_address_city')
                    ->nullable()
                    ->sortable()
                    ->rules('max:255')
                    ->hideFromIndex(),

                StateSelect::make('State', 'shipping_address_state')
                    ->useFullNames()
                    ->nullable()
                    ->sortable()
                    ->hideFromIndex(),

                ZipCode::make('Zipcode', 'shipping_address_zipcode')
                    ->nullable()
                    ->setCountry('US')
                    ->hideFromIndex(),

                CountrySelect::make('Country', 'shipping_address_country')
                    ->only(['US'])
                    ->nullable()
                    ->hideFromIndex()
            ]),

            Panel::make('Credit Card', [
                Heading::make('Credit Card'),
                Select::make('Type', 'credit_card_type')->options([
                    'mastercard' => 'MasterCard',
                    'visa' => 'Visa',
                    'jcb' => 'JCB',
                    'amex' => 'American Express',
                ])->nullable(),

                Text::make('Card Number', 'credit_card_number')
                    ->rules('nullable', 'string', 'max:19'),

                Text::make('Expiration', 'credit_card_expiration_date')
                    ->rules('nullable', 'string', 'date_format:m/Y')
                    ->placeholder(\Carbon\Carbon::now()->format('m/Y'))
                    ->help('<span class="text-success">Format: 01/2023</span>'),

                Text::make('CVV', 'credit_card_cvv')
                    ->rules('nullable', 'string', 'max:3'),
            ]),


            Panel::make('Misc.', [
                Heading::make('Misc.'),
                TextArea::make('Notes')->nullable()->alwaysShow(),
            ])
        ];
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
    /**
     * @return string
     */
    public static function createLabel()
    {
        return 'Add Basic Information';
    }

    /**
     * Get the text for the create resource button.
     *
     * @return string|null
     */
    public static function createButtonLabel()
    {
        return __('Add :resource', ['resource' => 'Basic Information']);
    }

    /**
     * Get the text for the update resource button.
     *
     * @return string|null
     */
    public static function updateButtonLabel()
    {
        return __('Update :resource', ['resource' => 'Basic Information']);
    }
}
