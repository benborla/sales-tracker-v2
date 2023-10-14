# Todo:

## Reports

[ ] -  Generate report either CSV or PDF
[ ] - Generate order report via Team with customer's information, shipping, and other order relevant fields
[ ] - Can generate daily, weekly, monthly, yearly and how many orders and shipment by
store.

## Dashboard

Admin Only
[ ] - Show number of stores
[ ] - Show number of staffs
[ ] - Show number of customers
[ ] - Show total number of orders

--

In Order index page
[ ] - add a card that will show the total number of orders (can only be accessed if user is admin, or user has ORDER_CAN_VIEW_TOTAL_ORDER_REPORTS permission)

[x] - Need to fix inconsistencies on order total payable
    - need to see the computation of total payable in updates
    - and on creation
[x] - add policies for orders
[x] - find a way to make store searchable in product page
[x] - add policies for products
[x] - add number of orders in product via using this package:  saumini/count
[x] - fixes on user and user information
[x] - add identifier if user is a staff or a customer
[x] - hide certain fields that should only be shown to staffs
[x] - add My Profile link 
[x] - add Change Password link
[x] - remove Agent in Orders, this should be automatically fill by whoever created the order
[x] - verify order prices

!! Priority
[x] - fix error on http://sales-tracker.local:8880/resources/roles/8
        - Not unique table/alias: role_user
[x] - Fix user table when vieweing as Team Leader
    - it should show all the members of the team including the team leader
[x] - Work with policies

### Orders
[x] - reference number should be manual input
[x]  fix the time format

[x] - add inventory in products (total_inventory_remaining)
[x] - add sku in products
[x] - add Weight (oz, fl/oz, ml, oz)
[x] - add manufactured date
[x] - add size
[x] - add made_from
[x] - no need for shipping info in products

[x] - add us_price = retail_price
[x] - add wholesale_price = reseller_price

### Products & Orders
[x] - In OrderObserver, capture the total quantity for that item
    - [x] - it should update the inventory of the product after saving the order
    - [x] - in create / update page, add a rule to show an error if the
            entered quantity is greater than the product's inventory
[x] - multiple images on product
[x] - Add created_by and updated_by, approved_by fields in order
[x] - Add is_approved column for orders
[x] - Test new order columns
[x] - Is Approve, need to add this
    - need to test

# Inventory
[x] - When creating an order, product inventory should be deducted based on
the set quantity for that product
[ ] - When updating an order items (Disabled editing for now)
    [ ] - it should put back the product inventory for recomputation
    [ ] - price should be updated
    [ ] - inventory should not be negative
[x] - when deleting an order item, it should put back the product remaining inventory based on the order item quantity

### Reports
[ ] - export number of total boxes shipped on a specific date.
	[ ] - it should be an excel file.



### Resource:
https://novapackages.com/packages/abordage/nova-html-card


## Rule based on value
```php
use Illuminate\Validation\Rule;

Text::make('Email')->required(function ($request) {
    return $this->account_locked !== true;
})->rules([Rule::requiredIf($this->account_locked)]),
```

## Hide certain fields based on permission
```php
        Text::make('Name')
                ->sortable()
                ->canSee(function ($request) {
                    return $request->user()->can('viewProfile', $this);
                }),
```
