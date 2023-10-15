<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Http\Requests\NovaRequest;

class Store extends Resource
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
    public static $model = \App\Models\Store::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

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
            ID::make(__('ID'), 'id')
                ->sortable()
                ->hideFromIndex()
                ->hideFromDetail(),

            Text::make('Name', function () {
                if (i('can view', static::$model)) {
                    $url = "/resources/{$this->uriKey()}/{$this->id}";
                    return "<a class='no-underline dim text-primary font-bold' href='{$url}'>{$this->name}</a>";
                }

                return $this->name;
            })
                ->asHtml()
                ->exceptOnForms(),

            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Trix::make('Description'),
            Boolean::make('Is Active'),

            Text::make('Domain', function () use ($request) {
                $scheme = $request->getScheme() . '://';
                $domain = $scheme . $this->domain . '.' . get_main_store_domain();

                return "<a class='no-underline dim text-primary font-bold' href='{$domain}' target='_blank'>{$domain}</a>";
            })->exceptOnForms()->asHtml(),

            Text::make('Sub-domain', 'domain')
                ->sortable()
                ->onlyOnForms()
                ->rules('required', 'max:255'),
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
