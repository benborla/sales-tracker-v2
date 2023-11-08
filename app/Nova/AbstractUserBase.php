<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Models\UserInformation;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use App\Models\Store;
use App\Models\Role;
use App\Models\GroupTeam;

abstract class AbstractUserBase extends Resource
{
    /**
     * The pagination per-page options configured for this resource.
     *
     * @return array
     */
    public static $perPageOptions = [5, 10, 20, 30, 50];

    public static $type = UserInformation::USER_TYPE_CUSTOMER;

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Users';

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\User::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'email';

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
            ID::make(__('ID'), 'id')->sortable(),
            Text::make('Store', 'store_name')->exceptOnForms(),
            Text::make('Email', 'email')->exceptOnForms(),
            Text::make('Name', 'full_name')->exceptOnForms(),
            Text::make('Type', 'type'),

            Select::make('Type')->options([
                'customer' => 'Customer',
                'staff' => 'Staff'
            ])->rules('required')->canSee(function () {
                return admin_all_access();
            }),
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

    public static function restrictedQuery(NovaRequest $request, $query)
    {
        /** @var \Illuminate\Database\Query\Builder $query **/
        $query->select([
            'users.*',
            'user_information.type',
            'users.email',
            'users.name as users.full_name',
            'stores.name as store_name',
        ]);
        $query->join('user_information', 'user_information.user_id', '=', 'users.id');
        $query->leftJoin('user_stores', 'user_stores.user_id', '=', 'user_information.user_id');
        $query->leftJoin('stores', 'stores.id', '=', 'user_stores.store_id');

        $query->where('user_information.type', '=', static::$type);

        if (is_main_store()) {
            return $query;
        }

        return $query->where('stores.id', '=', get_store_id());
    }

    /**
     * Build an "index" query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        return self::restrictedQuery($request, $query);
    }

    /**
     * Build a Scout search query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Laravel\Scout\Builder  $query
     * @return \Laravel\Scout\Builder
     */
    public static function scoutQuery(NovaRequest $request, $query)
    {
        return self::restrictedQuery($request, $query);
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
        return self::restrictedQuery($request, $query);
    }

    /**
     * Returns all available stores
     *
     * @return array
     */
    protected function getStores(): array
    {
        /** @var \Illuminate\Database\Query\Builder $query **/
        $query = Store::query();

        /** @INFO: Set the current store ID if user is in a subdomain **/
        if (!admin_all_access()) {
            $query->where('id', '=', get_store_id());
        }

        return $query
            ->get()
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * Returns all available roles
     *
     * @return array
     */
    protected function getRoles(): array
    {
        /** @var \Illuminate\Database\Query\Builder $query **/
        $query = Role::query();

        // @INFO: If user is a super admin
        if (!admin_all_access()) {
            $query->whereNotIn('slug', ['sales-tracker-admin', 'customer']);
        }

        // @INFO: This should not include customer role since we have
        // a dedicated resource tool for that
        $query->whereNotIn('slug', ['customer']);

        return $query
            ->get()
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * Returns all available teams
     *
     * @return array
     */
    protected function getTeams(): array
    {
        /** @var \Illuminate\Database\Query\Builder $query **/
        $query = GroupTeam::query();

        if (! is_main_store()) {
            $query->where('store_id', get_store_id());
        }

        return $query
            ->get()
            ->pluck('name', 'id')
            ->toArray();
    }
}
