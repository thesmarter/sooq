<?php

namespace Modules\Payment\Http\Controllers\Backend\Controllers\VendorSubscriptionPlanSetting;

use App\Config\ps_constant;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Constants\Constants;
use Modules\Payment\Http\Requests\StorePackageInAppPurchaseRequest;
use Modules\Payment\Http\Requests\StoreVendorSubscriptionPlanRequest;
use Modules\Payment\Http\Requests\UpdatePackageInAppPurchaseRequest;
use Modules\Payment\Http\Requests\UpdateVendorSubscriptionPlanRequest;
use Modules\Payment\Http\Services\VendorSubscriptionPlanSettingService;

class VendorSubscriptionPlanSettingController extends Controller
{
    const parentPath = "payment/vendor_subscription_plan/";
    const indexPath = self::parentPath . "Index";
    const createPath = self::parentPath . "Create";
    const editPath = self::parentPath . "Edit";
    const indexRoute = "vendor_subscription_plan.index";
    const createRoute = "vendor_subscription_plan.create";
    const editRoute = "vendor_subscription_plan.edit";

    protected $vendorSubscriptionPlanSettingService, $successFlag, $dangerFlag;

    public function __construct(VendorSubscriptionPlanSettingService $vendorSubscriptionPlanSettingService)
    {
        $this->vendorSubscriptionPlanSettingService = $vendorSubscriptionPlanSettingService;

        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;
    }

    public function index(Request $request)
    {
        $dataArr = $this->vendorSubscriptionPlanSettingService->index($request);
        if (!permissionControl(Constants::vendorSubscriptionPlanModule,ps_constant::readPermission)){
            return redirect()->route('admin.index');
        }
        return renderView(self::indexPath, $dataArr);
    }

    public function store(StoreVendorSubscriptionPlanRequest $request)
    {
        $inAppPurchase = $this->vendorSubscriptionPlanSettingService->store($request);
        // if have error
        if (isset($inAppPurchase['error'])) {
            $msg = $inAppPurchase['error'];
            return $msg;
            return redirectView(self::createRoute, $msg, $this->dangerFlag);
        }
        return redirectView(self::indexRoute, $inAppPurchase);
    }

    public function create()
    {
        if (!permissionControl(Constants::vendorSubscriptionPlanModule,ps_constant::createPermission)){
            return redirect()->route('admin.index');
        }
        $dataArr = $this->vendorSubscriptionPlanSettingService->create();
        return renderView(self::createPath, $dataArr);
    }

    public function edit($id)
    {
        if (!permissionControl(Constants::vendorSubscriptionPlanModule,ps_constant::updatePermission)){
            return redirect()->route('admin.index');
        }
        $dataArr = $this->vendorSubscriptionPlanSettingService->edit($id);
        return renderView(self::editPath, $dataArr);
    }

    public function update(UpdateVendorSubscriptionPlanRequest $request, $id)
    {
        $inAppPurchase = $this->vendorSubscriptionPlanSettingService->update($id, $request);

        // if have error
        if ($inAppPurchase) {
            $msg = $inAppPurchase;
            return redirectView(self::indexRoute, $msg, $this->dangerFlag, $id);
        }
        return redirect()->back();

    }

    public function destroy($id)
    {
        $dataArr = $this->vendorSubscriptionPlanSettingService->destroy($id);
        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }

    public function statusChange($id){
        $dataArr = $this->vendorSubscriptionPlanSettingService->makePublishOrUnpublish($id);
        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }

    public function handleIsMostPopularPlan($id){
        $dataArr = $this->vendorSubscriptionPlanSettingService->handleIsMostPopularPlan($id);
        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }
}
