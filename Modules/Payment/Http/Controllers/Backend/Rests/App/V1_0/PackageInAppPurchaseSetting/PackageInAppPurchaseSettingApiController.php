<?php

namespace Modules\Payment\Http\Controllers\Backend\Rests\App\V1_0\PackageInAppPurchaseSetting;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Payment\Http\Services\PackageInAppPurchaseSettingService;

class PackageInAppPurchaseSettingApiController extends Controller
{
    protected $packageInAppPurchaseSettingService;

    public function __construct(PackageInAppPurchaseSettingService $packageInAppPurchaseSettingService)
    {
        $this->packageInAppPurchaseSettingService = $packageInAppPurchaseSettingService;
    }

    public function index(Request $request)
    {
        $package_in_app_purchases = $this->packageInAppPurchaseSettingService->indexFromApi($request);
        return $package_in_app_purchases;
    }

    public function packagePurchasedCount(Request $request)
    {
        $package_purchased_count = $this->packageInAppPurchaseSettingService->packagePurchasedCountFromApi($request);
        return $package_purchased_count;
    }
}
