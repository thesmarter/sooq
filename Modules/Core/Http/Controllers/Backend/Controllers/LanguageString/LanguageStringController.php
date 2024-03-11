<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\LanguageString;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;
// use Modules\Core\Entities\Language;
use Modules\Core\Constants\Constants;
// use Modules\Core\Entities\LanguageString;
use Illuminate\Support\Facades\Validator;
use Modules\Core\Imports\LanguageStringImport;
use Modules\Core\Http\Services\LanguageStringService;
use Modules\Core\Http\Requests\StoreLanguageStringRequest;
use Modules\Core\Http\Requests\UpdateLanguageStringRequest;
use Modules\Core\Http\Services\CoreFieldFilterSettingService;

class LanguageStringController extends Controller
{

    const parentPath = "language_string/";
    const indexPath = self::parentPath."Index";
    const createPath = self::parentPath."Create";
    const editPath = self::parentPath."Edit";
    const indexRoute = "language_string.index";
    const createRoute = "language_string.create";
    const editRoute = "language_string.edit";

    protected $languageStringService, $successFlag, $dangerFlag, $csvFile, $warningFlag, $code, $coreFieldFilterSettingService;

    public function __construct(LanguageStringService $languageStringService, CoreFieldFilterSettingService $coreFieldFilterSettingService)
    {
        $this->languageStringService = $languageStringService;
        $this->coreFieldFilterSettingService = $coreFieldFilterSettingService;
        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;
        $this->warningFlag = Constants::warning;
        $this->code = Constants::languageString;
    }

    public function index($languageId,Request $request)
    {
        $dataArr = $this->languageStringService->index($languageId,$request);
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        return renderView(self::indexPath, $dataArr);
    }

    public function create($languageId)
    {
        $dataArr = $this->languageStringService->create($languageId);
        $checkPermission = $dataArr["checkPermission"];

        if (!empty($checkPermission)){
            return $checkPermission;
        }
        return renderView(self::createPath, $dataArr);
    }

    public function store(Request $request)
    {
        $routeParams = [$request->language];

        // validation start
        $errors = validateForCustomField($this->code,$request->language_strings_relation);

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

        if(in_array('key',$coreFieldsIds)){
            $validationArr['key'] = 'required|unique:psx_language_strings,key';
        }

        if(in_array('value',$coreFieldsIds)){
            $validationArr['value'] = 'required|unique:psx_language_strings,value';
        }

        $validator = Validator::make($request->all(),$validationArr);

        if ($validator->fails()) {
            return redirect()->route(self::createRoute, $request->language_id)->with('language_strings_relation_errors',$errors)
                ->withErrors($validator)
                ->withInput();
        } else {

            if (collect($errors)->isNotEmpty()){
                return redirect()->route(self::createRoute, $request->language_id)->with('language_strings_relation_errors',$errors);
            }
        }

        // validation end

        $languageString = $this->languageStringService->store($request);

        // if have error
        if (isset($languageString['error'])){
            $msg = $languageString['error'];
            return redirectView(self::indexRoute, $msg, $this->dangerFlag,$routeParams);
        }

        return redirect()->back();
    }

//    public function show(Language $language,LanguageString $language_string)
//    {
//        return redirect()->route('language_string.edit', $language, $language_string);
//    }

    public function edit($languageId, $languageStringId)
    {
        $dataArr = $this->languageStringService->edit($languageId, $languageStringId);
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        return renderView(self::editPath, $dataArr);
    }

    public function update($language_id, $id, Request $request)
    {

        // validation start
        $errors = validateForCustomField($this->code,$request->language_strings_relation);

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

        if(in_array('key',$coreFieldsIds)){
            $validationArr['key'] = 'required';
        }

        if(in_array('value',$coreFieldsIds)){
            $validationArr['value'] = 'required';
        }

        $validator = Validator::make($request->all(),$validationArr);

        if ($validator->fails()) {
            return redirect()->route('language_string.edit',['language' => $language_id, 'language_string' => $id])->with('language_strings_relation_errors',$errors)
                ->withErrors($validator)
                ->withInput();
        } else {
            if (collect($errors)->isNotEmpty()){
                return redirect()->route('language_string.edit',['language' => $language_id, 'language_string' => $id])->with('language_strings_relation_errors',$errors);
            }
        }

        // validation end
        $routeParams = [$language_id];
        $languageString = $this->languageStringService->update($id,$request);

        // if have error

        if (isset($languageString['error'])){
            $msg = $languageString['error'];
            return redirectView(self::indexRoute, $msg, $this->dangerFlag,$routeParams);
        }

        return redirect()->back();
    }

    public function destroy($languageId, $languageStringId)
    {
        $name = $this->languageStringService->destroy($languageId , $languageStringId);

        // $checkPermission = $dataArr["checkPermission"];
        // if (!empty($checkPermission)){
        //     return $checkPermission;
        // }

        // $msg = 'The '.$name.' row has been deleted successfully.';
        $msg = __('core__be_delete_success',['attribute'=>$name]);

        return redirectView(self::indexRoute, $msg, $this->dangerFlag, $languageId);
    }

    public function importCSV($languageId, Request $request){

        $this->languageStringService->importCSV($languageId, $request);
        return redirect()->back();
    }

    public function exportJson($languageId){
        $dataArr = $this->languageStringService->exportJson($languageId);

        return $dataArr;
    }

    public function exportCSV($languageId){
        $dataArr = $this->languageStringService->exportCSV($languageId);

        return $dataArr;
    }


    public function getLanguageString(Request $request){

        $dataArr = $this->languageStringService->getlanguageStringsByKey($request);
        return $dataArr;

    }

    public function updateLanguageStrings(Request $request){

        $dataArr = $this->languageStringService->updateLanguageStrings($request);
        return redirect()->back();

    }

    public function updateAllLanguageStrings(){

        $dataArr = $this->languageStringService->updateAllLanguageStrings();
        return redirect()->back();

    }

    public function importAllLanguageStrings(Request $request){

        $dataArr = $this->languageStringService->importAllLanguageStrings($request);
        return redirect()->back();

    }

    public function screenDisplayUiStore(Request $request) {
        $parameter = ['page' => $request->current_page];
        $this->languageStringService->makeColumnHideShown($request);
        $msg = __('core__be_screen_display_ui_updated');
        return redirect()->back();
    }


}
