<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\AvailableCurrency;



use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Constants\Constants;
// use Modules\Core\Entities\AvailableCurrency;
use Illuminate\Support\Facades\Validator;
use Modules\Core\Http\Services\AvailableCurrencyService;
use Modules\Core\Http\Requests\StoreAvailableCurrencyRequest;
use Modules\Core\Http\Services\CoreFieldFilterSettingService;
use Modules\Core\Http\Requests\UpdateAvailableCurrencyRequest;


class AvailableCurrencyController extends Controller
{
    const parentPath = "currency_available/";
    const indexPath = self::parentPath."Index";
    const createPath = self::parentPath."Create";
    const editPath = self::parentPath."Edit";
    const indexRoute = "available_currency.index";
    const createRoute = "available_currency.create";
    const editRoute = "available_currency.edit";

    protected $availableCurrencyService, $successFlag, $dangerFlag, $csvFile, $warningFlag, $code, $coreFieldFilterSettingService;

    public function __construct(AvailableCurrencyService $availableCurrencyService, CoreFieldFilterSettingService $coreFieldFilterSettingService)
    {
        $this->availableCurrencyService = $availableCurrencyService;
        $this->coreFieldFilterSettingService = $coreFieldFilterSettingService;
        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;
        $this->warningFlag = Constants::warning;
        $this->csvFile = Constants::csvFile;
        $this->code = Constants::availableCurrency;
    }

    public function index(Request $request)
    {
        $dataArr = $this->availableCurrencyService->index(self::indexRoute,$request);
        // check permission
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        return renderView(self::indexPath, $dataArr);
    }

    public function create(Request $request)
    {
        $dataArr = $this->availableCurrencyService->create(self::indexRoute);
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
        $errors = validateForCustomField($this->code,$request->available_currency_relation);

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

        if(in_array('currency_short_form',$coreFieldsIds)){
            $validationArr['currency_short_form'] = 'required|min:3|unique:psx_available_currencies,currency_short_form,';
        }

        if(in_array('currency_symbol',$coreFieldsIds)){
            $validationArr['currency_symbol'] = 'required';
        }

        if(in_array('name',$coreFieldsIds)){
            $validationArr['name'] = 'required';
        }

        // change custom attribute if required start
        $attributes['currency_symbol'] = "Currency Symbol";
        $attributes['currency_short_form'] = "Currency Short Form";
        // change custom attribute if required end

        $validator = Validator::make($request->all(),$validationArr,[], $attributes);

        if ($validator->fails()) {
            return redirect()->route(self::createRoute)->with('available_currency_relation_errors',$errors)
                ->withErrors($validator)
                ->withInput();
        } else {

            if (collect($errors)->isNotEmpty()){
                return redirect()->route(self::createRoute)->with('available_currency_relation_errors',$errors);
            }
        }

        // validation end

        $available_currency = $this->availableCurrencyService->store($request);

        // if have error
        if ($available_currency){
            $msg = $available_currency;
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }

        return redirect()->back();
    }

    public function show($available_currency)
    {
        return redirect()->route('available_currency.edit', $available_currency);
    }

    public function edit($id)
    {
        $dataArr = $this->availableCurrencyService->edit($id,self::indexRoute);
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
        $errors = validateForCustomField($this->code,$request->available_currency_relation);

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

        if(in_array('currency_short_form',$coreFieldsIds)){
            $validationArr['currency_short_form'] = 'required|min:3|unique:psx_available_currencies,currency_short_form,'.$id;
        }

        if(in_array('currency_symbol',$coreFieldsIds)){
            $validationArr['currency_symbol'] = 'required';
        }

        if(in_array('name',$coreFieldsIds)){
            $validationArr['name'] = 'required';
        }

        // change custom attribute if required start
        $attributes['currency_symbol'] = "Currency Symbol";
        $attributes['currency_short_form'] = "Currency Short Form";
        // change custom attribute if required end

        $validator = Validator::make($request->all(),$validationArr,[], $attributes);

        if ($validator->fails()) {
            return redirect()->route(self::editRoute, $id)->with('available_currency_relation_errors',$errors)
                ->withErrors($validator)
                ->withInput();
        } else {

            if (collect($errors)->isNotEmpty()){
                return redirect()->route(self::editRoute, $id)->with('available_currency_relation_errors',$errors);
            }
        }

        // validation end

        $available_currency = $this->availableCurrencyService->update($id, $request);

        // if have error
        if ($available_currency){
            $msg = $available_currency;
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }

        return redirect()->back();

    }

    public function destroy($id)
    {
        $dataArr = $this->availableCurrencyService->destroy($id);
        // check permission
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }

    public function statusChange($id){

        $dataArr = $this->availableCurrencyService->statusChange($id);
        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }

    public function defaultChange($id){

        $dataArr = $this->availableCurrencyService->defaultChange($id);
        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }

    public function screenDisplayUiStore(Request $request) {

        $parameter = ['page' => $request->current_page];

        $this->availableCurrencyService->makeColumnHideShown($request);

        $msg = 'ScreenDisplay UiSetting is updated.';
        // return redirectView(self::indexRoute, $msg,null,$parameter);
        return redirect()->back();
    }
}
