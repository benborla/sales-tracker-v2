<?php

namespace App\Nova;

use App\Rules\DuplicateUserInStore;
use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Text;
use Silvanite\NovaToolPermissions\Role;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Http\Requests\NovaRequest;

class User extends Resource
{
    /**
     * Indicates if the resource should be displayed in the sidebar.
     *
     * @var bool
     */
    public static $displayInNavigation = false;

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Administration';

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
        'user_information.first_name',
        'user_information.last_name',
        'user_information.middle_name',
        'email',
        'group_teams.name',
        'roles.name'
    ];

    /**
     * The relationships that should be eager loaded on index queries.
     *
     * @var array
     */
    // public static $with = ['information', 'teams', 'roles'];

    public static function restrictedQuery(NovaRequest $request, $query)
    {
        if (is_customer()) {
            return $query->where('id', '=', auth()->id());
        }

        /** @var \Illuminate\Database\Query\Builder $query **/
        $query = $query
            ->select([
                'users.*',
                'user_information.first_name as first_name',
                'user_information.last_name as last_name',
                'user_information.middle_name as middle_name',
                'r.name as role',
                'group_teams.name as team'
            ])
            ->leftjoin('group_team_members', 'group_team_members.user_id', '=', 'users.id')
            ->leftjoin('group_teams', 'group_teams.id', '=', 'group_team_members.group_teams_id')
            ->leftjoin('user_information', 'user_information.user_id', '=', 'users.id')
            ->leftjoin(DB::raw('role_user ru'), 'ru.user_id', '=', 'users.id')
            ->leftjoin(DB::raw('roles r'), 'r.id', '=', 'ru.role_id');

        if (admin_all_access()) {
            return $query;
        }

        if (i('can view all in store', static::$model)) {
            return $query
                ->leftjoin('user_stores', 'user_stores.user_id', '=', 'users.id')
                ->leftjoin('stores', 'stores.id', '=', 'user_stores.store_id')
                ->where('stores.id', '=', get_store_id())
                ->distinct();
        }

        $query->where('id', '=', auth()->user()->id);
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
        if (admin_all_access()) {
            return $query;
        }

        if (i('can view all in store', static::$model)) {
            return $query
                ->leftjoin('user_stores', 'user_stores.user_id', '=', 'users.id')
                ->leftjoin('stores', 'stores.id', '=', 'user_stores.store_id')
                ->where('stores.id', '=', get_store_id())
                ->distinct();
        }

        return $query->where('id', '=', auth()->user()->id);
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
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        $isStaff = is_staff();
        return [
            ID::make(__('ID'), 'id')
                ->sortable()
                ->hideFromIndex()
                ->hideFromDetail(),

            Text::make('Email', function () {
                $url = "/resources/{$this->uriKey()}/{$this->id}";
                return "<a class='no-underline dim text-primary font-bold' href='{$url}'>{$this->email}</a>";
            })
                ->asHtml()
                ->exceptOnForms(),

            Text::make('Email')
                ->sortable()
                ->onlyOnForms()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}'),

            Gravatar::make()->maxWidth(50),

            Text::make('Store', function () {
                if (admin_all_access()) {
                    return $this->stores->implode('store.name', ', ');
                }

                return store()->name;
            })
                ->showOnIndex(admin_all_access())
                ->showOnDetail(admin_all_access())
                ->canSee(function () use ($isStaff) {
                    return $isStaff;
                })
                ->exceptOnForms()
                ->sortable(),

            Text::make('Name', function () {
                $name = "$this->last_name, $this->first_name " . strtoupper(substr($this->middle_name, 1, 1));

                if (strlen(trim($name)) <= 1) {
                    return $this->name ?: $this->email;
                }

                return $name;
            })
                ->exceptOnForms()
                ->sortable(),

            Text::make('Name')->required()->onlyOnForms(),

            Text::make('Team', function () {
                return $this->team;
            })
                ->exceptOnForms()
                ->canSee(function () use ($isStaff) {
                    return $isStaff;
                })
                ->sortable(),

            Text::make('Role', function () {
                return $this->role;
            })
                ->exceptOnForms()
                ->sortable()
                ->canSee(function () use ($isStaff) {
                    return $isStaff;
                }),

            Password::make('Password')
                ->onlyOnForms()
                ->creationRules('required', 'string', 'min:8')
                ->updateRules('nullable', 'string', 'min:8'),

            BelongsToMany::make('Roles', 'roles', Role::class)
                ->canSee(function () use ($isStaff) {
                    return $isStaff;
                }),
            HasOne::make('Basic Information', 'information', \App\Nova\UserInformation::class)->sortable(),
            HasMany::make('Teams', 'teams', \App\Nova\GroupTeamMember::class)->sortable(),
            HasMany::make('Available Store(s)', 'stores', \App\Nova\UserStore::class)
                ->rules(new DuplicateUserInStore($request->user, $request->store))
                ->sortable(),
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
