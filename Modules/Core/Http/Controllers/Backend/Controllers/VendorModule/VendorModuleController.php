<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\VendorModule;

use Inertia\Inertia;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\CoreMenu;
use Modules\Core\Entities\CoreMenuGroup;
use Modules\Core\Entities\CoreSubMenuGroup;
use Modules\Core\Http\Services\CoreMenuService;
use Modules\Core\Http\Requests\StoreVendorModuleRequest;
use Modules\Core\Http\Requests\UpdateVendorModuleRequest;
use Illuminate\Http\Request;
use Modules\Core\Http\Services\VendorModuleService;

class VendorModuleController extends Controller
{

    const parentPath = "vendor_module/";
    const indexPath = self::parentPath."Index";
    const createPath = self::parentPath."Create";
    const editPath = self::parentPath."Edit";
    const indexRoute = "vendor_module_registering.index";
    const createRoute = "vendor_module_registering.create";
    const editRoute = "vendor_module_registering.edit";

    protected $moduleService, $successFlag, $dangerFlag, $csvFile, $warningFlag;

    public function __construct(VendorModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;
        $this->warningFlag = Constants::warning;
        $this->csvFile = Constants::csvFile;
    }

    public function index(Request $request)
    {
        $dataArr = $this->moduleService->index($request);
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        // dd($dataArr);
        return renderView(self::indexPath, $dataArr);
    }

    public function create()
    {
        $dataArr = $this->moduleService->create();
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        return renderView(self::createPath, $dataArr);
    }

    public function store(StoreVendorModuleRequest $request)
    {
        $module = $this->moduleService->store($request);

        // if have error
        if (isset($module['error'])){
            $msg = $module['error'];
            return $msg;
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }

        return redirect()->back();
    }

//    public function show(CoreModule $module)
//    {
//        return redirect()->route('module.edit', $module);
//    }

    public function edit($moduleId)
    {
        $dataArr = $this->moduleService->edit($moduleId);
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        return renderView(self::editPath, $dataArr);
    }


    public function update(UpdateVendorModuleRequest $request, $moduleId)
    {
        $module = $this->moduleService->update($moduleId, $request);

        // if have error
        if (isset($module['error'])){
            $msg = $module['error'];
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }

        return redirect()->back();
    }

    public function destroy($moduleId)
    {
        $dataArr = $this->moduleService->destroy($moduleId);
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }

    public function statusChange($moduleId){
        $dataArr = $this->moduleService->statusChange($moduleId);
        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }
    public function screenDisplayUiStore(Request $request) {
        $parameter = ['page' => $request->current_page];
        $this->moduleService->makeColumnHideShown($request);
        $msg = __('core__be_screen_display_ui_updated');
        return redirect()->back();
    }

}
