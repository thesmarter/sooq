<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\PhoneCountryCode;



use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Constants\Constants;
use Illuminate\Support\Facades\Validator;
use Modules\Core\Entities\PhoneCountryCode;
use Modules\Core\Http\Services\PhoneCountryCodeService;
use Modules\Core\Http\Requests\StorePhoneCountryCodeRequest;
use Modules\Core\Http\Requests\UpdatePhoneCountryCodeRequest;
use Modules\Core\Http\Services\CoreFieldFilterSettingService;


class PhoneCountryCodeController extends Controller
{
    const parentPath = "phone_country_code/";
    const indexPath = self::parentPath."Index";
    const createPath = self::parentPath."Create";
    const editPath = self::parentPath."Edit";
    const indexRoute = "phone_country_code.index";
    const createRoute = "phone_country_code.create";
    const editRoute = "phone_country_code.edit";

    protected $phoneCountryCodeService, $successFlag, $dangerFlag, $csvFile, $warningFlag, $code, $coreFieldFilterSettingService;

    public function __construct(PhoneCountryCodeService $phoneCountryCodeService, CoreFieldFilterSettingService $coreFieldFilterSettingService)
    {
        $this->phoneCountryCodeService = $phoneCountryCodeService;
        $this->coreFieldFilterSettingService = $coreFieldFilterSettingService;
        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;
        $this->warningFlag = Constants::warning;
        $this->csvFile = Constants::csvFile;
        $this->code = Constants::phoneCountryCode;
    }

    public function index(Request $request)
    {
        $dataArr = $this->phoneCountryCodeService->index(self::indexRoute,$request);
        // check permission
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        return renderView(self::indexPath, $dataArr);
    }

    public function create(Request $request)
    {
        $dataArr = $this->phoneCountryCodeService->create(self::indexRoute);
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
        $errors = validateForCustomField($this->code,$request->phone_country_code_relation);

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

        if(in_array('country_name',$coreFieldsIds)){
            $validationArr['country_name'] = 'required|min:3|unique:psx_phone_country_codes,country_name,'.request()->id;
        }

        if(in_array('country_code',$coreFieldsIds)){
            $validationArr['country_code'] = 'required';
        }

        $validator = Validator::make($request->all(),$validationArr);

        if ($validator->fails()) {
            return redirect()->route(self::createRoute)->with('phone_country_code_relation_errors',$errors)
                ->withErrors($validator)
                ->withInput();
        } else {

            if (collect($errors)->isNotEmpty()){
                return redirect()->route(self::createRoute)->with('phone_country_code_relation_errors',$errors);
            }
        }

        // validation end

        $phone_country_code = $this->phoneCountryCodeService->store($request);

        // if have error
        if ($phone_country_code){
            $msg = $phone_country_code;
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }

        return redirect()->back();
    }

    public function show(PhoneCountryCode $phone_country_code)
    {
        return redirect()->route('phone_country_code.edit', $phone_country_code);
    }

    public function edit($id)
    {
        $dataArr = $this->phoneCountryCodeService->edit($id,self::indexRoute);
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
         $errors = validateForCustomField($this->code,$request->phone_country_code_relation);

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

         if(in_array('country_name',$coreFieldsIds)){
             $validationArr['country_name'] = 'required|min:3|unique:psx_phone_country_codes,country_name,'.$id;
         }

         if(in_array('country_code',$coreFieldsIds)){
             $validationArr['country_code'] = 'required';
         }

         $validator = Validator::make($request->all(),$validationArr);

         if ($validator->fails()) {
             return redirect()->route(self::editRoute, $id)->with('phone_country_code_relation_errors',$errors)
                 ->withErrors($validator)
                 ->withInput();
         } else {

             if (collect($errors)->isNotEmpty()){
                 return redirect()->route(self::editRoute, $id)->with('phone_country_code_relation_errors',$errors);
             }
         }

         // validation end

        $phone_country_code = $this->phoneCountryCodeService->update($id, $request);

        // if have error
        if ($phone_country_code){
            $msg = $phone_country_code;
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }

        return redirect()->back();

    }

    public function destroy($id)
    {
        $dataArr = $this->phoneCountryCodeService->destroy($id);
        // check permission
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }

    public function statusChange($id){

        $dataArr = $this->phoneCountryCodeService->statusChange($id);
        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }

    public function defaultChange($id){

        $dataArr = $this->phoneCountryCodeService->defaultChange($id);
        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }

    public function screenDisplayUiStore(Request $request) {

        $parameter = ['page' => $request->current_page];

        $this->phoneCountryCodeService->makeColumnHideShown($request);

        $msg = 'ScreenDisplay UiSetting is updated.';
        // return redirectView(self::indexRoute, $msg,null,$parameter);
        return redirect()->back();
    }
}
