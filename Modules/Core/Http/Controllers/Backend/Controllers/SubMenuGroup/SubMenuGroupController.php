<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\SubMenuGroup;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Constants\Constants;
use Illuminate\Support\Facades\Validator;
use Modules\Core\Http\Services\SubMenuGroupService;
use Modules\Core\Http\Requests\StoreSubMenuGroupRequest;
use Modules\Core\Http\Requests\UpdateSubMenuGroupRequest;
use Modules\Core\Http\Services\CoreFieldFilterSettingService;

class SubMenuGroupController extends Controller
{

    const parentPath = "sub_menu_group/";
    const indexPath = self::parentPath."Index";
    const createPath = self::parentPath."Create";
    const editPath = self::parentPath."Edit";
    const indexRoute = "sub_menu_group.index";
    const createRoute = "sub_menu_group.create";
    const editRoute = "sub_menu_group.edit";

    protected $subMenuGroupService, $successFlag, $dangerFlag, $csvFile, $warningFlag, $code, $coreFieldFilterSettingService;

    public function __construct(SubMenuGroupService $subMenuGroupService, CoreFieldFilterSettingService $coreFieldFilterSettingService)
    {
        $this->subMenuGroupService = $subMenuGroupService;
        $this->coreFieldFilterSettingService = $coreFieldFilterSettingService;
        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;
        $this->warningFlag = Constants::warning;
        $this->csvFile = Constants::csvFile;
        $this->code = Constants::coreSubMenuGroup;
    }

    public function index(Request $request)
    {
        $dataArr = $this->subMenuGroupService->index($request);
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        return renderView(self::indexPath, $dataArr);
    }

    public function create()
    {
        $dataArr = $this->subMenuGroupService->create();
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        return renderView(self::createPath, $dataArr);
    }

    public function store(Request $request)
    {
        // validation start
        $errors = validateForCustomField($this->code,$request->sub_menu_group_relation);

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

        if(in_array('sub_menu_name',$coreFieldsIds)){
            $validationArr['sub_menu_name'] = 'required|min:3|unique:psx_core_sub_menu_groups,sub_menu_name,';
        }

        if(in_array('core_menu_group_id',$coreFieldsIds)){
            $validationArr['core_menu_group_id'] = 'required';
        }

        if(in_array('sub_menu_icon',$coreFieldsIds)){
            $validationArr['icon_id'] = 'required';
        }

        if(in_array('ordering',$coreFieldsIds)){
            $validationArr['ordering'] = 'integer';
        }

        // change custom attribute if required start
        $attributes['sub_menu_name'] = "Sub Menu Name";
        $attributes['core_menu_group_id'] = "Menu Group Id";
        $attributes['icon_id'] = "Icon";
        // change custom attribute if required end

        $validator = Validator::make($request->all(),$validationArr,[], $attributes);

        if ($validator->fails()) {
            return redirect()->route(self::createRoute)->with('sub_menu_group_relation_errors',$errors)
                ->withErrors($validator)
                ->withInput();
        } else {

            if (collect($errors)->isNotEmpty()){
                return redirect()->route(self::createRoute)->with('sub_menu_group_relation_errors',$errors);
            }
        }

        // validation end

        $subMenuGroup = $this->subMenuGroupService->store($request);

        // if have error
        if (isset($subMenuGroup['error'])){
            $msg = $subMenuGroup['error'];


            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }

        return redirectView(self::indexRoute, $subMenuGroup);
    }

//    public function show(CoreSubMenuGroup $sub_menu_group)
//    {
//        return redirect()->route('sub_menu_group.edit', $sub_menu_group);
//    }

    public function edit($id)
    {
        $dataArr = $this->subMenuGroupService->edit($id);
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        return renderView(self::editPath, $dataArr);
    }

    public function update(Request $request, $id)
    {
        // validation start
        $errors = validateForCustomField($this->code,$request->sub_menu_group_relation);

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

        if(in_array('sub_menu_name',$coreFieldsIds)){
            $validationArr['sub_menu_name'] = 'required|min:3|unique:psx_core_sub_menu_groups,sub_menu_name,'.$id;
        }

        if(in_array('core_menu_group_id',$coreFieldsIds)){
            $validationArr['core_menu_group_id'] = 'required';
        }

        if(in_array('sub_menu_icon',$coreFieldsIds)){
            $validationArr['icon_id'] = 'required';
        }

        if(in_array('ordering',$coreFieldsIds)){
            $validationArr['ordering'] = 'integer';
        }

        // change custom attribute if required start
        $attributes['sub_menu_name'] = "Sub Menu Name";
        $attributes['core_menu_group_id'] = "Menu Group Id";
        $attributes['icon_id'] = "Icon";
        // change custom attribute if required end

        $validator = Validator::make($request->all(),$validationArr,[], $attributes);

        if ($validator->fails()) {
            return redirect()->route(self::editRoute, $id)->with('sub_menu_group_relation_errors',$errors)
                ->withErrors($validator)
                ->withInput();
        } else {

            if (collect($errors)->isNotEmpty()){
                return redirect()->route(self::editRoute, $id)->with('sub_menu_group_relation_errors',$errors);
            }
        }

        // validation end

        $currency = $this->subMenuGroupService->update($id, $request);

        // if have error
        if ($currency){
            $msg = $currency;
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }

        return redirectView(self::indexRoute, $currency);
    }

    public function destroy($id)
    {
        $dataArr = $this->subMenuGroupService->destroy($id);
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);

    }

    public function statusChange($id){
        $dataArr = $this->subMenuGroupService->statusChange($id);
        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }
    public function screenDisplayUiStore(Request $request) {

        $parameter = ['page' => $request->current_page];
        $this->subMenuGroupService->makeColumnHideShown($request);
        $msg = __('core__be_screen_display_ui_updated');
        return redirect()->back();
    }
}
