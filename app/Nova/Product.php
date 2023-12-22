<?php

namespace App\Nova;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Panel;
use Saumini\Count\RelationshipCount;
use Vyuldashev\NovaMoneyField\Money;
use App\Models\Store;

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
        'sku',
        'made_from',
        'notes',
    ];

    /**
     * The relationship columns that should be searched.
     *
     * @var array
     */
    public static $searchRelations = [
        'store' => ['name'],
    ];

    /**
     * @param |Laravel\Nova\Http\Requests\NovaRequest $request
     * @param \Illuminate\Database\Query\Builder $query
     *
     * @return void
     */
    public static function restrictedQuery(NovaRequest $request, $query)
    {
        if (admin_all_access()) {
            return $query;
        }

        if (i('can view all in store', static::$model)) {
            return $query
                ->where('store_id', '=', get_store_id());
        }

        abort(403);
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
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        $stores = [];

        if (!is_main_store()) {
            $stores = Store::where('id', get_store_id())->get();
        } else {
            $stores = Store::all();
        }

        // map stores with keys
        $stores = $stores->mapWithKeys(function (Store $store) {
            return [$store->id => $store->name];
        })->toArray();


        return [
            ID::make(__('ID'), 'id')->sortable()->hideFromIndex(),
            BelongsTo::make('Store', 'store', \App\Nova\Store::class)
                ->exceptOnForms()
                ->display('name'),

            Select::make('Store', 'store_id')
                ->withMeta(['data-field' => 'store-field'])
                ->required()
                ->options($stores)
                ->searchable()
                ->onlyOnForms(),

            Text::make('Last Update', 'updated_at', function () {
                return $this->updated_at->format('M d, Y h:i:s A');
            })->onlyOnDetail(),
            Text::make('Updated By', 'updatedBy', function () {
                return $this->updatedBy->information->fullName ?? $this->updatedBy->email;
            })->onlyOnDetail(),

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
            Text::make('Name', function () {
                $url = "/resources/{$this->uriKey()}/{$this->id}";
                return "<a class='no-underline dim text-primary font-bold' href='{$url}'>{$this->name}</a>";
            })
                ->asHtml()
                ->onlyOnIndex(),
            Text::make('Name')->required()->hideFromIndex(),
            Text::make('UPC')->required()->hideFromIndex(),
            Text::make('ASIN')->required()->hideFromIndex(),
            Text::make('SKU')->required(),
            Number::make('Remaining quantity in inventory', 'total_inventory_remaining')
                ->rules('integer')
                ->canSee(function () {
                    return is_staff();
                })
                ->required(),
            Date::make('Manufactured Date', 'manufactured_date', function () {
                if ($this->manufactured_date) {
                    return $this->manufactured_date->format('M d, Y');
                }
            })->required(),
            Text::make('Made from', 'made_from')->required(),
        ];
    }

    protected function productImageField()
    {
        return [
            HasMany::make('Images', 'images', \App\Nova\ProductImage::class)
        ];
    }

    protected function productInfoFields()
    {
        return [
            // INFO: product image url
            Text::make('Weight Value')->hideFromIndex(),
            Text::make('Size')->hideFromIndex(),
            Select::make('Weight Unit')->options([
                'oz' => 'oz',
                'fl/oz' => 'fl/oz',
                'ml' => 'ml'
            ])->default('oz')->hideFromIndex(),
        ];
    }

    protected function pricingFields()
    {
        return [
            Money::make('US Price', 'USD', 'retail_price'),
            Money::make('Wholesale Price', 'USD', 'reseller_price'),
        ];
    }

    protected function miscFields()
    {
        return [
            RelationshipCount::make('Orders', 'orders')
                ->canSee(function () {
                    return is_staff();
                })
                ->exceptOnForms(),
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
