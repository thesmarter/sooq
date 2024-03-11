<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\Language;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Modules\Core\Constants\Constants;
use Illuminate\Support\Facades\Validator;
use Modules\Core\Http\Services\LanguageService;
use Modules\Core\Http\Requests\StoreLanguageRequest;
use Modules\Core\Http\Requests\UpdateLanguageRequest;
use Modules\Core\Http\Services\CoreFieldFilterSettingService;

class LanguageController extends Controller
{

    const parentPath = "language/";
    const indexPath = self::parentPath."Index";
    const createPath = self::parentPath."Create";
    const editPath = self::parentPath."Edit";
    const indexRoute = "language.index";
    const createRoute = "language.create";
    const editRoute = "language.edit";


    protected $languageService, $successFlag, $dangerFlag, $csvFile, $warningFlag, $code, $coreFieldFilterSettingService;

    public function __construct(LanguageService $languageService, CoreFieldFilterSettingService $coreFieldFilterSettingService)
    {
        $this->languageService = $languageService;
        $this->coreFieldFilterSettingService = $coreFieldFilterSettingService;
        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;
        $this->warningFlag = Constants::warning;
        $this->csvFile = Constants::csvFile;
        $this->code = Constants::language;
    }
    public function index(Request $request)
    {
        $dataArr = $this->languageService->index($request);
         // check permission
         $checkPermission = $dataArr["checkPermission"];
         if (!empty($checkPermission)){
             return $checkPermission;
         }
        return renderView(self::indexPath, $dataArr);
    }

    public function screenDisplayUiStore(Request $request) {

        $parameter = ['page' => $request->current_page];

        $this->languageService->makeColumnHideShown($request);

        $msg = 'ScreenDisplay UiSetting is updated.';
        // return redirectView(self::indexRoute, $msg,null,$parameter);
        return redirect()->back();
    }

    public function create()
    {
        $dataArr = $this->languageService->createView();
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
        $errors = validateForCustomField($this->code,$request->language_relation);

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
            $validationArr['symbol'] = 'required|unique:psx_languages,symbol';
        }

        if(in_array('name',$coreFieldsIds)){
            $validationArr['name'] = 'required|unique:psx_languages,name,';
        }

        $validator = Validator::make($request->all(),$validationArr);

        if ($validator->fails()) {
            return redirect()->route(self::createRoute)->with('language_relation_errors',$errors)
                ->withErrors($validator)
                ->withInput();
        } else {

            if (collect($errors)->isNotEmpty()){
                return redirect()->route(self::createRoute)->with('language_relation_errors',$errors);
            }
        }

        // validation end

        $language = $this->languageService->create($request);

        // if have error
        if (isset($language['error'])){
            $msg = $language['error'];
            return redirectView(self::createRoute, $msg, $this->dangerFlag);
        }

        return redirectView(self::indexRoute, $language);
    }

//    public function show(Language $language)
//    {
//        return redirect()->route('language.edit', $language);
//    }

    public function edit($id)
    {
        $dataArr = $this->languageService->edit($id);
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        return renderView(self::editPath, $dataArr);
    }

    public function update(Request $request, $id)
    {
        // validation start
        $errors = validateForCustomField($this->code,$request->language_relation);

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
            $validationArr['symbol'] = 'required|unique:psx_languages,symbol,'.$id;
        }

        if(in_array('name',$coreFieldsIds)){
            $validationArr['name'] = 'required|unique:psx_languages,name,'.$id;
        }

        $validator = Validator::make($request->all(),$validationArr);

        if ($validator->fails()) {
            return redirect()->route(self::editRoute, $id)->with('language_relation_errors',$errors)
                ->withErrors($validator)
                ->withInput();
        } else {

            if (collect($errors)->isNotEmpty()){
                return redirect()->route(self::editRoute, $id)->with('language_relation_errors',$errors);
            }
        }

        // validation end

        $response = $this->languageService->update($id, $request);
        // if have error
        if ($response && isset($response['error'])){
            $msg = $response['error'];
            return redirectView(self::editRoute, $msg, $this->dangerFlag, $id);
        }

        return redirect()->back();
    }

    public function destroy($id)
    {
        $dataArr = $this->languageService->destroy($id);
         // check permission
        //  $checkPermission = $dataArr["checkPermission"];
        //  if (!empty($checkPermission)){
        //      return $checkPermission;
        //  }
        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }

    public function statusChange($id){

        $dataArr = $this->languageService->statusChange($id);
        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);

    }

    public function generateall(){

        generateAllLanguage();
        return redirect()->back();

    }



    public function languageTable(Request $request){

        $dataArr = $this->languageService->getlanguageTable($request);
        return $dataArr;

    }

    public function getLanguages(){

        $dataArr = $this->languageService->getlanguages();
        return $dataArr;

    }

    public function changeLanguage(Request $request){
        Session::put("applocale", $request->langSymbol);
        return redirect()->back();
    }




}
