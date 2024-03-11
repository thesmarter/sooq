<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\Currency;



use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Constants\Constants;
// use Modules\Core\Entities\Currency;
use Modules\Core\Http\Services\CurrencyService;
use Modules\Core\Http\Requests\StoreCurrencyRequest;
use Modules\Core\Http\Requests\UpdateCurrencyRequest;
use Modules\Core\Http\Services\CoreFieldFilterSettingService;
use Illuminate\Support\Facades\Validator;

class CurrencyController extends Controller
{
    const parentPath = "currency/";
    const indexPath = self::parentPath."Index";
    const createPath = self::parentPath."Create";
    const editPath = self::parentPath."Edit";
    const indexRoute = "currency.index";
    const createRoute = "currency.create";
    const editRoute = "currency.edit";

    protected $currencyService, $successFlag, $dangerFlag, $csvFile, $warningFlag, $code, $coreFieldFilterSettingService;

    public function __construct(CurrencyService $currencyService, CoreFieldFilterSettingService $coreFieldFilterSettingService)
    {
        $this->currencyService = $currencyService;
        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;
        $this->warningFlag = Constants::warning;
        $this->csvFile = Constants::csvFile;
        $this->coreFieldFilterSettingService = $coreFieldFilterSettingService;
        $this->code = Constants::currency;
    }

    public function index(Request $request)
    {
        $dataArr = $this->currencyService->index(self::indexRoute,$request);
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        // return $dataArr;
        return renderView(self::indexPath, $dataArr);
    }
    public function create()
    {
        $dataArr = $this->currencyService->createShow();
        // check permission
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }

        return renderView(self::createPath);
    }
    public function screenDisplayUiStore(Request $request) {

        $parameter = ['page' => $request->current_page];

        $this->currencyService->makeColumnHideShown($request);

        $msg = 'ScreenDisplay UiSetting is updated.';
        // return redirectView(self::indexRoute, $msg,null,$parameter);
        return redirect()->back();
    }

    public function store(Request $request)
    {

         // validation start
         $errors = validateForCustomField($this->code,$request->currency_relation);

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

         if(in_array('currency_short_form',$coreFieldsIds)){
             $validationArr['currency_short_form'] = 'required|min:3|unique:psx_currencies,currency_short_form,';
         }

         if(in_array('currency_symbol',$coreFieldsIds)){
             $validationArr['currency_symbol'] = "required";
         }


         $validator = Validator::make($request->all(),$validationArr);

         if ($validator->fails()) {
             return redirect()->route('currency.create')->with('currency_relation_errors',$errors)
                 ->withErrors($validator)
                 ->withInput();
         } else {

             if (collect($errors)->isNotEmpty()){
                 return redirect()->route('currency.create')->with('currency_relation_errors',$errors);
             }
         }

         // validation end

        $currency = $this->currencyService->create($request);

        // if have error
        if ($currency){
            $msg = $currency;
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }

        return redirect()->back();
    }

    public function show($currency)
    {
        return redirect()->route('currency.edit', $currency);
    }

    public function edit($id)
    {
        $dataArr = $this->currencyService->edit($id);

        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }

        return renderView(self::editPath, $dataArr);
    }

    public function update(Request $request, $id)
    {
        // validation start
        $errors = validateForCustomField($this->code,$request->currency_relation);

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

        if(in_array('currency_short_form',$coreFieldsIds)){
            $validationArr['currency_short_form'] = 'required|min:3|unique:psx_currencies,currency_short_form,';
        }

        if(in_array('currency_symbol',$coreFieldsIds)){
            $validationArr['currency_symbol'] = "required";
        }


        $validator = Validator::make($request->all(),$validationArr);

        if ($validator->fails()) {
            return redirect()->route('currency.edit', $id)->with('currency_relation_errors',$errors)
                ->withErrors($validator)
                ->withInput();
        } else {

            if (collect($errors)->isNotEmpty()){
                return redirect()->route('currency.edit', $id)->with('currency_relation_errors',$errors);
            }
        }

        // validation end

        $currency = $this->currencyService->update($id, $request);

        // if have error
        if ($currency){
            $msg = $currency;
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }

        return redirect()->back();

    }

    public function destroy($id)
    {
        $dataArr = $this->currencyService->destroy($id);

        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }

        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }

    public function statusChange($id){

        $dataArr = $this->currencyService->statusChange($id);
        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }

    public function defaultChange($id){

        $dataArr = $this->currencyService->defaultChange($id);
        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }


    public function import(Request $request)
    {
        $dataArr = $this->currencyService->import($request);
        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }
}
