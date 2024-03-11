<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\MobileLanguage;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Constants\Constants;
use Illuminate\Support\Facades\Validator;
use Modules\Core\Http\Services\MobileLanguageService;
use Modules\Core\Http\Requests\StoreMobileLanguageRequest;
use Modules\Core\Http\Requests\UpdateMobileLanguageRequest;
use Modules\Core\Http\Services\CoreFieldFilterSettingService;

class MobileLanguageController extends Controller
{
    const parentPath = "mobile_language/";
    const indexPath = self::parentPath."Index";
    const createPath = self::parentPath."Create";
    const editPath = self::parentPath."Edit";
    const indexRoute = "mobile_language.index";
    const createRoute = "mobile_language.create";
    const editRoute = "mobile_language.edit";

    protected $mobileLanguageService, $successFlag, $dangerFlag, $csvFile, $warningFlag, $code, $coreFieldFilterSettingService;

    public function __construct(MobileLanguageService $mobileLanguageService, CoreFieldFilterSettingService $coreFieldFilterSettingService)
    {
        $this->mobileLanguageService = $mobileLanguageService;
        $this->coreFieldFilterSettingService = $coreFieldFilterSettingService;
        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;
        $this->warningFlag = Constants::warning;
        $this->csvFile = Constants::csvFile;
        $this->code = Constants::mobileLanguage;
    }

    public function index(Request $request)
    {
        $dataArr = $this->mobileLanguageService->index(self::indexRoute,$request);
        // return $dataArr;
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }

        return renderView(self::indexPath, $dataArr);
    }

    public function screenDisplayUiStore(Request $request) {

        $parameter = ['page' => $request->current_page];

        $this->mobileLanguageService->makeColumnHideShown($request);

        $msg = 'ScreenDisplay UiSetting is updated.';
        // return redirectView(self::indexRoute, $msg,null,$parameter);
        return redirect()->back();
    }

    public function create()
    {
        $dataArr = $this->mobileLanguageService->create();
        // check permission
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        return renderView(self::createPath);
    }

    public function store(Request $request)
    {
        // validation start
        $errors = validateForCustomField($this->code,$request->mobile_language_relation);

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

        if(in_array('symbol',$coreFieldsIds)){
            $validationArr['symbol'] = 'required|unique:psx_mobile_languages,symbol';
        }

        if(in_array('name',$coreFieldsIds)){
            $validationArr['name'] = 'required|unique:psx_mobile_languages,name';
        }

        if(in_array('language_code',$coreFieldsIds)){
            $validationArr['languageCode'] = 'required|unique:psx_mobile_languages,language_code';
        }

        if(in_array('country_code',$coreFieldsIds)){
            $validationArr['countryCode'] = 'required|unique:psx_mobile_languages,country_code';
        }

        $validator = Validator::make($request->all(),$validationArr);

        if ($validator->fails()) {
            return redirect()->route(self::createRoute)->with('mobile_language_relation_errors',$errors)
                ->withErrors($validator)
                ->withInput();
        } else {

            if (collect($errors)->isNotEmpty()){
                return redirect()->route(self::createRoute)->with('mobile_language_relation_errors',$errors);
            }
        }

        // validation end

        $mobileLanguage = $this->mobileLanguageService->store($request);

        // if have error
        if (isset($mobileLanguage['error'])){
            $msg = $mobileLanguage['error'];
            return redirectView(self::editRoute, $msg, $this->dangerFlag);
        }

        return redirectView(self::indexRoute, $mobileLanguage);
    }

    public function edit($id)
    {
        $dataArr = $this->mobileLanguageService->edit($id);
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        return renderView(self::editPath, $dataArr);
    }

    public function update(Request $request, $id)
    {
        // validation start
        $errors = validateForCustomField($this->code,$request->mobile_language_relation);

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

        if(in_array('symbol',$coreFieldsIds)){
            $validationArr['symbol'] = 'required|unique:psx_mobile_languages,symbol,'.$id;
        }

        if(in_array('name',$coreFieldsIds)){
            $validationArr['name'] = 'required|unique:psx_mobile_languages,name,'.$id;
        }

        if(in_array('language_code',$coreFieldsIds)){
            $validationArr['languageCode'] = 'required|unique:psx_mobile_languages,language_code,'.$id;
        }

        if(in_array('country_code',$coreFieldsIds)){
            $validationArr['countryCode'] = 'required|unique:psx_mobile_languages,country_code,'.$id;
        }

        $validator = Validator::make($request->all(),$validationArr);

        if ($validator->fails()) {
            return redirect()->route(self::editRoute, $id)->with('mobile_language_relation_errors',$errors)
                ->withErrors($validator)
                ->withInput();
        } else {

            if (collect($errors)->isNotEmpty()){
                return redirect()->route(self::editRoute, $id)->with('mobile_language_relation_errors',$errors);
            }
        }

        // validation end

        $mobileLanguage = $this->mobileLanguageService->update($id,$request);

        // if have error
        if (isset($mobileLanguage['error'])){
            $msg = $mobileLanguage['error'];
            return redirectView(self::editRoute, $msg, $this->dangerFlag);
        }

        return redirectView(self::indexRoute, $mobileLanguage);
    }


    public function destroy($id)
    {
        $dataArr = $this->mobileLanguageService->destroy($id);
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }

    // default language
    public function statusChange($id){
        $dataArr = $this->mobileLanguageService->statusChange($id);
        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }

    // enable disable language
    public function enableDisable($id){
//        DB::table('mobile_languages')->update(['status' => 0]);

        $dataArr = $this->mobileLanguageService->enableDisable($id);
        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }
}
