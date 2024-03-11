<?php

namespace Modules\StoreFront\VendorPanel\Transformers\Api\App\V1_0\Vendor;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\VendorPayment;
use Modules\Core\Entities\VendorPaymentInfo;
use Modules\StoreFront\VendorPanel\Entities\OrderItem;
use stdClass;

class VendorConfigInfoApiResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            "vendor_stripe_enabled" => (string) VendorPayment::where(VendorPayment::paymentId, Constants::stripePaymentId)->where(VendorPayment::vendorId, $request->vendor_id)->first()->status,
            "vendor_paypal_enabled" => (string) VendorPayment::where(VendorPayment::paymentId, Constants::paypalPaymentId)->where(VendorPayment::vendorId, $request->vendor_id)->first()->status,
            "vendor_razor_enabled" => (string) VendorPayment::where(VendorPayment::paymentId, Constants::razorPaymentId)->where(VendorPayment::vendorId, $request->vendor_id)->first()->status,
            "vendor_paystack_enabled" => (string) VendorPayment::where(VendorPayment::paymentId, Constants::paystackPaymentId)->where(VendorPayment::vendorId, $request->vendor_id)->first()->status,
            "vendor_razor_key" => (string) VendorPaymentInfo::select('value')->where(VendorPaymentInfo::coreKeysId, Constants::razorKey)->where(VendorPaymentInfo::vendorId, $request->vendor_id)->first(),
            "vendor_stripe_publishable_key" => (string) VendorPaymentInfo::select('value')->where(VendorPaymentInfo::coreKeysId, Constants::stripePublishableKey)->where(VendorPaymentInfo::vendorId, $request->vendor_id)->first(),
            "vendor_paystack_key" => (string) VendorPaymentInfo::select('value')->where(VendorPaymentInfo::coreKeysId, Constants::paystackKey)->where(VendorPaymentInfo::vendorId, $request->vendor_id)->first(),
        ];
    }
}
