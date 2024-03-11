<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\CoreMenu;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Modules\Core\Entities\Module;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\CoreMenu;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\CoreMenuGroup;
use Illuminate\Support\Facades\Validator;
use Modules\Core\Entities\CoreSubMenuGroup;
use Modules\Core\Http\Services\CoreMenuService;
use Modules\Core\Http\Requests\StoreMenuRequest;
use Modules\Core\Http\Requests\UpdateMenuRequest;
use Modules\Core\Http\Requests\StoreModuleRequest;
use Modules\Core\Http\Requests\UpdateModuleRequest;
use Modules\Core\Http\Services\CoreFieldFilterSettingService;

class CoreMenuController extends Controller
{

    const parentPath = "core_menu/";
    const indexPath = self::parentPath."Index";
    const createPath = self::parentPath."Create";
    const editPath = self::parentPath."Edit";
    const indexRoute = "menu.index";
    const createRoute = "menu.create";
    const editRoute = "menu.edit";

    protected $moduleService, $successFlag, $dangerFlag, $csvFile, $warningFlag, $code, $coreFieldFilterSettingService;

    public function __construct(CoreMenuService $moduleService, CoreFieldFilterSettingService $coreFieldFilterSettingService)
    {
        $this->moduleService = $moduleService;
        $this->coreFieldFilterSettingService = $coreFieldFilterSettingService;
        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;
        $this->warningFlag = Constants::warning;
        $this->csvFile = Constants::csvFile;
        $this->code = Constants::coreModule;
    }

    public function index(Request $request)
    {
        $dataArr = $this->moduleService->index($request);
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }
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

    public function store(Request $request)
    {
        // validation start
        $errors = validateForCustomField($this->code,$request->menu_relation);

        $coreFieldsIds = [];
        $errors = [];

        $cond['module_name'] = $this->code;
        $cond['mandatory'] = 1;
        $cond['is_core_field'] = 1;

        $coreFields = $this->coreFieldFilterSettingService->getCoreFieldsWithConds($cond);

        foreach ($coreFields as $key => $value){
            if (str_contains($value->field_name,"@@")) {
                $originFieldName = strstr($value->field_name,"@@",true);
            } else {
                $originFieldName = $value->field_name;
            }
            array_push($coreFieldsIds,$originFieldName);

        }

        $validationArr = [];

        if(in_array('module_name',$coreFieldsIds)){
            $validationArr['module_name'] = 'required|min:3|unique:psx_core_menus,module_name,';
        }

        if(in_array('core_sub_menu_group_id',$coreFieldsIds)){
            $validationArr['core_sub_menu_group_id'] = 'required';
        }

        if(in_array('module_desc',$coreFieldsIds)){
            $validationArr['module_desc'] = 'required|unique:psx_core_menus,module_desc';
        }

        if(in_array('module_lang_key',$coreFieldsIds)){
            $validationArr['module_lang_key'] = 'required|unique:psx_core_menus,module_lang_key';
        }

        // if(in_array('module_id',$coreFieldsIds)){
            $validationArr['module_id'] = 'required|unique:psx_core_menus,module_id';
        // }

        // change custom attribute if required start
        $attributes['module_name'] = "Menu Name";
        $attributes['core_sub_menu_group_id'] = "Sub Menu Group Id";
        $attributes['module_desc'] = "Menu Description";
        $attributes['module_lang_key'] = "Menu Language Key";
        $attributes['module_id'] = "Module";
        // change custom attribute if required end

        $validator = Validator::make($request->all(),$validationArr,[], $attributes);

        if ($validator->fails()) {
            return redirect()->route(self::createRoute)->with('menu_relation_errors',$errors)
                ->withErrors($validator)
                ->withInput();
        } else {

            if (collect($errors)->isNotEmpty()){
                return redirect()->route(self::createRoute)->with('menu_relation_errors',$errors);
            }
        }

        // validation end
        $module = $this->moduleService->store($request);

        // if have error
        if (isset($module['error'])){
            $msg = $module['error'];
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }

        return redirectView(self::indexRoute, $module);
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
        // return $dataArr;
        return renderView(self::editPath, $dataArr);
    }

    public function update(Request $request, $moduleId)
    {
        // dd($request->all(), $moduleId);
        // validation start
        $errors = validateForCustomField($this->code,$request->menu_relation);

        $coreFieldsIds = [];
        $errors = [];

        $cond['module_name'] = $this->code;
        $cond['mandatory'] = 1;
        $cond['is_core_field'] = 1;

        $coreFields = $this->coreFieldFilterSettingService->getCoreFieldsWithConds($cond);

        foreach ($coreFields as $key => $value){
            if (str_contains($value->field_name,"@@")) {
                $originFieldName = strstr($value->field_name,"@@",true);
            } else {
                $originFieldName = $value->field_name;
            }
            array_push($coreFieldsIds,$originFieldName);

        }

        $validationArr = [];

        if(in_array('module_name',$coreFieldsIds)){
            $validationArr['module_name'] = 'required|min:3|unique:psx_core_menus,module_name,'.$moduleId;
        }

        if(in_array('core_sub_menu_group_id',$coreFieldsIds)){
            $validationArr['core_sub_menu_group_id'] = 'required';
        }

        if(in_array('module_desc',$coreFieldsIds)){
            $validationArr['module_desc'] = 'required|unique:psx_core_menus,module_desc,'.$moduleId;
        }

        if(in_array('module_lang_key',$coreFieldsIds)){
            $validationArr['module_lang_key'] = 'required|unique:psx_core_menus,module_lang_key,'.$moduleId;
        }

        if(in_array('module_id',$coreFieldsIds)){
            $validationArr['module_id'] = 'required|unique:psx_core_menus,module_id,'.$moduleId;
        }

        // change custom attribute if required start
        $attributes['module_name'] = "Menu Name";
        $attributes['core_sub_menu_group_id'] = "Sub Menu Group Id";
        $attributes['module_desc'] = "Menu Description";
        $attributes['module_lang_key'] = "Menu Language Key";
        $attributes['module_id'] = "Module";
        // change custom attribute if required end

        $validator = Validator::make($request->all(),$validationArr,[], $attributes);

        if ($validator->fails()) {
            return redirect()->route(self::editRoute, $moduleId)->with('menu_relation_errors',$errors)
                ->withErrors($validator)
                ->withInput();
        } else {

            if (collect($errors)->isNotEmpty()){
                return redirect()->route(self::editRoute, $moduleId)->with('menu_relation_errors',$errors);
            }
        }

        // validation end
        $module = $this->moduleService->update($moduleId, $request);

        // if have error
        if (isset($module['error'])){
            $msg = $module['error'];
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }

        return redirectView(self::indexRoute, $module);
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
