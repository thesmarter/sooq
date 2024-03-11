<?php

namespace Modules\Payment\Http\Controllers\Backend\Controllers\PackageInAppPurchaseSetting;

use App\Config\ps_constant;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Constants\Constants;
use Modules\Payment\Http\Requests\StorePackageInAppPurchaseRequest;
use Modules\Payment\Http\Requests\UpdatePackageInAppPurchaseRequest;
use Modules\Payment\Http\Services\PackageInAppPurchaseSettingService;

class PackageInAppPurchaseSettingController extends Controller
{
    const parentPath = "payment/package_in_app_purchase/";
    const indexPath = self::parentPath . "Index";
    const createPath = self::parentPath . "Create";
    const editPath = self::parentPath . "Edit";
    const indexRoute = "package_in_app_purchase.index";
    const createRoute = "package_in_app_purchase.create";
    const editRoute = "package_in_app_purchase.edit";

    protected $packageInAppPurchaseSettingService, $successFlag, $dangerFlag;

    public function __construct(PackageInAppPurchaseSettingService $packageInAppPurchaseSettingService)
    {
        $this->packageInAppPurchaseSettingService = $packageInAppPurchaseSettingService;

        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;
    }

    public function index(Request $request)
    {
        $dataArr = $this->packageInAppPurchaseSettingService->index($request);
        if (!permissionControl(Constants::packageInAppPurchaseModule,ps_constant::readPermission)){
            return redirect()->route('admin.index');
        }
        return renderView(self::indexPath, $dataArr);
    }

    public function store(StorePackageInAppPurchaseRequest $request)
    {
        $inAppPurchase = $this->packageInAppPurchaseSettingService->store($request);
        // if have error
        if (isset($inAppPurchase['error'])) {
            $msg = $inAppPurchase['error'];
            return redirectView(self::createRoute, $msg, $this->dangerFlag);
        }
        return redirectView(self::indexRoute, $inAppPurchase);
    }

    public function create()
    {
        if (!permissionControl(Constants::packageInAppPurchaseModule,ps_constant::createPermission)){
            return redirect()->route('admin.index');
        }
        $dataArr = $this->packageInAppPurchaseSettingService->create();
        return renderView(self::createPath, $dataArr);
    }

    public function edit($id)
    {
        if (!permissionControl(Constants::packageInAppPurchaseModule,ps_constant::updatePermission)){
            return redirect()->route('admin.index');
        }
        $dataArr = $this->packageInAppPurchaseSettingService->edit($id);
        return renderView(self::editPath, $dataArr);
    }

    public function update(UpdatePackageInAppPurchaseRequest $request, $id)
    {
        $inAppPurchase = $this->packageInAppPurchaseSettingService->update($id, $request);

        // if have error
        if ($inAppPurchase) {
            $msg = $inAppPurchase;
            return redirectView(self::indexRoute, $msg, $this->dangerFlag, $id);
        }
        return redirect()->back();

    }

    public function destroy($id)
    {
        $dataArr = $this->packageInAppPurchaseSettingService->destroy($id);
        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }

    public function statusChange($id){
        $dataArr = $this->packageInAppPurchaseSettingService->makePublishOrUnpublish($id);
        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }
}
