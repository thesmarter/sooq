<?php

namespace Modules\Payment\Http\Controllers\Backend\Controllers\PromotionInAppPurchaseSetting;

use App\Config\ps_constant;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Constants\Constants;
use Modules\Payment\Http\Requests\StorePromoteInAppPurchaseRequest;
use Modules\Payment\Http\Requests\UpdatePromoteInAppPurchaseRequest;
use Modules\Payment\Http\Services\PromotionInAppPurchaseSettingService;

class PromotionInAppPurchaseSettingController extends Controller
{
    const parentPath = "payment/promotion_in_app_purchase/";
    const indexPath = self::parentPath . "Index";
    const createPath = self::parentPath . "Create";
    const editPath = self::parentPath . "Edit";
    const indexRoute = "promotion_in_app_purchase.index";
    const createRoute = "promotion_in_app_purchase.create";
    const editRoute = "promotion_in_app_purchase.edit";

    protected $promotionInAppPurchaseSettingService, $successFlag, $dangerFlag;

    public function __construct(PromotionInAppPurchaseSettingService $promotionInAppPurchaseSettingService)
    {
        $this->promotionInAppPurchaseSettingService = $promotionInAppPurchaseSettingService;

        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;
    }

    public function index(Request $request)
    {
        $dataArr = $this->promotionInAppPurchaseSettingService->index($request);
        if (!permissionControl(Constants::promotionInAppPurchaseModule,ps_constant::readPermission)){
            return redirect()->route('admin.index');
        }
        return renderView(self::indexPath, $dataArr);
    }

    public function store(StorePromoteInAppPurchaseRequest $request)
    {
        $inAppPurchase = $this->promotionInAppPurchaseSettingService->store($request);

        // if have error
        if (isset($inAppPurchase['error'])) {
            $msg = $inAppPurchase['error'];
            return redirectView(self::createRoute, $msg, $this->dangerFlag);
        }

        return redirectView(self::indexRoute, $inAppPurchase);
    }

    public function create()
    {
        if (!permissionControl(Constants::promotionInAppPurchaseModule,ps_constant::createPermission)){
            return redirect()->route('admin.index');
        }
        $dataArr = $this->promotionInAppPurchaseSettingService->create();
        return renderView(self::createPath, $dataArr);
    }

    public function edit($id)
    {
        if (!permissionControl(Constants::promotionInAppPurchaseModule,ps_constant::updatePermission)){
            return redirect()->route('admin.index');
        }
        $dataArr = $this->promotionInAppPurchaseSettingService->edit($id);
        return renderView(self::editPath, $dataArr);
    }

    public function update(UpdatePromoteInAppPurchaseRequest $request, $id)
    {
        $inAppPurchase = $this->promotionInAppPurchaseSettingService->update($id, $request);

        // if have error
        if ($inAppPurchase) {
            $msg = $inAppPurchase;
            return redirectView(self::indexRoute, $msg, $this->dangerFlag, $id);
        }

        return redirect()->back();
    }

    public function destroy($id)
    {
        $dataArr = $this->promotionInAppPurchaseSettingService->destroy($id);
        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }

    public function statusChange($id){
        $dataArr = $this->promotionInAppPurchaseSettingService->makePublishOrUnpublish($id);
        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }
}
