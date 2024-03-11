<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\SystemConfig;

use Illuminate\Routing\Controller;
use Modules\Core\Constants\Constants;
use Modules\Core\Http\Requests\StoreSystemConfigRequest;
use Modules\Core\Http\Requests\UpdateSystemConfigRequest;
use Modules\Core\Http\Services\SystemConfigService;
use Modules\Core\Http\Services\MobileSettingService;

class SystemConfigController extends Controller
{
    const parentPath = "system_config/";
    const indexPath = self::parentPath."Index";
    const createPath = self::parentPath."Create";
    const createPath1 = self::parentPath."Create1";
    const createPath2 = self::parentPath."Create2";
    const editPath = self::parentPath."Edit";
    const indexRoute = "system_config.index";

    protected $systemConfigService, $dangerFlag,$mobileSettingService;


    public function __construct(SystemConfigService $systemConfigService,MobileSettingService $mobileSettingService)
    {
        $this->systemConfigService = $systemConfigService;
        $this->dangerFlag = Constants::danger;
        $this->mobileSettingService = $mobileSettingService;
    }

    public function index()
    {
        $dataArr = $this->systemConfigService->index();
        $checkPermission = $dataArr["checkPermission"];

        if (!empty($checkPermission)){
            return $checkPermission;
        }
        if (empty($dataArr['system_config']) && empty($dataArr['mobile_setting'])){
            return renderView(self::createPath, $dataArr);
        }
        if (empty($dataArr['system_config'])){
            return renderView(self::createPath1, $dataArr);
        }
        if (empty($dataArr['mobile_setting'])){
            return renderView(self::createPath2, $dataArr);
        }

        return renderView(self::editPath, $dataArr);
    }

    public function store(StoreSystemConfigRequest $request)
    {

        $object = new \stdClass();
        foreach ($request->sysForm as $key => $value)
        {
            $object->$key = $value;
        }
        $systemConfig = $this->systemConfigService->store($object);

        // if have error
        if (isset($systemConfig['error'])){
            $msg = $systemConfig['error'];
            dd( $msg);
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }

        $mbobject = new \stdClass();
        foreach ($request->form as $key => $value)
        {
            $mbobject->$key = $value;
        }
        $mobileSetting = $this->mobileSettingService->store($mbobject);

        // if have error
        if (isset($mobileSetting['error'])){
            $msg = $mobileSetting['error'];
            dd( $msg);
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }
        // $msg = 'The system configuration setting has been updated successfully.';
        $msg = __('core__be_config_success');
        return redirectView(self::indexRoute, $msg);

    }

    public function update(UpdateSystemConfigRequest $request, $id)
    {
        if($request->edit_from == 'create1'){
            $object = new \stdClass();
            foreach ($request->sysForm as $key => $value)
            {
                $object->$key = $value;
            }
            $systemConfig = $this->systemConfigService->store($object);

            // if have error
            if (isset($systemConfig['error'])){
                $msg = $systemConfig['error'];
                return redirectView(self::indexRoute, $msg, $this->dangerFlag);
            }

            $mbobject = new \stdClass();
            foreach ($request->form as $key => $value)
            {
                $mbobject->$key = $value;
            }
            $mobileSetting = $this->mobileSettingService->update($mbobject->id, $mbobject);

            // if have error
            if (isset($mobileSetting['error'])){
                $msg = $mobileSetting['error'];
                // dd( $msg);
                return redirectView(self::indexRoute, $msg, $this->dangerFlag);
            }
        }else if($request->edit_from == 'create2'){
            $object = new \stdClass();
            foreach ($request->sysForm as $key => $value)
            {
                $object->$key = $value;
            }
            $systemConfig = $this->systemConfigService->update($object->id, $object);

            // if have error
            if (isset($systemConfig['error'])){
                $msg = $systemConfig['error'];
                // dd( $msg);
                return redirectView(self::indexRoute, $msg, $this->dangerFlag);
            }

            $mbobject = new \stdClass();
            foreach ($request->form as $key => $value)
            {
                $mbobject->$key = $value;
            }
            $mobileSetting = $this->mobileSettingService->store($mbobject);
            // if have error
            if (isset($mobileSetting['error'])){
                $msg = $mobileSetting['error'];
                // dd( $msg);
                return redirectView(self::indexRoute, $msg, $this->dangerFlag);
            }
        }else{
            $object = new \stdClass();
            foreach ($request->sysForm as $key => $value)
            {
                $object->$key = $value;
            }
            $systemConfig = $this->systemConfigService->update($object->id, $object);

            // if have error
            if (isset($systemConfig['error'])){
                $msg = $systemConfig['error'];
                return redirectView(self::indexRoute, $msg, $this->dangerFlag);
            }

            $mbobject = new \stdClass();
            foreach ($request->form as $key => $value)
            {
                $mbobject->$key = $value;
            }
            $mobileSetting = $this->mobileSettingService->update($mbobject->id, $mbobject);

            // if have error
            if (isset($mobileSetting['error'])){
                $msg = $mobileSetting['error'];
                // dd( $msg);
                return redirectView(self::indexRoute, $msg, $this->dangerFlag);
            }
        }

        // $msg = 'The system configuration setting has been updated successfully.';
        $msg = __('core__be_config_success');
        return redirectView(self::indexRoute, $msg);

    }
}
