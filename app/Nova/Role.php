<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Silvanite\NovaToolPermissions\Role as BaseRole;
use App\Models\Store;

class Role extends BaseRole
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Role::class;

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
}
