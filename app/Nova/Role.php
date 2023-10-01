<?php

namespace App\Nova;

use Silvanite\NovaToolPermissions\Role as BaseRole;
use App\Models\Store;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Silvanite\Brandenburg\Policy;
use Benjaminhirsch\NovaSlugField\Slug;
use Laravel\Nova\Fields\BelongsToMany;
use Silvanite\NovaFieldCheckboxes\Checkboxes;
use Benjaminhirsch\NovaSlugField\TextWithSlug;
use App\Permissions\GetPermissions;

class Role extends BaseRole
{
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
    public static $model = \App\Models\Role::class;

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),

            TextWithSlug::make(__('Name'), 'name')->sortable()->slug('slug'),

            Slug::make(__('Slug'), 'slug')
                ->rules('required')
                ->creationRules('unique:roles')
                ->updateRules('unique:roles,slug,{{resourceId}}')
                ->sortable(),

            Checkboxes::make(__('Permissions'), 'permissions')->options(collect(GetPermissions::all())
                ->mapWithKeys(function ($policy) {
                    return [
                        $policy => __($policy),
                    ];
                })
                ->sort()
                ->toArray()),

            Text::make(__('Users'), function () {
                return count($this->users);
            })->onlyOnIndex(),

            BelongsToMany::make(__('Users'), 'users', config('novatoolpermissions.userResource', 'App\Nova\User'))
                ->searchable(),
        ];
    }


    /**
     * @var \Illuminate\Http\Request $request
     * @var \Illuminate\Database\Query\Builder $query
     */
    public static function indexQuery(Request $request, $query)
    {
        /** @var \App\Models\Store $store **/
        /** @var \Illuminate\Database\Query\Builder $query **/

        $store = $request->get('store');

        if (! $store instanceof Store) {
            return;
        }

        return $query->where('store_id', $store->id);
    }

    /**
     * Get the logical group associated with the resource.
     *
     * @return string
     */
    public static function group()
    {
        return static::$group;
    }

}
