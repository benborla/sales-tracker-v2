<?php

namespace App\Nova;

use App\Nova\AbstractUserBase;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use App\Models\UserInformation;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Panel;
use Enmaboya\CountrySelect\CountrySelect;
use Dniccum\StateSelect\StateSelect;
use Digitalcloud\ZipCodeNova\ZipCode;
use Laravel\Nova\Fields\HasMany;
use App\Rules\DuplicateUserInStore;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Password;

class Staff extends AbstractUserBase
{
    public static $type = UserInformation::USER_TYPE_STAFF;

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
        return [
            $this->displayStoresTextField(),
            $this->displayRoleTextField(),
            Text::make('Type', 'type', function () {
                return $this->information->type ?? 'staff';
            })
                ->default('staff')
                ->onlyOnForms()
                ->readonly(true),

            Select::make('Store', 'store')
                ->options($this->getStores())
                ->onlyOnForms()
                ->hideWhenUpdating()
                ->required(),

            Select::make('Position', 'role')
                ->options($this->getRoles())
                ->searchable()
                ->onlyOnForms()
                ->hideWhenUpdating()
                ->required(),

            Select::make('Team', 'team')
                ->options($this->getTeams())
                ->searchable()
                ->onlyOnForms()
                ->hideWhenUpdating(),

            Text::make('Email')
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}')
                ->onlyOnForms(),

            Password::make('Password')
                ->rules('required', 'email', 'min:8')
                ->onlyOnForms()->hideWhenCreating(),

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

            Text::make('Name', 'full_name', function () {
                $name = $this->information->full_name ?? '';
                $url = "/resources/{$this->uriKey()}/{$this->id}";
                return "<a class='no-underline dim text-primary font-bold' href='{$url}'>{$name}</a>";
            })->asHtml()->onlyOnIndex(),

            Text::make('Created At', 'created_at', function () {
                return $this->created_at->format('M. d Y');
            })->exceptOnForms(),

            Text::make('Updated At', 'updated_at', function () {
                return $this->created_at->format('M. d Y');
            })->exceptOnForms(),


            /** @INFO: Staff Address **/
            Panel::make('Address', [
                Text::make('Address', 'billing_address', function () {
                    return $this->information->billing_address ?? '';
                })
                    ->nullable()
                    ->sortable()
                    ->rules('max:255')
                    ->hideFromIndex(),

                Text::make('City', 'billing_address_city', function () {
                    return $this->information->billing_address_city ?? '';
                })
                    ->nullable()
                    ->sortable()
                    ->rules('max:255')
                    ->hideFromIndex(),

                StateSelect::make('State', 'billing_address_state', function () {
                    return $this->information->billing_address_state ?? '';
                })
                    ->useFullNames()
                    ->nullable()
                    ->sortable()
                    ->hideFromIndex(),

                ZipCode::make('Zipcode', 'billing_address_zipcode', function () {
                    return $this->information->billing_address_zipcode ?? '';
                })
                    ->nullable()
                    ->setCountry('US')
                    ->hideFromIndex(),

                CountrySelect::make('Country', 'billing_address_country', function () {

                    return $this->information->billing_address_country ?? '';
                })
                    ->only(['US'])
                    ->nullable()
                    ->hideFromIndex()
            ]),

            BelongsToMany::make('Roles', 'roles', Role::class),

            HasMany::make('Teams', 'teams', \App\Nova\GroupTeamMember::class)->sortable(),

            HasMany::make('Available Store(s)', 'stores', \App\Nova\UserStore::class)
                ->rules(new DuplicateUserInStore($request->user, $request->store))
                ->sortable(),
        ];
    }
}
