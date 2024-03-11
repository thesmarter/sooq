<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\MobileLanguageString;

use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Constants\Constants;
use Illuminate\Support\Facades\Validator;
// use Modules\Core\Entities\MobileLanguage;
// use Modules\Core\Entities\MobileLanguageString;
use Illuminate\Contracts\Support\Renderable;
use Modules\Core\Http\Services\MobileLanguageStringService;
use Modules\Core\Http\Services\CoreFieldFilterSettingService;
use Modules\Core\Http\Requests\StoreMobileLanguageStringRequest;
use Modules\Core\Http\Requests\UpdateMobileLanguageStringRequest;

class MobileLanguageStringController extends Controller
{
    const parentPath = "mobile_language_string/";
    const indexPath = self::parentPath."Index";
    const createPath = self::parentPath."Create";
    const editPath = self::parentPath."Edit";
    const indexRoute = "mobile_language_string.index";
    const createRoute = "mobile_language_string.create";
    const editRoute = "mobile_language_string.edit";

    protected $mobileLanguageStringService, $successFlag, $dangerFlag, $csvFile, $warningFlag, $code, $coreFieldFilterSettingService;

    public function __construct(MobileLanguageStringService $mobileLanguageStringService, CoreFieldFilterSettingService $coreFieldFilterSettingService)
    {
        $this->mobileLanguageStringService = $mobileLanguageStringService;
        $this->coreFieldFilterSettingService = $coreFieldFilterSettingService;
        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;
        $this->warningFlag = Constants::warning;
        $this->code = Constants::mobileLanguageString;
    }

    public function index($id,Request $request)
    {
        $dataArr = $this->mobileLanguageStringService->index(self::indexRoute,$request);
        // return $dataArr;
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }

        return renderView(self::indexPath, $dataArr);
    }

    public function screenDisplayUiStore(Request $request) {

        $parameter = ['page' => $request->current_page];

        $this->mobileLanguageStringService->makeColumnHideShown($request);

        $msg = 'ScreenDisplay UiSetting is updated.';
        // return redirectView(self::indexRoute, $msg,null,$parameter);
        return redirect()->back();
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create($mobileLanguageId)
    {
        $dataArr = $this->mobileLanguageStringService->create($mobileLanguageId);

        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }

        return renderView(self::createPath, $dataArr);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store($mobileLanguage_id, Request $request)
    {
        // validation start
        $errors = validateForCustomField($this->code,$request->mobile_language_strings_relation);

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
            $validationArr['key'] = 'required|unique:psx_mobile_language_strings,key,';
        }

        if(in_array('value',$coreFieldsIds)){
            $validationArr['value'] = 'required|unique:psx_mobile_language_strings,value,';
        }

        $validator = Validator::make($request->all(),$validationArr);

        if ($validator->fails()) {
            return redirect()->route(self::createRoute, $mobileLanguage_id)->with('mobile_language_strings_relation_errors',$errors)
                ->withErrors($validator)
                ->withInput();
        } else {

            if (collect($errors)->isNotEmpty()){
                return redirect()->route(self::createRoute, $mobileLanguage_id)->with('mobile_language_strings_relation_errors',$errors);
            }
        }

        // validation end

        $languageString = $this->mobileLanguageStringService->store($request);

        // if have error
        if (isset($languageString['error'])){
            $msg = $languageString['error'];
            return redirectView(self::editRoute, $msg, $this->dangerFlag);
        }

        return redirect()->back();

    }

//    public function show($id)
//    {
//        return view('core::show');
//    }
    public function statusChange($id){
        $dataArr = $this->mobileLanguageStringService->statusChange($id);
        return redirect()->back()->with(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }


    public function edit($mobileLanguageId, $mobileLanguageStringId)
    {
        $dataArr = $this->mobileLanguageStringService->edit($mobileLanguageId, $mobileLanguageStringId);

        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }

        return renderView(self::editPath, $dataArr);
    }

    public function importCSV($languageId, Request $request){
        $this->mobileLanguageStringService->importCSV($languageId, $request);


        return redirect()->back();
    }
    public function exportJson($languageId){
        $dataArr = $this->mobileLanguageStringService->exportJson($languageId);


        return $dataArr;
    }
    public function exportCSV($languageId){
        $dataArr = $this->mobileLanguageStringService->exportCSV($languageId);


        return $dataArr;
    }


    public function update($mobileLanguageId, Request $request, $mobileLanguageStringId)
    {
        // validation start
        $errors = validateForCustomField($this->code,$request->mobile_language_strings_relation);

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
            return redirect()->route(self::editRoute, [$mobileLanguageId, $mobileLanguageStringId])->with('mobile_language_strings_relation_errors',$errors)
                ->withErrors($validator)
                ->withInput();
        } else {

            if (collect($errors)->isNotEmpty()){
                return redirect()->route(self::editRoute, [$mobileLanguageId, $mobileLanguageStringId])->with('mobile_language_strings_relation_errors',$errors);
            }
        }

        // validation end

        $languageString = $this->mobileLanguageStringService->update($mobileLanguageStringId, $request);

        // if have error
        if ($languageString && isset($languageString['error'])){
            $msg = $languageString['error'];
            return redirect()->back()->with(self::indexRoute, $msg, $this->dangerFlag);
        }

        return redirect()->back();

    }

    public function destroy($mobileLanguageId, $mobileLanguageStringId)
    {
        $dataArr = $this->mobileLanguageStringService->destroy($mobileLanguageId, $mobileLanguageStringId);

        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        // dd($dataArr);
        return redirectView(self::indexRoute, $dataArr, $this->successFlag, $mobileLanguageId);
    }
}
