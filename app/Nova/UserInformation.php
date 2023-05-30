<?php

namespace App\Nova;

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
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Panel;
use Laravel\Nova\Http\Requests\NovaRequest;

class UserInformation extends Resource
{
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
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            Panel::make('Details', [
                ID::make(__('ID'), 'id')->sortable(),

                Select::make('Type')->options([
                    'customer' => 'Customer',
                    'staff' => 'Staff'
                ]),

                Boolean::make('Is Active'),

                Text::make('Email')
                    ->sortable()
                    ->rules('required', 'max:100'),

                Heading::make('Full Name'),

                Text::make('First Name')
                    ->sortable()
                    ->rules('required', 'max:50'),

                Text::make('Last Name')
                    ->sortable()
                    ->rules('required', 'max:50'),

                Text::make('Middle Name (Optional)')
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

            Panel::make('Shipping Address', [
                Text::make('Address', 'shippping_address')
                    ->nullable()
                    ->sortable()
                    ->rules('max:255')
                    ->hideFromIndex(),

                Text::make('City', 'shippping_address_city')
                    ->nullable()
                    ->sortable()
                    ->rules('max:255')
                    ->hideFromIndex(),

                StateSelect::make('State', 'shippping_address_state')
                    ->useFullNames()
                    ->nullable()
                    ->sortable()
                    ->hideFromIndex(),

                ZipCode::make('Zipcode', 'shippping_address_zipcode')
                    ->nullable()
                    ->setCountry('US')
                    ->hideFromIndex(),

                CountrySelect::make('Country', 'shippping_address_country')
                    ->only(['US'])
                    ->nullable()
                    ->hideFromIndex()
            ]),

            Panel::make('Credit Card', [

                Select::make('Type', 'credit_card_type')->options([
                    'mastercard' => 'MasterCard',
                    'visa' => 'Visa',
                    'jcb' => 'JCB',
                    'amex' => 'American Express',
                ]),

                Text::make('Card Number', 'credit_card_number')
                    ->rules('string', 'max:19')
                    ->nullable(),

                Date::make('Expiration', 'credit_card_expiration_date')
                    ->format('MM/YYYY')
                    ->pickerDisplayFormat('m.Y')
                    ->nullable(),

                Text::make('CVV', 'credit_card_cvv')
                    ->nullable()
                    ->rules('string', 'max:3'),
            ]),


            Panel::make('Misc.', [
                TextArea::make('Notes')->nullable(),
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
}
