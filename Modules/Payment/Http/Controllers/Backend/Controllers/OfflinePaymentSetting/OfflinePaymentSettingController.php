<?php

namespace Modules\Payment\Http\Controllers\Backend\Controllers\OfflinePaymentSetting;

use App\Config\ps_constant;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Constants\Constants;
use Modules\Payment\Http\Requests\StoreOfflinePaymentSettingRequest;
use Modules\Payment\Http\Requests\UpdateOfflinePaymentSettingRequest;
use Modules\Payment\Http\Services\OfflinePaymentSettingService;
class OfflinePaymentSettingController extends Controller
{
    const parentPath = "payment/offline_payment_setting/";
    const indexPath = self::parentPath . "Index";
    const createPath = self::parentPath . "Create";
    const editPath = self::parentPath . "Edit";
    const indexRoute = "offline_payment_setting.index";
    const createRoute = "offline_payment_setting.create";
    const editRoute = "offline_payment_setting.edit";

    protected $offlinePaymentSettingService, $successFlag, $dangerFlag;

    public function __construct(OfflinePaymentSettingService $offlinePaymentSettingService)
    {
        $this->offlinePaymentSettingService = $offlinePaymentSettingService;

        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;
    }

    public function index(Request $request)
    {
        $dataArr = $this->offlinePaymentSettingService->index($request);
        if (!permissionControl(Constants::offlinePaymentSettingModule,ps_constant::readPermission)){
            return redirect()->route('admin.index');
        }
        return renderView(self::indexPath, $dataArr);
    }

    public function store(StoreOfflinePaymentSettingRequest $request)
    {
        $offlinePayment = $this->offlinePaymentSettingService->store($request);
        // if have error
        if (isset($offlinePayment['error'])) {
            $msg = $offlinePayment['error'];
            return redirectView(self::createRoute, $msg, $this->dangerFlag);
        }
        return redirectView(self::indexRoute, $offlinePayment);
    }

    public function create()
    {
        if (!permissionControl(Constants::offlinePaymentSettingModule,ps_constant::createPermission)){
            return redirect()->route('admin.index');
        }
        return renderView(self::createPath);
    }

    public function edit($id)
    {
        if (!permissionControl(Constants::offlinePaymentSettingModule,ps_constant::updatePermission)){
            return redirect()->route('admin.index');
        }
        $dataArr = $this->offlinePaymentSettingService->edit($id);
        return renderView(self::editPath, $dataArr);
    }

    public function update(UpdateOfflinePaymentSettingRequest $request, $id)
    {
        $offlinePayment = $this->offlinePaymentSettingService->update($id, $request);
        // if have error
        if ($offlinePayment) {
            $msg = $offlinePayment;
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }
        return redirect()->back();

    }

    public function destroy($id)
    {
        if (!permissionControl(Constants::offlinePaymentSettingModule,ps_constant::deletePermission)){
            return redirect()->route('admin.index');
        }
        $dataArr = $this->offlinePaymentSettingService->destroy($id);
        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }

    public function statusChange($id){
        $dataArr = $this->offlinePaymentSettingService->makePublishOrUnpublish($id);
        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }
}
