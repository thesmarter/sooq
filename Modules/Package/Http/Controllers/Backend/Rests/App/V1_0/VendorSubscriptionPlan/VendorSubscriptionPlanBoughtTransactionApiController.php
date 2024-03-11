<?php

namespace Modules\Package\Http\Controllers\Backend\Rests\App\V1_0\VendorSubscriptionPlan;

use Modules\Core\Constants\Constants;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Translation\Translator;
use Modules\Package\Http\Services\VendorSubscriptionPlanBoughtTransactionService;

class VendorSubscriptionPlanBoughtTransactionApiController extends Controller
{
    protected $translator, $vendorSubscriptionPlanBoughtTransactionService;

    public function __construct(Translator $translator, VendorSubscriptionPlanBoughtTransactionService $vendorSubscriptionPlanBoughtTransactionService)
    {
        $this->translator = $translator;
        $this->vendorSubscriptionPlanBoughtTransactionService = $vendorSubscriptionPlanBoughtTransactionService;
    }

    public function store(Request $request)
    {
        //prepare validation
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'vendor_id' => 'required|exists:psx_vendors,id',
            'subscription_plan_id' => 'required|exists:psx_payment_infos,id',
            'payment_method' => 'required',
            'price' => 'required',
        ]);

        if($request->language_symbol){
            $this->translator->setLocale($request->language_symbol);
            $validator->setTranslator($this->translator);
        }

        if ($validator->fails()) {
            return responseMsgApi(implode("\n", Arr::flatten($validator->getMessageBag()->getMessages())), Constants::badRequestStatusCode);
        }
        /// validation end
        $package = $this->vendorSubscriptionPlanBoughtTransactionService->getPaymentInfoByPackageId($request->subscription_plan_id);
        if($package->payment_id != Constants::vendorSubscriptionPlanPaymentId){
            return responseMsgApi('package__pkg_invalid', Constants::badRequestStatusCode);
        }
        $packages = $this->vendorSubscriptionPlanBoughtTransactionService->storeFromApi($request);
        return $packages;
    }
}
