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
[x] - In OrderObserver, capture the total quantity for that item
    - [x] - it should update the inventory of the product after saving the order
    - [x] - in create / update page, add a rule to show an error if the
            entered quantity is greater than the product's inventory
[ ] - multiple images on product
    - [ ] add a media library
    - Link: https://novapackages.com/?search=media&tag=all
[ ] - use reseller_price of the user is a reseller
	[ ] - add a reseller flag in the user table

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

