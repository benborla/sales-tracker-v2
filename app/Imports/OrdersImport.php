<?php

namespace App\Imports;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Role;
use App\Models\Store;
use App\Models\User;
use App\Models\UserInformation;
use App\Models\UserStore;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class OrdersImport implements ToModel, WithProgressBar, WithHeadingRow, WithCalculatedFormulas
{
    use Importable;

    private const NONE_VALUE = 'None';
    private const DEFAULT_STORE_ID = 1;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        if (is_null($row['customer_name'])) {
            return;
        }

        $this->createCustomer($row);
        /** INFO: get customer id (user_id) **/
        $user = User::where('name', '=', $row['customer_name'])->first();

        if ($user) {
            /** @INFO: Cast scientific notation into integer, but retain value if value is equal to "none" **/
            $referenceId = $row['reference_id'] === self::NONE_VALUE ? self::NONE_VALUE : (int) $row['reference_id'];
            /** @INFO: stored in a variable just in case we need to append more information **/
            $notes = $row['notes'];
            $orderStatus = $row['order_status'] === 'Active' ? Order::ORDER_STATUS_FULFILLED : Order::ORDER_STATUS_FAILED;
            /** @INFO: Remove whitespace in invoice_id **/
            $invoiceId = str_replace(' ', '', $row['invoice_id']);

            $order = new Order([
                'user_id' => $user->id,
                'store_id' => self::DEFAULT_STORE_ID,
                'email' => $user->email,
                'invoice_id' => $invoiceId,
                'reference_id' => $referenceId,
                'num_of_boxes_shipped' => $row['num_of_boxes_shipped'],
                'tax_fee' => $row['tax_fee'],
                'shipping_fee' => $row['shipping_fee'],
                'shipping_fee' => $row['shipping_fee'],
                'item_type' => strtolower($row['item_type']),
                'intermediary_fees' => $row['intermediary_fees'],
                'created_at' => $this->formatDate($row['created_at']),
                'updated_at' => $this->formatDate($row['created_at']),
                'created_by' => 3,
                'updated_by' => 3,
                'notes' => $notes,
                'order_status' => strtolower($orderStatus),
                'payment_status' => Order::PAYMENT_STATUS_SUCCESS,
                'payment_payload' => '{}',
                'sales_channel' => $row['sales_channel'],
                'is_approved' => true,
                'price_based_on' => Order::PRICE_BASED_ON_RETAIL,
                'tracking_type' => $row['item_type'],
                'tracking_reference' => $row['tracking_reference'],
                'shipper' => $row['shipping_method'],
                'total_sales' => $row['total_sales'],
            ]);

            $order->save();

            // assign order to order item
            $product = Product::query()
                ->where('name', 'like', '%' . $row['order_product_name'] . '%')
                ->andWhere('store_id', '=', self::DEFAULT_STORE_ID)
                ->first();
            if (!is_null($product)) {
                $order->orderItems()->create([
                    'product_id' => $product->id,
                    'quantity' => (int) $row['quantity']
                ]);
            }

            return $order;
        }
    }

    private function createCustomer(array $row): bool
    {
        $success = false;
        $name = $this->deconstructName($row['customer_name']);
        $email = $row['email'];

        if ($email === self::NONE_VALUE) {
            $email = strtolower($name['firstName']) . '_' . strtolower($name['lastName']) . '_autogenerate@infinitenaturals.com';
        }

        $user = User::updateOrCreate(
            /** @INFO: check whether this email exists or not **/
            [
                'email' => $email,
            ],
            /** @INFO: payload to insert or update **/
            [
                'name' => $row['customer_name'],
                'email' => $email,
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'remember_token' => \Illuminate\Support\Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        if ($user->id) {
            $success = UserInformation::updateOrCreate([
                'user_id' => $user->id,
                'type' => UserInformation::USER_TYPE_CUSTOMER,
                'first_name' => $name['firstName'],
                'last_name' => $name['lastName'],
                'middle_name' => $name['middleName'],
                'telephone_number' => $row['telephone_number'],
                'mobile_number' => $row['telephone_number'],
                'is_active' => true,
                'notes' => 'Auto-generated via order import automation',
                'billing_address' => $row['billing_address'],
                'billing_address_city' => $row['billing_city'],
                'billing_address_state' => $row['billing_state'],
                'billing_address_zipcode' => $row['billing_zipcode'],
                'billing_address_country' => 'USA',

                'shipping_address' => $row['shipping_address'],
                'shipping_address_city' => $row['shipping_city'],
                'shipping_address_state' => $row['shipping_state'],
                'shipping_address_zipcode' => $row['shipping_zipcode'],
                'shipping_address_country' => 'USA',
            ])->count();

            /** @INFO: Only assign if the query is successful **/
            if ($success) {
                // Find the information of the store "Infinite Naturals"
                $store = Store::where('name', '=', 'Infinite Naturals')->first();
                $role = Role::where('slug', '=', UserInformation::USER_TYPE_CUSTOMER)->first();

                if ($store) {
                    // Assign customer to a store
                    UserStore::updateOrCreate([
                        'user_id' => $user->id,
                        'store_id' => $store->id
                    ]);
                }

                // assign CUSTOMER role to customer
                // @INFO: Had to do it this way because when using the ->assignRole
                // method, it's giving a duplicate error, and I can't find the
                // model for the `role_user` table. This is just a workaround
                DB::table('role_user')->insertOrIgnore([
                    'user_id' => $user->id,
                    'role_id' => $role->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return (bool) $success;
    }

    private function formatDate(string $date): string
    {
        return Carbon::instance(Date::excelToDateTimeObject($date))->format('Y-m-d');
    }

    private function deconstructName($fullName)
    {
        // Split the full name into parts using whitespace as the delimiter
        $nameParts = preg_split('/\s+/', $fullName);

        // Initialize variables to store first name, middle name, and last name
        $firstName = '';
        $middleName = '';
        $lastName = '';

        // Determine the number of name parts
        $numParts = count($nameParts);

        // If there's only one part, it's assumed to be the last name
        if ($numParts == 1) {
            $lastName = $nameParts[0];
        } elseif ($numParts == 2) {
            // If there are two parts, assume the first part is the first name, and the second part is the last name
            $firstName = $nameParts[0];
            $lastName = $nameParts[1];
        } else {
            // If there are more than two parts, the first part is the first name, and the last part is the last name.
            // Any parts in between are considered the middle name.
            $firstName = $nameParts[0];
            $lastName = $nameParts[$numParts - 1];

            // Concatenate the middle parts to form the middle name
            for ($i = 1; $i < $numParts - 1; $i++) {
                $middleName .= $nameParts[$i];
                if ($i < $numParts - 2) {
                    $middleName .= ' ';
                }
            }
        }

        // Return the deconstructed name components
        return [
            'firstName' => $firstName,
            'middleName' => $middleName,
            'lastName' => $lastName,
        ];
    }
}
