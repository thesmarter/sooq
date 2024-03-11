<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\FrontendSetting;

use Inertia\Inertia;
use Illuminate\Routing\Controller;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\FrontendSetting;
use Modules\Core\Http\Services\FrontendSettingService;
use Modules\Core\Http\Requests\StoreFrontendSettingRequest;
use Modules\Core\Http\Requests\UpdateFrontendSettingRequest;
use Illuminate\Http\Request;

class FrontendSettingController extends Controller
{

    const parentPath = "frontend_setting/";
    const indexPath = self::parentPath."Index";
    const createPath = self::parentPath."Create";
    const editPath = self::parentPath."Edit";
    const indexRoute = "frontend_setting.index";

    protected $frontendSettingService, $dangerFlag;


    public function __construct(FrontendSettingService $frontendSettingService)
    {
        $this->frontendSettingService = $frontendSettingService;
        $this->dangerFlag = Constants::danger;
    }

    public function index()
    {
        $dataArr = $this->frontendSettingService->index();
        if (empty($dataArr['frontend_setting'])){
            return renderView(self::createPath, $dataArr);
        }
        return renderView(self::editPath, $dataArr);
    }

    public function store(StoreFrontendSettingRequest $request)
    {

        $frontendSetting = $this->frontendSettingService->store($request);

        // if have error
        if (isset($frontendSetting['error'])){
            $msg = $frontendSetting['error'];
            // dd($msg );
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }

        $msg =  __('core__be_config_success');
        return redirectView(self::indexRoute, $msg);
    }

    public function update(UpdateFrontendSettingRequest $request, $id)
    {
        $frontendSetting= $this->frontendSettingService->update($id,$request);

        // if have error
        if (isset($frontendSetting['error'])){
            $msg = $frontendSetting['error'];
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }
        $msg =  __('core__be_config_success');
        return redirectView(self::indexRoute, $msg);

    }

    public function languageRefresh(){
        $msg = "Frontend Language is finished Successfully";
        generateAllFeLanguages();
        return redirectView(self::indexRoute, $msg, "langSuccess");
    }

    public function colorGenerate(Request $request){
        
        $frontendSetting = $this->frontendSettingService->generateColors($request);
        $msg = "Color Generated Successfully";
        return redirectView(self::indexRoute, $msg, "colorSuccess");
    }
}
