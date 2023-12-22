<?php

namespace App\Nova;

use App\Models\Product;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Select;

class OrderItem extends Resource
{
    /**
     * Indicates if the resource should be displayed in the sidebar.
     *
     * @var bool
     */
    public static $displayInNavigation = false;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\OrderItem::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'order_entries_id';

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
        $products = Product::query()
            ->getActive()
            ->getProductsBasedOnStore()
            ->where('total_inventory_remaining', '>=', 1)
            ->with(['store'])
            ->get()
            ->mapWithKeys(function ($product) {
                $store = $product->store['name'];

                $remainingInventory = (int) $product->total_inventory_remaining >= 1 ?
                    'Stock: ' . $product->total_inventory_remaining : 'Out of stock';

                return [$product->id => "$store » $product->name (UPC: $product->upc)"];
            })->toArray();

        return [
            ID::make(__('ID'), 'id')->sortable(),
            Number::make('Quantity', 'quantity')->default('1'),
            BelongsTo::make('Order', 'order', \App\Nova\Order::class),
            BelongsTo::make('Product', 'product', \App\Nova\Product::class)->displayUsing(function ($product) {
                return $product->name;
            })->withSubtitles()->exceptOnForms(),

            Number::make('Price', function () {
                $priceBasedOn = $this->order->price_based_on;
                $price = $this->product->$priceBasedOn;

                return "$ " . number_format($price, 2);
            })->exceptOnForms(),

            Select::make('Product', 'product_id')
                ->withMeta(['data-field' => 'product-field'])
                ->rules('required')
                ->options($products)
                ->searchable()
                ->onlyOnForms(),

            // Number::make('Remaining', 'product_remaining_quantity')->exceptOnForms(),
            Number::make('Total Price', 'formatted_total_price')
                ->exceptOnForms()
        ];
    }

    /**
     * Build a "relatable" query for the given resource.
     *
     * This query determines which instances of the model may be attached to other resources.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function relatableQuery(NovaRequest $request, $query)
    {
        return parent::relatableQuery($request, $query);
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
    /**
     * @return string
     */
    public static function createLabel()
    {
        return 'Add Product';
    }

    /**
     * Get the text for the create resource button.
     *
     * @return string|null
     */
    public static function createButtonLabel()
    {
        return __('Add :resource', ['resource' => 'Product']);
    }

    /**
     * Get the text for the update resource button.
     *
     * @return string|null
     */
    public static function updateButtonLabel()
    {
        return __('Update :resource', ['resource' => 'Product']);
    }

    /**
     * Return the location to redirect the user after creation.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param \App\Nova\Resource $resource
     * @return string
     */
    public static function redirectAfterCreate(NovaRequest $request, $resource)
    {
        $orderId = $request->request->get('order');
        return "/resources/orders/$orderId";
    }

    /**
     * Return the location to redirect the user after update.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param \App\Nova\Resource $resource
     * @return string
     */
    public static function redirectAfterUpdate(NovaRequest $request, $resource)
    {
        $orderId = $request->request->get('order');
        return "/resources/orders/$orderId";
    }

    /**
     * Return the location to redirect the user after update.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param \App\Nova\Resource $resource
     * @return string
     */
    public static function redirectAfterDelete(NovaRequest $request)
    {
        $orderId = $request->request->get('order');
        return "/resources/orders/$orderId";
    }

    protected static function isProductOutOfQuantity(Product $product, int $quantity, int $offset = 0)
    {
        // @INFO: Offset is used whenever the resource is being updated
        // we have to consider that this should virtually put back as a product
        // inventory, so that we can calculate the real total remaining quantity
        $remaining = $product?->total_inventory_remaining + $offset ?? 0;

        if ($remaining < 1) {
            throw new \Exception("Cannot add $product->name, product is out of stock.");
        }


        if ($quantity > $remaining) {
            throw new \Exception("Cannot add $product->name, product is low on stock.");
        }
    }

    /**
     * Handle any post-validation processing.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    protected static function afterCreationValidation(NovaRequest $request, $validator)
    {
        if (is_null($request->product_id)) {
            throw new \Exception('Please select a product');
        }

        // verify the product quantity
        $product = Product::query()->where('id', '=', $request->product_id)->first();
        $quantity = (int) $request->quantity;

        if (self::isProductOutOfQuantity($product, $quantity)) {
            throw new \Exception("Cannot add $product->name, product is low on stock.");
        }
    }

    /**
     * Handle any post-update validation processing.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    protected static function afterUpdateValidation(NovaRequest $request, $validator)
    {
        if (is_null($request->product_id)) {
            throw new \Exception('Please select a product');
        }

        // verify the product quantity
        $product = Product::query()->where('id', '=', $request->product_id)->first();
        $quantity = (int) $request->quantity;

        if (self::isProductOutOfQuantity($product, $quantity)) {
            throw new \Exception("Cannot add $product->name, product is low on stock.");
        }
    }

    public function authorizedToUpdate(Request $request): bool
    {
        return false;
    }
}
