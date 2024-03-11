<?php

namespace Modules\StoreFront\VendorPanel\Transformers\Api\App\V1_0\Order;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\StoreFront\VendorPanel\Entities\OrderItem;
use stdClass;

class OrderSummaryApiResource extends JsonResource
{

    public function toArray($request)
    {

        $itemInfoArr = [];
        $currencySymbol = $this->vendorTransaction?->currency?->currency_symbol;
        $totalAmount = $currencySymbol . $this->vendorTransaction?->payment_amount;

        foreach($this->orderItems as $orderItem){
            $itemInfoObj = new stdClass;
            $itemInfoObj->item_id = (string)$orderItem->item_id;
            $itemInfoObj->item_name = (string)$orderItem->item->title;
            $itemInfoObj->original_price = (string)$currencySymbol.$orderItem->original_price;
            $itemInfoObj->discount_price = (string)$this->vendorTransaction?->currency?->currency_symbol.$orderItem->discount_price;
            $itemInfoObj->quantity = (string)$orderItem->quantity;
            $itemInfoObj->sub_total = (string)$this->vendorTransaction?->currency?->currency_symbol.$orderItem->sub_total;

            array_push($itemInfoArr, $itemInfoObj);
        }

        $orderSummaryObj = new stdClass();
        $orderSummaryObj->order_code = (string)$this->order_code;
        $orderSummaryObj->shipping_first_name = (string)$this->shippingAndBilling->shipping_first_name;
        $orderSummaryObj->shipping_last_name = (string)$this->shippingAndBilling->shipping_last_name;
        $orderSummaryObj->shipping_email = (string)$this->shippingAndBilling->shipping_email;
        $orderSummaryObj->shipping_phone_no = (string)$this->shippingAndBilling->shipping_phone_no;
        $orderSummaryObj->shipping_address = (string)$this->shippingAndBilling->shipping_address;
        $orderSummaryObj->shipping_country = (string)$this->shippingAndBilling->shipping_country;
        $orderSummaryObj->shipping_state = (string)$this->shippingAndBilling->shipping_state;
        $orderSummaryObj->shipping_city = (string)$this->shippingAndBilling->shipping_city;
        $orderSummaryObj->shipping_postal_code = (string)$this->shippingAndBilling->shipping_postal_code;
        $orderSummaryObj->is_save_shipping_info_for_next_time = (string)$this->shippingAndBilling->is_save_shipping_info_for_next_time;
        $orderSummaryObj->billing_first_name = (string)$this->shippingAndBilling->billing_first_name;
        $orderSummaryObj->billing_last_name = (string)$this->shippingAndBilling->billing_last_name;
        $orderSummaryObj->billing_email = (string)$this->shippingAndBilling->billing_email;
        $orderSummaryObj->billing_phone_no = (string)$this->shippingAndBilling->billing_phone_no;
        $orderSummaryObj->billing_address = (string)$this->shippingAndBilling->billing_address;
        $orderSummaryObj->billing_country = (string)$this->shippingAndBilling->billing_country;
        $orderSummaryObj->billing_state = (string)$this->shippingAndBilling->billing_state;
        $orderSummaryObj->billing_city = (string)$this->shippingAndBilling->billing_city;
        $orderSummaryObj->billing_postal_code = (string)$this->shippingAndBilling->billing_postal_code;
        $orderSummaryObj->is_save_billing_info_for_next_time = (string)$this->shippingAndBilling->is_save_billing_info_for_next_time;
        $orderSummaryObj->payment_method = (string)$this->vendorTransaction?->payment_method;
        $orderSummaryObj->payment_status = (string)$this->vendorTransaction?->vendorPaymentStatus->name;
        $orderSummaryObj->payment_date = isset($this->vendorTransaction->payment_date) ? (string)\Carbon\Carbon::parse($this->vendorTransaction->payment_date)->format("M d, Y") : "";
        $orderSummaryObj->vendor_name = (string)$this->vendorTransaction?->vendor->name;
        $orderSummaryObj->item_info = $itemInfoArr;
        $orderSummaryObj->total = (string)$totalAmount;

        return [
            'order_summary' => $orderSummaryObj,
        ];
    }
}
