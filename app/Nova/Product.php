<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Panel;

class Product extends Resource
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
    public static $model = \App\Models\Product::class;

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
        'name',
        'retail_price',
        'reseller_price',
        'upc',
        'asin',

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
            BelongsTo::make('Store', 'store', \App\Nova\Store::class)
                ->display('name'),
            new Panel('Basic Information', $this->basicInfoFields()),
            new Panel('Product Information', $this->productInfoFields()),
            new Panel('Images', $this->productImageField()),
            new Panel('Pricing', $this->pricingFields()),
            new Panel('Misc.', $this->miscFields()),

        ];
    }

    protected function basicInfoFields()
    {
        return [
            Text::make('Name')->required(),
            Text::make('UPC')->required(),
            Text::make('ASIN')->required(),
            Text::make('SKU')->required(),
            Number::make('Remaining quantity in inventory', 'total_inventory_remaining')
                ->rules('integer')
                ->required(),
            Date::make('Manufactured Date')->required(),
            Text::make('Made from', 'made_from')->required(),
        ];
    }

    protected function productImageField()
    {
        return [

        ];
    }

    protected function productInfoFields()
    {
        return [
            // INFO: product image url
            Text::make('Product Image'),
            Text::make('Weight Value'),
            Text::make('Size'),
            Select::make('Weight Unit')->options([
                'oz' => 'oz',
                'fl/oz' => 'fl/oz',
                'ml' => 'ml'
            ])->default('oz'),
        ];
    }

    protected function pricingFields()
    {
        return [
            Number::make('Retail Price')->step(0.01)->displayUsing(fn ($amount) => "$ $amount"),
            Number::make('Reseller Price')->step(0.01)->displayUsing(fn ($amount) => "$ $amount"),
        ];
    }

    protected function miscFields()
    {
        return [
            Textarea::make('Notes')->alwaysShow(),
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
