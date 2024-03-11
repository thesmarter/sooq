<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\VendorMenuGroup;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\VendorMenuGroup;
use Illuminate\Support\Facades\Validator;
use Modules\Core\Http\Services\VendorMenuGroupService;
use Modules\Core\Http\Requests\StoreVendorMenuGroupRequest;
use Modules\Core\Http\Requests\UpdateVendorMenuGroupRequest;
use Modules\Core\Http\Services\CoreFieldFilterSettingService;

class VendorMenuGroupController extends Controller
{

    const parentPath = "vendor_menu_group/";
    const indexPath = self::parentPath."Index";
    const createPath = self::parentPath."Create";
    const editPath = self::parentPath."Edit";
    const indexRoute = "vendor_menu_group.index";
    const createRoute = "vendor_menu_group.create";
    const editRoute = "vendor_menu_group.edit";

    protected $menuGroupService, $successFlag, $dangerFlag, $csvFile, $warningFlag, $code, $coreFieldFilterSettingService;

    public function __construct(VendorMenuGroupService $menuGroupService, CoreFieldFilterSettingService $coreFieldFilterSettingService)
    {
        $this->menuGroupService = $menuGroupService;
        $this->coreFieldFilterSettingService = $coreFieldFilterSettingService;
        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;
        $this->warningFlag = Constants::warning;
        $this->csvFile = Constants::csvFile;
        $this->code = Constants::vendorMenuGroup;
    }

    public function index(Request $request)
    {
        $dataArr = $this->menuGroupService->index($request);
        $checkPermission = $dataArr["checkPermission"];

        if (!empty($checkPermission)){
            return $checkPermission;
        }
        return renderView(self::indexPath, $dataArr);
    }

    public function create()
    {
        $dataArr = $this->menuGroupService->create();
        // check permission
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        return renderView(self::createPath,$dataArr);
    }

    public function store(Request $request)
    {
        // validation start
        // dd($request->all());
        $errors = validateForCustomField($this->code,$request->menu_group_relation);

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

        if(in_array('group_name',$coreFieldsIds)){
            $validationArr['group_name'] = 'required|min:3|unique:psx_vendor_menu_groups,group_name,';
        }

        // change custom attribute if required start
        $attributes['group_name'] = "Group Name";
        // change custom attribute if required end

        $validator = Validator::make($request->all(),$validationArr,[], $attributes);

        if ($validator->fails()) {
            return redirect()->route(self::createRoute)->with('menu_group_relation_errors',$errors)
                ->withErrors($validator)
                ->withInput();
        } else {

            if (collect($errors)->isNotEmpty()){
                return redirect()->route(self::createRoute)->with('menu_group_relation_errors',$errors);
            }
        }

        // validation end
        $menuGroup = $this->menuGroupService->store($request);

        // if have error
        if (isset($menuGroup['error'])){
            $msg = $menuGroup['error'];
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }

        return redirectView(self::indexRoute, $menuGroup);

    }

    public function edit($id)
    {
        $dataArr = $this->menuGroupService->edit($id);
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        return renderView(self::editPath, $dataArr);
    }

    public function update(Request $request, $id)
    {
        // validation start
        $errors = validateForCustomField($this->code,$request->menu_group_relation);

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

        if(in_array('group_name',$coreFieldsIds)){
            $validationArr['group_name'] = 'required|min:3|unique:psx_vendor_menu_groups,group_name,'.$id;
        }

        // change custom attribute if required start
        $attributes['group_name'] = "Group Name";
        // change custom attribute if required end

        $validator = Validator::make($request->all(),$validationArr,[], $attributes);

        if ($validator->fails()) {
            return redirect()->route(self::editRoute, $id)->with('menu_group_relation_errors',$errors)
                ->withErrors($validator)
                ->withInput();
        } else {

            if (collect($errors)->isNotEmpty()){
                return redirect()->route(self::editRoute, $id)->with('menu_group_relation_errors',$errors);
            }
        }

        // validation end
        $menuGroup = $this->menuGroupService->update($id, $request);

        // if have error
        if (isset($menuGroup['error'])){
            $msg = $menuGroup['error'];
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }

        return redirectView(self::indexRoute, $menuGroup);
    }

    public function destroy($id)
    {
        $dataArr = $this->menuGroupService->destroy($id);
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }

        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }

    public function statusChange($id){
        $dataArr = $this->menuGroupService->statusChange($id);
        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }

    public function screenDisplayUiStore(Request $request) {
        $parameter = ['page' => $request->current_page];
        $this->menuGroupService->makeColumnHideShown($request);
        $msg = __('core__be_screen_display_ui_updated');
        return redirect()->back();
    }
}
