<?php

namespace Modules\Payment\Http\Controllers\Backend\Rests\App\V1_0\VendorSubscriptionPlanSetting;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Payment\Http\Services\VendorSubscriptionPlanSettingService;

class VendorSubscriptionPlanSettingApiController extends Controller
{
    protected $vendorSubscriptionPlanSettingService;

    public function __construct(VendorSubscriptionPlanSettingService $vendorSubscriptionPlanSettingService)
    {
        $this->vendorSubscriptionPlanSettingService = $vendorSubscriptionPlanSettingService;
    }

    public function index(Request $request)
    {
        $vendor_subscription_plans = $this->vendorSubscriptionPlanSettingService->indexFromApi($request);
        return $vendor_subscription_plans;
    }

}
