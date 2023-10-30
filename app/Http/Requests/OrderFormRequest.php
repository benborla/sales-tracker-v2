<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;

class OrderFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // @INFO: updated this, this should check if the user
        // has the permission to export orders (ORDER_CAN_EXPORT_ORDERS)
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'created_at_from' => 'nullable|date',
            'created_at_to' => 'nullable|date',
            'created_by' => 'nullable|int',
            'order_status' => 'nullable|in:' . implode(',', array_merge([APP_SELECT_ALL], Order::orderStatuses())),
            'payment_status' => 'nullable|in:' . implode(',', array_merge([APP_SELECT_ALL], Order::paymentStatuses())),
            'sales_channel' => 'nullable|string'
        ];
    }
}
