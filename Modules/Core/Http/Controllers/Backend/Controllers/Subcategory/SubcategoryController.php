<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\Subcategory;

use Illuminate\Routing\Controller;
use Modules\Core\Constants\Constants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\Core\Imports\SubcategoryImport;
use Modules\Core\Http\Services\SubcategoryService;
use Modules\Core\Http\Requests\StoreSubcategoryRequest;
use Modules\Core\Http\Requests\UpdateSubcategoryRequest;
use Modules\Core\Http\Services\CoreFieldFilterSettingService;

class SubcategoryController extends Controller
{

    const parentPath = "subcategory/";
    const indexPath = self::parentPath."Index";
    const createPath = self::parentPath."Create";
    const editPath = self::parentPath."Edit";
    const indexRoute = "subcategory.index";
    const createRoute = "subcategory.create";
    const editRoute = "subcategory.edit";

    protected $subcategoryService, $successFlag, $dangerFlag, $csvFile, $warningFlag, $code, $coreFieldFilterSettingService;

    public function __construct(SubcategoryService $subcategoryService, CoreFieldFilterSettingService $coreFieldFilterSettingService)
    {
        $this->subcategoryService = $subcategoryService;

        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;
        $this->warningFlag = Constants::warning;
        $this->csvFile = Constants::csvFile;
        $this->coreFieldFilterSettingService = $coreFieldFilterSettingService;
        $this->code = Constants::subcategory;
    }


    public function index(Request $request)
    {
        $dataArr = $this->subcategoryService->index(self::indexRoute,$request);
        // return $dataArr;
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        // dd($dataArr) ;
        return renderView(self::indexPath, $dataArr);
    }
    public function screenDisplayUiStore(Request $request) {

        $parameter = ['page' => $request->current_page];

        $this->subcategoryService->makeColumnHideShown($request);

        $msg = 'ScreenDisplay UiSetting is updated.';
        // return redirectView(self::indexRoute, $msg,null,$parameter);
        return redirect()->back();
    }

    public function create()
    {
        $dataArr = $this->subcategoryService->create();
        // check permission
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        return renderView(self::createPath, $dataArr);
    }

    public function store(Request $request)
    {

        // validation start
        $errors = validateForCustomField($this->code,$request->subcategory_relation);

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

        if(in_array('name',$coreFieldsIds)){
            $validationArr['name'] = 'required|min:3|unique:psx_subcategories,name,'.request()->id;
        }

        if(in_array('category_id',$coreFieldsIds)){
            $validationArr['category_id'] = 'required';
        }

        if(in_array('sub_cat_photo',$coreFieldsIds)){
            $validationArr['cover'] = "required|sometimes|image";
        }

        if(in_array('sub_cat_icon',$coreFieldsIds)){
            $validationArr['icon'] = "required|sometimes|image";
        }

        // change custom attribute if required start
        $attributes['category_id'] = "Category";
        $attributes['name'] = "Sub Category";
        $attributes['cover'] = "Cover Image";
        $attributes['icon'] = "Icon Image";
        // change custom attribute if required end

        $validator = Validator::make($request->all(),$validationArr,[], $attributes);

        if ($validator->fails()) {
            return redirect()->route('subcategory.create')->with('subcategory_relation_errors',$errors)
                ->withErrors($validator)
                ->withInput();
        } else {

            if (collect($errors)->isNotEmpty()){
                return redirect()->route('subcategory.create')->with('subcategory_relation_errors',$errors);
            }
        }

        // validation end

        $subcategory = $this->subcategoryService->store($request);

        // if have error
        if (isset($subcategory['error'])){
            $msg = $subcategory['error'];
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }

        return redirectView(self::indexRoute, $subcategory);
    }

//    public function show(Subcategory $subcategory)
//    {
//        return redirect()->route('subcategory.edit', $subcategory);
//    }

    public function edit($id)
    {
        $dataArr = $this->subcategoryService->edit($id);
        // check permission
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        return renderView(self::editPath, $dataArr);
    }

    public function update(Request $request, $id)
    {

        // validation start
        $errors = validateForCustomField($this->code,$request->subcategory_relation);

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

        if(in_array('name',$coreFieldsIds)){
            $validationArr['name'] = 'required|min:3|unique:psx_subcategories,name,'.$id;
        }

        if(in_array('category_id',$coreFieldsIds)){
            $validationArr['category_id'] = 'required';
        }

        if(in_array('sub_cat_photo',$coreFieldsIds)){
            $validationArr['cover'] = "nullable|sometimes|image";
        }

        if(in_array('sub_cat_icon',$coreFieldsIds)){
            $validationArr['icon'] = "nullable|sometimes|image";
        }

        // change custom attribute if required start
        $attributes['category_id'] = "Category";
        $attributes['name'] = "Sub Category";
        $attributes['cover'] = "Cover Image";
        $attributes['icon'] = "Icon Image";
        // change custom attribute if required end

        $validator = Validator::make($request->all(),$validationArr,[], $attributes);

        if ($validator->fails()) {
            return redirect()->route('subcategory.edit', $id)->with('subcategory_relation_errors',$errors)
                ->withErrors($validator)
                ->withInput();
        } else {

            if (collect($errors)->isNotEmpty()){
                return redirect()->route('subcategory.edit', $id)->with('subcategory_relation_errors',$errors);
            }
        }

        // validation end

        $subcategory= $this->subcategoryService->update($id, $request);

        // if have error
        if (isset($subcategory['error'])){
            $msg = $subcategory['error'];
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }

        return redirect()->back();
    }

    public function destroy($id){
        $dataArr = $this->subcategoryService->destroy($id);
        // check permission
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }

    public function statusChange($id){
        $dataArr = $this->subcategoryService->statusChange($id);
        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }

    public function importCSV(Request $request){

        // dd($request->all());
        $import = new SubcategoryImport();
        // dd($request->file($this->csvFile));
        $import->import($request->file($this->csvFile));
        return back();
    }

}
