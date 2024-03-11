<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\MobileSetting;

use Illuminate\Routing\Controller;
use Modules\Core\Constants\Constants;
use Modules\Core\Http\Services\MobileSettingService;
use Modules\Core\Http\Requests\StoreMobileSettingRequest;
use Modules\Core\Http\Requests\UpdateMobileSettingRequest;

class MobileSettingController extends Controller
{
    const parentPath = "mobile_setting/";
    const indexPath = self::parentPath."Index";
    const createPath = self::parentPath."Create";
    const editPath = self::parentPath."Edit";
    const indexRoute = "mobile_setting.index";
    const createRoute = "mobile_setting.edit";
    const editRoute = "mobile_setting.edit";

    protected $mobileSettingService, $dangerFlag;


    public function __construct(MobileSettingService $mobileSettingService)
    {
        $this->mobileSettingService = $mobileSettingService;
        $this->dangerFlag = Constants::danger;
    }

    public function index()
    {
        $dataArr = $this->mobileSettingService->index();
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        if (empty($dataArr['mobile_setting'])){
            return renderView(self::createPath, $dataArr);
        }
        // return $dataArr;
        return renderView(self::editPath, $dataArr);
    }
    public function test()
    {
        $dataArr = $this->mobileSettingService->index();
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        if (empty($dataArr['mobile_setting'])){
            return renderView(self::createPath, $dataArr);
        }

        return renderView(self::editPath, $dataArr);
    }

    public function store(StoreMobileSettingRequest $request)
    {

        $mobileSetting = $this->mobileSettingService->store($request);

        // if have error
        if (isset($mobileSetting['error'])){
            $msg = $mobileSetting['error'];
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }

        // $msg = 'The mobile configuration setting has been updated successfully.';
        $msg = __('core__be_config_success');
        return redirectView(self::indexRoute, $msg);

    }

    public function update(UpdateMobileSettingRequest $request, $id)
    {
        // dd($request->all());
        $mobileSetting = $this->mobileSettingService->update($id, $request);

        // if have error
        if (isset($mobileSetting['error'])){
            $msg = $mobileSetting['error'];
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }

        // $msg = 'The mobile configuration setting has been updated successfully.';
        $msg = __('core__be_config_success');
        return redirectView(self::indexRoute, $msg);

    }
}
