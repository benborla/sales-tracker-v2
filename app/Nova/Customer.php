<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Nova\AbstractUserBase;
use App\Models\UserInformation;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Select;
use Digitalcloud\ZipCodeNova\ZipCode;
use Enmaboya\CountrySelect\CountrySelect;
use Dniccum\StateSelect\StateSelect;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Panel;
use Bissolli\NovaPhoneField\PhoneNumber;

class Customer extends AbstractUserBase
{
    public static $type = UserInformation::USER_TYPE_CUSTOMER;

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'email',
    ];

    /**
     * Get the searchable columns for the resource.
     *
     * @return array
     */
    public static function searchableRelations(): array
    {
        // @TODO: Add gatekeeper permission here
        return [
            'information' => ['first_name', 'middle_name', 'last_name'],
            'stores.store' => ['name'],
        ];
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        $canSeeCreditCardInfo = i('can view credit card info', static::$model);
        $canSeeShippingAddress = i('can view shipping address', static::$model);
        $canSeeBillingAddress = i('can view billing address', static::$model);

        return [
            $this->displayStoresTextField(),

            Text::make('Type', 'type', function () {
                return $this->information->type ?? 'customer';
            })
                ->default('customer')
                ->onlyOnForms()
                ->readonly(true),

            Select::make('Store', 'store')
                ->options($this->getStores())
                ->onlyOnForms()
                ->required()
                ->hideWhenUpdating(),

            Text::make('Name', 'full_name', function () {
                $name = $this->information->full_name ?? '';
                $url = "/resources/{$this->uriKey()}/{$this->id}";
                return "<a class='no-underline dim text-primary font-bold' href='{$url}'>{$name}</a>";
            })
                ->asHtml()
                ->onlyOnIndex(),

            Text::make('Email')
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}')
                ->onlyOnForms(),

            Text::make('Email', 'email')
                ->exceptOnForms(),

            Boolean::make('Is Active', 'is_active', function () {
                return $this->information->is_active ?? false;
            })
                ->hideWhenCreating()
                ->canSee(function () {
                    return i('can update user status', \App\Models\UserInformation::class);
                }),

            Text::make('First Name', 'first_name', function () {
                return $this->information->first_name ?? '';
            })->required()->hideFromIndex(),

            Text::make('Last Name', 'last_name', function () {
                return $this->information->last_name ?? '';
            })->required()->hideFromIndex(),

            Text::make('Middle Name', 'middle_name', function () {
                return $this->information->middle_name ?? '';
            })->required()->hideFromIndex(),


            Text::make('Created At', 'created_at', function () {
                return $this->created_at->format('M. d Y');
            })->exceptOnForms(),

            Text::make('Updated At', 'updated_at', function () {
                return $this->created_at->format('M. d Y');
            })->exceptOnForms(),

            /** @INFO: Contact Number **/
            Panel::make('Contact Number(s)', [
                PhoneNumber::make('Telephone Number', 'telephone_number', function () {
                    return $this->information->telephone_number ?? null;;
                })
                    ->onlyCountries('US')
                    ->nullable()
                    ->sortable()
                    ->hideFromIndex()
                    ->rules('max:50'),

                PhoneNumber::make('Mobile Number', 'mobile_number', function () {
                    return $this->information->mobile_number ?? null;
                })
                    ->onlyCountries('US')
                    ->nullable()
                    ->sortable()
                    ->hideFromIndex()
                    ->rules('max:50'),
            ]),

            /** @INFO: Billing Address **/
            Panel::make('Billing Address', [
                Text::make('Address', 'billing_address', function () {
                    return $this->information->billing_address ?? '';
                })
                    ->nullable()
                    ->sortable()
                    ->rules('max:255')
                    ->canSee(function ()  use ($canSeeBillingAddress) {
                        return $canSeeBillingAddress;
                    })
                    ->hideFromIndex(),

                Text::make('City', 'billing_address_city', function () {
                    return $this->information->billing_address_city ?? '';
                })
                    ->nullable()
                    ->sortable()
                    ->rules('max:255')
                    ->canSee(function ()  use ($canSeeBillingAddress) {
                        return $canSeeBillingAddress;
                    })
                    ->hideFromIndex(),

                StateSelect::make('State', 'billing_address_state', function () {
                    return $this->information->billing_address_state ?? '';
                })
                    ->useFullNames()
                    ->nullable()
                    ->sortable()
                    ->canSee(function ()  use ($canSeeBillingAddress) {
                        return $canSeeBillingAddress;
                    })
                    ->hideFromIndex(),

                ZipCode::make('Zipcode', 'billing_address_zipcode', function () {
                    return $this->information->billing_address_zipcode ?? '';
                })
                    ->nullable()
                    ->setCountry('US')
                    ->canSee(function ()  use ($canSeeBillingAddress) {
                        return $canSeeBillingAddress;
                    })
                    ->hideFromIndex(),

                CountrySelect::make('Country', 'billing_address_country', function () {

                    return $this->information->billing_address_country ?? '';
                })
                    ->only(['US'])
                    ->nullable()
                    ->canSee(function ()  use ($canSeeBillingAddress) {
                        return $canSeeBillingAddress;
                    })
                    ->hideFromIndex()
            ]),

            /** @INFO: Shipping Address **/
            Panel::make('Shiping Address', [
                Text::make('Address', 'shipping_address', function () {
                    return $this->information->shipping_address ?? '';
                })
                    ->nullable()
                    ->sortable()
                    ->rules('max:255')
                    ->canSee(function ()  use ($canSeeShippingAddress) {
                        return $canSeeShippingAddress;
                    })
                    ->hideFromIndex(),

                Text::make('City', 'shipping_address_city', function () {
                    return $this->information->shipping_address_city ?? '';
                })
                    ->nullable()
                    ->sortable()
                    ->rules('max:255')
                    ->canSee(function ()  use ($canSeeShippingAddress) {
                        return $canSeeShippingAddress ?? '';
                    })
                    ->hideFromIndex(),

                StateSelect::make('State', 'shipping_address_state', function () {
                    return $this->information->shipping_address_state ?? '';
                })
                    ->useFullNames()
                    ->nullable()
                    ->sortable()
                    ->canSee(function ()  use ($canSeeShippingAddress) {
                        return $canSeeShippingAddress;
                    })
                    ->hideFromIndex(),

                ZipCode::make('Zipcode', 'shipping_address_zipcode', function () {
                    return $this->information->shipping_address_zipcode ?? '';
                })
                    ->nullable()
                    ->setCountry('US')
                    ->canSee(function ()  use ($canSeeShippingAddress) {
                        return $canSeeShippingAddress;
                    })
                    ->hideFromIndex(),

                CountrySelect::make('Country', 'shipping_address_country', function () {
                    return $this->information->shipping_address_country ?? '';
                })
                    ->only(['US'])
                    ->nullable()
                    ->canSee(function ()  use ($canSeeShippingAddress) {
                        return $canSeeShippingAddress;
                    })
                    ->hideFromIndex()
            ]),

            /** INFO: Credit Card Info **/
            Panel::make('Credit Card', [
                Select::make('Type', 'credit_card_type', function () {
                    return $this->information->credit_card_type ?? '';
                })->options([
                    'mastercard' => 'MasterCard',
                    'visa' => 'Visa',
                    'jcb' => 'JCB',
                    'amex' => 'American Express',
                ])
                    ->canSee(function () use ($canSeeCreditCardInfo) {
                        return $canSeeCreditCardInfo;
                    })
                    ->hideFromIndex()
                    ->nullable(),

                Text::make('Card Number', 'credit_card_number', function () {
                    return $this->information->credit_card_number ?? '';
                })
                    ->rules('nullable', 'string', 'max:19')
                    ->hideFromIndex()
                    ->canSee(function () use ($canSeeCreditCardInfo) {
                        return $canSeeCreditCardInfo;
                    }),

                Text::make('Expiration', 'credit_card_expiration_date', function () {
                    return $this->information->credit_card_expiration_date ?? '';
                })
                    ->rules('nullable', 'string', 'date_format:m/Y')
                    ->placeholder(\Carbon\Carbon::now()->format('m/Y'))
                    ->hideFromIndex()
                    ->help('<span class="text-success">Format: 01/2023</span>')
                    ->canSee(function () use ($canSeeCreditCardInfo) {
                        return $canSeeCreditCardInfo;
                    }),

                Text::make('CVV', 'credit_card_cvv', function () {
                    return $this->information->credit_card_cvv ?? '';
                })
                    ->rules('nullable', 'string', 'max:3')
                    ->hideFromIndex()
                    ->canSee(function () use ($canSeeCreditCardInfo) {
                        return $canSeeCreditCardInfo;
                    }),
            ]),

            /** INFO: Misc fields **/
            Panel::make('Misc.', [
                Textarea::make('Notes', 'notes', function () {
                    return $this->information->notes ?? '';
                })->nullable()->alwaysShow(),
            ]),

            HasMany::make('Orders', 'orders', Order::class),
        ];
    }
}
