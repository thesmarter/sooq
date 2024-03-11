<?php

namespace Modules\Payment\Http\Requests;

use App\Rules\CheckUniqueValueColForPaymentInfo;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Constants\Constants;

class StoreVendorSubscriptionPlanRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'in_app_purchase_prd_id' => 'required',
            'title' => ["required", new CheckUniqueValueColForPaymentInfo(null, Constants::vendorSubscriptionPlanPaymentId)],
            'sale_price' => 'required',
            'discount_price' => 'required',
            'duration' => 'required',
            'currency_id' => 'required'
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
