<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\Category;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Constants\Constants;
use Modules\Core\Http\Services\CategoryService;
use Modules\Core\Http\Requests\StoreCategoryRequest;
use Modules\Core\Http\Requests\UpdateCategoryRequest;
use Modules\Core\Http\Services\CoreFieldFilterSettingService;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    const parentPath = "category/";
    const indexPath = self::parentPath."Index";
    const createPath = self::parentPath."Create";
    const editPath = self::parentPath."Edit";
    const indexRoute = "category.index";
    const createRoute = "category.create";
    const editRoute = "category.edit";


    protected $categoryService, $successFlag, $dangerFlag, $csvFile, $code, $coreFieldFilterSettingService;

    public function __construct(CategoryService $categoryService, CoreFieldFilterSettingService $coreFieldFilterSettingService)
    {
        $this->categoryService = $categoryService;
        $this->coreFieldFilterSettingService = $coreFieldFilterSettingService;
        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;
        $this->csvFile = Constants::csvFile;
        $this->code = Constants::category;
    }



    public function index(Request $request)
    {
        $dataArr = $this->categoryService->index(self::indexRoute,$request);
//         return $dataArr;
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        // dd($dataArr);
        return renderView(self::indexPath, $dataArr);
    }

    public function screenDisplayUiStore(Request $request) {

        $parameter = ['page' => $request->current_page];

        $this->categoryService->makeColumnHideShown($request);

        // $msg = 'ScreenDisplay UiSetting is updated.';
        $msg = __('core__be_screen_display_ui_updated');
        // return redirectView(self::indexRoute, $msg,null,$parameter);
        return redirect()->back();
    }

    public function create()
    {
        $dataArr = $this->categoryService->create();
        // dd($dataArr);
        // check permission
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        // dd($dataArr);

        return renderView(self::createPath, $dataArr);
        // return renderView(self::createPath);
    }

    public function store(Request $request)
    {

        // validation start
        $errors = validateForCustomField($this->code,$request->category_relation);

        $coreFieldsIds = [];
        $errors = [];

        $cond['module_name'] = $this->code;
        $cond['mandatory'] = 1;
        $cond['is_core_field'] = 1;

        $coreFields = $this->coreFieldFilterSettingService->getCoreFieldsWithConds($cond);

        // $coreFields = CoreFieldFilterSetting::where('module_name',)->where('mandatory',1)->where('is_core_field',1)->get();
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
            $validationArr['name'] = 'required|min:3|unique:psx_categories,name';
        }

        if(in_array('cat_photo',$coreFieldsIds)){
            $validationArr['cover'] = "required|sometimes|image";
        }

        if(in_array('cat_icon',$coreFieldsIds)){
            $validationArr['icon'] = "required|sometimes|image";
        }


        $validator = Validator::make($request->all(),$validationArr);

        if ($validator->fails()) {
            return redirect()->route('category.create')->with('category_relation_errors',$errors)
                ->withErrors($validator)
                ->withInput();
        } else {

            if (collect($errors)->isNotEmpty()){
                return redirect()->route('category.create')->with('category_relation_errors',$errors);
            }
        }

        // validation end

        $category = $this->categoryService->createOrUpdate($request);
        // dd($category);

        // if have error
        if ($category){
            $msg = $category;
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }
        return redirectView(self::indexRoute, $subcategory);

    }

    public function edit($id)
    {
        $dataArr = $this->categoryService->edit($id);
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
        $errors = validateForCustomField($this->code,$request->category_relation);

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
            $validationArr['name'] = 'required|min:3|unique:psx_categories,name,'.request()->route('category');
        }

        if(in_array('cat_photo',$coreFieldsIds)){
            $validationArr['cover'] = 'nullable|sometimes|image';
        }

        if(in_array('cat_icon',$coreFieldsIds)){
            $validationArr['icon'] = "nullable|sometimes|image";
        }


        $validator = Validator::make($request->all(),$validationArr);

        if ($validator->fails()) {
            return redirect()->route('category.edit', $id)->with('category_relation_errors',$errors)
                ->withErrors($validator)
                ->withInput();
        } else {

            if (collect($errors)->isNotEmpty()){
                return redirect()->route('category.edit', $id)->with('category_relation_errors',$errors);
            }
        }

        // validation end

        $category = $this->categoryService->createOrUpdate($request, $id);

        // if have error
        if ($category){
            $msg = $category;
            return redirectView(self::editRoute, $msg, $this->dangerFlag, $id);
        }

        return redirect()->back();
    }

    public function destroy($id)
    {
        $dataArr = $this->categoryService->destroy($id);

        // check permission
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }

        // $msg = 'The '.$dataArr["name"].' row has been deleted successfully.';
        $msg = __('core__be_delete_success',['attribute'=>$dataArr["name"]]);
        return redirectView(self::indexRoute, $msg);
    }

    public function statusChange($id){

        $this->categoryService->makePublishOrUnpublish($id);

        // $msg = 'The status of category row has been updated successfully.';
        $msg = __('core__be_status_attribute_updated',['attribute'=>__('core__be_category')]);
        // dd($msg);
        return redirectView(self::indexRoute, $msg);

    }

    public function importCSV(Request $request){

        // dd($request->file($this->csvFile));
        $this->categoryService->importCSVFile($request, $this->csvFile);
        return back();

    }

}
