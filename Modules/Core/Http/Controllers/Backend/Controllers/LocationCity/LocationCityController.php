<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\LocationCity;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Illuminate\Support\Facades\Auth;
use Modules\Core\Constants\Constants;
// use Modules\Core\Entities\LocationCity;
use Illuminate\Support\Facades\Validator;
use Modules\Core\Http\Services\ImageService;
use Modules\Core\Imports\LocationCityImport;
use Modules\Core\Http\Services\CustomFieldService;
use Modules\Core\Http\Services\LocationCityService;
use Modules\Core\Http\Services\CoreFieldFilterSettingService;

class LocationCityController extends Controller
{
    const parentPath = "location_city/";
    const indexPath = self::parentPath . "Index";
    const createPath = self::parentPath . "Create";
    const editPath = self::parentPath . "Edit";
    const indexRoute = "city.index";
    const createRoute = "city.create";
    const editRoute = "city.edit";

    protected $cityService, $imageService, $customFieldService, $code, $coreFieldFilterSettingService;

    public function __construct(LocationCityService $cityService, ImageService $imageService, CustomFieldService $customFieldService, CoreFieldFilterSettingService $coreFieldFilterSettingService)
    {
        $this->cityService = $cityService;
        $this->imageService = $imageService;
        $this->customFieldService = $customFieldService;
        $this->coreFieldFilterSettingService = $coreFieldFilterSettingService;
        $this->code = Constants::locationCity;
    }

    public function index(Request $request)
    {
        $dataArr = $this->cityService->index(self::indexRoute,$request);
        // return $dataArr;
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }

        return renderView(self::indexPath, $dataArr);
    }

    public function screenDisplayUiStore(Request $request) {

        $parameter = ['page' => $request->current_page];

        $this->cityService->makeColumnHideShown($request);

        $msg = 'ScreenDisplay UiSetting is updated.';
        // return redirectView(self::indexRoute, $msg,null,$parameter);
        return redirect()->back();
    }


    public function create()
    {
        $dataArr = $this->cityService->create(self::indexRoute);

        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }

        return renderView(self::createPath, $dataArr);
    }

    public function store(Request $request)
    {

        // validation start
        $errors = validateForCustomField($this->code,$request->city_relation);

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
            $validationArr['name'] = 'required|min:3|unique:psx_location_cities,name,';
        }

        if(in_array('lat',$coreFieldsIds)){
            $validationArr['lat'] = 'required|numeric|max:90|min:-90';
        }


        if(in_array('lng',$coreFieldsIds)){
            $validationArr['lng'] = 'required|numeric|max:180|min:-180';
        }


        $validationArr['city_relation.ps-loc00002'] = 'email';

        // change custom attribute if required start
        $attributes['name'] = "City";
        $attributes['lat'] = "Latitude";
        $attributes['lng'] = "Longitude";
        // change custom attribute if required end


        $validator = Validator::make($request->all(),$validationArr,[], $attributes);

        if ($validator->fails()) {
            return redirect()->route(self::createRoute)->with('city_relation_errors',$errors)
                ->withErrors($validator)
                ->withInput();
        } else {

            if (collect($errors)->isNotEmpty()){
                return redirect()->route(self::createRoute)->with('city_relation_errors',$errors);
            }
        }


        // validation end


        $city = $this->cityService->store($request);
        if ($city){
            // go back to index
            $msg = $city;
            // return $msg;
            return redirectView(self::indexRoute, $msg);
        }

        // $msg = "Location city has been added successfully.";
        $msg = __('core__be_added_success',['attribute'=>'Location City']);
        return redirectView(self::indexRoute, $msg);

    }

    public function edit($id)
    {
        $dataArr = $this->cityService->edit($id, self::indexRoute);

        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }

        return renderView(self::editPath, $dataArr);
    }

    public function update(Request $request)
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
            $validationArr['name'] = 'required|min:3|unique:psx_location_cities,name,' . $request->id;
        }

        if(in_array('lat',$coreFieldsIds)){
            $validationArr['lat'] = 'required|numeric|max:90|min:-90';
        }


        if(in_array('lng',$coreFieldsIds)){
            $validationArr['lng'] = 'required|numeric|max:180|min:-180';
        }


        $validationArr['city_relation.ps-loc00002'] = 'email';

        // change custom attribute if required start
        $attributes['name'] = "City Name";
        $attributes['lat'] = "Latitude";
        $attributes['lng'] = "Longitude";
        // change custom attribute if required end


        $validator = Validator::make($request->all(),$validationArr,[], $attributes);

        if ($validator->fails()) {
            return redirect()->route(self::editRoute,$request->id)->with('city_relation_errors',$errors)
                ->withErrors($validator)
                ->withInput();
        } else {
            if (collect($errors)->isNotEmpty()){
                return redirect()->route(self::editRoute,$request->id)->with('city_relation_errors',$errors);
            }
        }

        // validation end


        $city = $this->cityService->update($request);

        // if ($city){
        //     // go back to index
        //     $msg = $city;
        //     return $msg;
        //     return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        // }

        $msg = 'Location city is updated.';
        return redirect()->route(self::indexRoute, $msg);


    }

    public function destroy($id)
    {
        $dataArr = $this->cityService->destroy($id, self::indexRoute);

        // go back to index
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }

        // $msg = $dataArr['title']." has been deleted successfully.";
        $msg = __('core__be_delete_success',['attribute'=>$dataArr["title"]]);

        return redirectView(self::indexRoute, $msg);
    }


    public function statusChange($id){

        $city = $this->cityService->getLocationCity($id);

        if($city->status == 1){
            $city->status = 0;
        }else{
            $city->status = 1;
        }
        $city->updated_user_id = Auth::user()->id;
        $city->update();

        $status = [
            'flag' => 'success',
            // 'msg' => 'The status of '.$city->name.' row has been updated successfully.',
            'msg' => __('core__be_status_attribute_updated',['attribute'=>$city->name]),
        ];

        return redirect()->route('city.index')->with('status',$status);
    }

    public function importCSV(Request $request){



        $import = new LocationCityImport();
        $import->import($request->file('csvFile'));
        return back();
    }

}
