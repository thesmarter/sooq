<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\LocationTownship;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Constants\Constants;
use Illuminate\Support\Facades\Validator;
use Modules\Core\Http\Services\LocationTownshipService;
use Modules\Core\Http\Requests\StoreLocationTownshipRequest;
use Modules\Core\Http\Requests\UpdateLocationTownshipRequest;
use Modules\Core\Http\Services\CoreFieldFilterSettingService;

class LocationTownshipController extends Controller
{
    const parentPath = "township/";
    const indexPath = self::parentPath."Index";
    const createPath = self::parentPath."Create";
    const editPath = self::parentPath."Edit";
    const indexRoute = "township.index";
    const createRoute = "township.create";
    const editRoute = "township.edit";

    protected $townshipService, $successFlag, $dangerFlag, $csvFile, $warningFlag, $code, $coreFieldFilterSettingService;

    public function __construct(LocationTownshipService $townshipService, CoreFieldFilterSettingService $coreFieldFilterSettingService)
    {
        $this->townshipService = $townshipService;
        $this->coreFieldFilterSettingService = $coreFieldFilterSettingService;
        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;
        $this->warningFlag = Constants::warning;
        $this->csvFile = Constants::csvFile;
        $this->code = Constants::locationTownship;
    }
    public function index(Request $request)
    {
        $dataArr = $this->townshipService->index(self::indexRoute,$request);
//         return $dataArr;
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }

        return renderView(self::indexPath, $dataArr);
    }

    public function screenDisplayUiStore(Request $request) {

        $parameter = ['page' => $request->current_page];

        $this->townshipService->makeColumnHideShown($request);

        $msg = 'ScreenDisplay UiSetting is updated.';
        // return redirectView(self::indexRoute, $msg,null,$parameter);
        return redirect()->back();
    }

    public function create()
    {
        $dataArr = $this->townshipService->create();

        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }

        return renderView(self::createPath, $dataArr);
    }

    public function store(Request $request)
    {
        // validation start
        $errors = validateForCustomField($this->code,$request->township_relation);

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
            $validationArr['name'] = 'required|min:3|unique:psx_location_townships,name,';
        }

        if(in_array('lat',$coreFieldsIds)){
            $validationArr['lat'] = 'required|numeric|max:90|min:-90';
        }


        if(in_array('lng',$coreFieldsIds)){
            $validationArr['lng'] = 'required|numeric|max:180|min:-180';
        }


        if(in_array('location_city_id',$coreFieldsIds)){
            $validationArr['location_city_id'] = 'required';
        }

        // change custom attribute if required start
        $attributes['name'] = "TownShip";
        $attributes['lat'] = "Latitude";
        $attributes['lng'] = "Longitude";
        $attributes['location_city_id'] = "Location City";
        // change custom attribute if required end


        $validator = Validator::make($request->all(),$validationArr,[], $attributes);

        if ($validator->fails()) {
            return redirect()->route(self::createRoute)->with('township_relation_errors',$errors)
                ->withErrors($validator)
                ->withInput();
        } else {

            if (collect($errors)->isNotEmpty()){
                return redirect()->route(self::createRoute)->with('township_relation_errors',$errors);
            }
        }


        // validation end

        $township = $this->townshipService->store($request);

        // if have error
        if (isset($township['error'])){
            $msg = $township['error'];
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }

        return redirectView(self::indexRoute, $township);
    }

    public function edit($townshipId)
    {
        $dataArr = $this->townshipService->edit($townshipId);

        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }

        return renderView(self::editPath, $dataArr);
    }

    public function update(Request $request, $townshipId)
    {
        // validation start
        $errors = validateForCustomField($this->code,$request->township_relation);

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
            $validationArr['name'] = 'required|min:3|unique:psx_location_townships,name,';
        }

        if(in_array('lat',$coreFieldsIds)){
            $validationArr['lat'] = 'required|numeric|max:90|min:-90';
        }


        if(in_array('lng',$coreFieldsIds)){
            $validationArr['lng'] = 'required|numeric|max:180|min:-180';
        }


        if(in_array('location_city_id',$coreFieldsIds)){
            $validationArr['location_city_id'] = 'required';
        }

        // change custom attribute if required start
        $attributes['name'] = "Township Name";
        $attributes['lat'] = "Latitude";
        $attributes['lng'] = "Longitude";
        $attributes['location_city_id'] = "Location City";
        // change custom attribute if required end


        $validator = Validator::make($request->all(),$validationArr,[], $attributes);

        if ($validator->fails()) {
            return redirect()->route(self::editRoute, $townshipId)->with('township_relation_errors',$errors)
                ->withErrors($validator)
                ->withInput();
        } else {

            if (collect($errors)->isNotEmpty()){
                return redirect()->route(self::editRoute, $townshipId)->with('township_relation_errors',$errors);
            }
        }


        // validation end

        $township = $this->townshipService->update($townshipId, $request);

        // if have error
        if (isset($township['error'])){
            $msg = $township['error'];
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }

        return redirectView(self::indexRoute, $township);

    }

    public function destroy($townshipId)
    {
        $dataArr = $this->townshipService->destroy($townshipId);

        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }

        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }

    public function statusChange($townshipId){

        $dataArr = $this->townshipService->statusChange($townshipId);
        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }

    public function importCSV(Request $request){
        $this->townshipService->importCSV($request);
        return back();
    }
}
