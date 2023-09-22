# Todo:

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
[ ] - In OrderObserver, capture the total quantity for that item
    - [ ] - it should update the inventory of the product after saving the order
    - [ ] - in create / update page, add a rule to show an error if the
            entered quantity is greater than the product's inventory
[ ] - multiple images on product
    - [ ] add a media library
[ ] - use reseller_price of the user is a reseller
	[ ] - add a reseller flag in the user table


### Reports
[ ] - export number of total boxes shipped on a specific date.
	[ ] - it should be an excel file.

