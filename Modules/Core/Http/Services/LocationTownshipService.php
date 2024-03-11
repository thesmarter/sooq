<?php
namespace Modules\Core\Http\Services;

use App\Http\Services\PsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\CoreFieldFilterSetting;
use Modules\Core\Entities\CustomizeUi;
use Modules\Core\Entities\CustomizeUiDetail;
use Modules\Core\Entities\LocationCity;
use Modules\Core\Entities\LocationTownship;
use Modules\Core\Entities\ScreenDisplayUiSetting;
use Modules\Core\Imports\LocationTownshipImport;
use Modules\Core\Transformers\Backend\Model\LocationTownship\LocationTownshipWithKeyResource;

class LocationTownshipService extends PsService
{
    protected $locationCityTableName, $backendSettingService, $locationCityNameCol, $unPublish, $successFlag, $dangerFlag, $townshipIdCol, $townshipNameCol, $townshipCityIdCol, $publish, $townshipStatusCol, $locationCityService, $locationTownshipApiRelation, $noContentStatusCode, $successStatus,  $viewAnyAbility, $createAbility, $editAbility, $deleteAbility, $code, $screenDisplayUiKeyCol, $screenDisplayUiIdCol, $screenDisplayUiIsShowCol, $coreFieldFilterModuleNameCol, $customUiIsDelCol, $hide, $show, $delete, $unDelete;

    public function __construct(LocationCityService $locationCityService, BackendSettingService $backendSettingService)
    {
        $this->locationCityService = $locationCityService;
        $this->backendSettingService = $backendSettingService;
        $this->townshipStatusCol = LocationTownship::status;
        $this->townshipIdCol = LocationTownship::id;
        $this->townshipNameCol = LocationTownship::name;
        $this->townshipCityIdCol = LocationTownship::location_city_id;
        $this->townshiptableName = LocationTownship::tableName;
        $this->publish = Constants::publish;
        $this->unPublish = Constants::unPublish;
        $this->show = Constants::show;
        $this->hide = Constants::hide;
        $this->delete = Constants::delete;
        $this->unDelete = Constants::unDelete;

        $this->locationTownshipApiRelation = ['location_city'];

        $this->dangerFlag = Constants::danger;
        $this->successFlag = Constants::success;

        $this->noContentStatusCode = Constants::noContentStatusCode;
        $this->successStatus = Constants::successStatus;

        $this->viewAnyAbility = Constants::viewAnyAbility;
        $this->createAbility = Constants::createAbility;
        $this->editAbility = Constants::editAbility;
        $this->deleteAbility = Constants::deleteAbility;
        $this->code = Constants::locationTownship;

        $this->locationCityTableName = LocationCity::tableName;
        $this->locationCityNameCol = LocationCity::name;

        $this->screenDisplayUiKeyCol = ScreenDisplayUiSetting::key;
        $this->screenDisplayUiIdCol = ScreenDisplayUiSetting::id;
        $this->screenDisplayUiIsShowCol = ScreenDisplayUiSetting::isShow;

        $this->coreFieldFilterModuleNameCol = CoreFieldFilterSetting::moduleName;

        $this->customUiIsDelCol = CustomizeUi::isDelete;

        $this->cityTableName = LocationCity::tableName;
        $this->cityIdCol = LocationCity::id;
        $this->cityNameCol = LocationCity::name;

        $this->dropDownUi = Constants::dropDownUi;
        $this->textUi = Constants::textUi;
        $this->radioUi = Constants::radioUi;
        $this->checkBoxUi = Constants::checkBoxUi;
        $this->dateTimeUi = Constants::dateTimeUi;
        $this->textAreaUi = Constants::textAreaUi;
        $this->numberUi = Constants::numberUi;
        $this->multiSelectUi = Constants::multiSelectUi;
        $this->imageUi = Constants::imageUi;
        $this->timeOnlyUi = Constants::timeOnlyUi;
        $this->dateOnlyUi = Constants::dateOnlyUi;

        $this->uploadPathForDel = Constants::uploadPathForDel;
        $this->thumb1xPathForDel = Constants::thumb1xPathForDel;
        $this->thumb2xPathForDel = Constants::thumb2xPathForDel;
        $this->thumb3xPathForDel = Constants::thumb3xPathForDel;

        $this->customUiModuleName = CustomizeUi::moduleName;
        $this->customUiDetailCoreKeysIdCol = CustomizeUiDetail::coreKeysId;

    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            $township = new LocationTownship();
            $township->name = $request->name;
            $township->location_city_id = $request->location_city_id;
            $township->ordering = $request->ordering;
            $township->lat = $request->lat;
            $township->lng = $request->lng;
            $township->description = $request->description;
            $township->is_featured = $request->is_featured;
            $township->added_user_id = Auth::user()->id;
            $township->save();

            DB::commit();
        } catch (\Throwable $e){
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }

        return $township;
    }

    public function update($townshipId,$request)
    {
        DB::beginTransaction();

        try {
            $township = $this->getLocationTownship($townshipId);
            $township->name = $request->name;
            $township->location_city_id = $request->location_city_id;
            $township->ordering = $request->ordering;
            $township->lat = $request->lat;
            $township->lng = $request->lng;
            $township->description = $request->description;
            $township->is_featured = $request->is_featured;
            $township->updated_user_id = Auth::user()->id;
            $township->update();

            DB::commit();
        } catch (\Throwable $e){
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }

        return $township;
    }

    public function getLocationTownship($id = null, $status = null){

        $township = LocationTownship::when($id, function ($q, $id){
                                            $q->where($this->townshipIdCol, $id);
                                        })
                                        ->when($status, function ($q, $status){
                                            $q->where($this->townshipStatusCol, $status);
                                        })->latest()->first();
        return $township;
    }

    public function getLocationTownships($relation = null, $status =null, $limit = null, $offset = null, $conds = null,$noPagination = null,  $pagPerPage = null){
        $sort = '';
        if(isset($conds['order_by'])){
            $sort = $conds['order_by'];
        }
        $townships = LocationTownship::select($this->townshiptableName.'.*')
                ->when(isset($conds['order_by']) && $conds['order_by'] , function ($q) use($sort){
                    if($sort == 'location_city_id@@name')
                    {
                        $q->join($this->cityTableName, "$this->cityTableName.$this->cityIdCol", '=', $this->townshipCityIdCol);
                        $q->select("$this->cityTableName.$this->cityNameCol as location_city_name","$this->townshiptableName.*");
                    }
                })
                ->when($relation, function ($q, $relation){
                    $q->with($relation);
                })
                ->when($limit, function ($query, $limit) {
                    $query->limit($limit);
                })
                ->when($offset, function ($query, $offset) {
                    $query->offset($offset);
                })
                ->when($status, function ($q, $status){
                    $q->where($this->townshipStatusCol, $status);
                })
                ->when($conds, function ($query, $conds) {
                    $query = $this->searching($query, $conds);
                })
                ->when(empty($sort), function ($query, $conds){
                    $query->orderBy("$this->townshiptableName.added_date", 'desc')->orderBy("$this->townshiptableName.$this->townshipStatusCol", 'desc')->orderBy("$this->townshiptableName.$this->townshipNameCol", 'asc');
                });
                if ($pagPerPage) {
                    $townships = $townships->paginate($pagPerPage)->onEachSide(1)->withQueryString();
                }else{
                    $townships = $townships->get();
                }
        return $townships;
    }

    public function searching($query, $conds){
        if (isset($conds['keyword']) && $conds['keyword']) {
            $conds['searchterm'] = $conds['keyword'];
        }
        // search term
        if (isset($conds['searchterm']) && $conds['searchterm']) {
            $search = $conds['searchterm'];
            $query->where(function ($query) use ($search) {
                $query->where($this->townshiptableName . '.' . $this->townshipNameCol, 'like', '%' . $search . '%');
            });
        }

        if(isset($conds['location_city_id']) && $conds['location_city_id']){
            $location_city_filter=$conds['location_city_id'];
            $query->whereHas('location_city', function($q) use($location_city_filter){
                $q->where($this->townshiptableName .'.'.$this->townshipCityIdCol, $location_city_filter);
            });
        }
        // order by
        if (isset($conds['order_by']) && isset($conds['order_type']) && $conds['order_by'] && $conds['order_type']){

            if($conds['order_by'] == 'id'){
                $query->orderBy("$this->cityTableName.$this->cityIdCol", $conds['order_type']);
            }
            elseif($conds['order_by'] == 'location_city_id@@name'){

                $query->orderBy('location_city_name', $conds['order_type']);
            }
            else{
                $query->orderBy($conds['order_by'], $conds['order_type']);
            }

        }

        return $query;
    }

    public function makeColumnHideShown($request){
        $hideShowFields = $request->value;

//        foreach ($hideShowFields as $hideShowField){
//            $ScreenDisplayUiSetting = ScreenDisplayUiSetting::find($hideShowField[$this->screenDisplayUiIdCol]);
//            $ScreenDisplayUiSetting->is_show = $hideShowField['hidden'] ? $this->hide : $this->show;
//            $ScreenDisplayUiSetting->update();
//        }
        foreach ($hideShowFields as $hideShowField){
            $hideShowFieldData[] = [
                $this->screenDisplayUiIdCol => $hideShowField['id'],
                $this->screenDisplayUiIsShowCol => $hideShowField['hidden'] ? $this->hide : $this->show,
            ];
        }
        ScreenDisplayUiSetting::upsert(
            $hideShowFieldData,
            [$this->screenDisplayUiIdCol], [$this->screenDisplayUiIsShowCol]
        );
    }

    public function makePublishOrUnPublish($townshipId){
        $township = $this->getLocationTownship($townshipId);
        if($township->status == $this->publish){
            $township->status = $this->unPublish;
        }else{
            $township->status = $this->publish;
        }
        $township->updated_user_id = Auth::user()->id;
        $township->update();
        return $township;
    }

    // ------------------------
    public function index($routeName,$request){

        // check permission
        $checkPermission = $this->checkPermission($this->viewAnyAbility,LocationTownship::class, "admin.index");
        // $customizeDetails = $this->getCustomizeFieldAttrs();

        // Search and filter
        $conds['searchterm'] = $request->input('search') ?? '';
        $conds['location_city_id'] = $request->input('city_filter') == 'all'? null  : $request->city_filter;

        $conds['order_by']= null;
        $conds['order_type'] = null;
        $row = $request->input('row') ?? Constants::dataTableDefaultRow;

        if($request->sort_field)
        {
            $conds['order_by'] = $request->sort_field;
            $conds['order_type'] = $request->sort_order;
        }

        $customizeUisByModule = CustomizeUi::where($this->customUiModuleName, $this->code)->latest()->get();
        $customizeDetails = CustomizeUiDetail::whereIn($this->customUiDetailCoreKeysIdCol, $customizeUisByModule->pluck($this->customUiDetailCoreKeysIdCol))->get()->groupBy($this->customUiDetailCoreKeysIdCol)->toArray();

        $townshipRelation =['location_city', 'owner', 'editor'];

        $locationTownships = LocationTownshipWithKeyResource::collection($this->getLocationTownships($townshipRelation,  null, null,null,$conds,  false,$row ));
//        return $locationTownships;
        // taking for column and columnFilterOption
        $columnAndColumnFilter = $this->takingForColumnAndFilterOption();
        $showProductCols = $columnAndColumnFilter['showCoreField'];
        $columnProps = $columnAndColumnFilter['arrForColumnProps'];
        $columnFilterOptionProps = $columnAndColumnFilter['arrForColumnFilterProps'];

        // changing item arr object with new format
        $changedProductObj = $locationTownships;
        $selected_City = $this->locationCityService->getLocationCity($conds['location_city_id']);
        if($conds['order_by'])
        {
            $dataArr = [
                "checkPermission" => $checkPermission,
                'showCoreAndCustomFieldArr' => $columnProps,
                'hideShowFieldForFilterArr' => $columnFilterOptionProps,
                'townships' => $changedProductObj,
                'sort_field' =>$conds['order_by'],
                'sort_order'=>$request->sort_order,
                'search'=>$conds['searchterm'] ,
                'selectedCity'=>$selected_City?$selected_City : '',
                'customizeDetails' => $customizeDetails,
                'customizeHeaders' => $customizeUisByModule
            ];
        }
        else
        {
            $dataArr = [
                "checkPermission" => $checkPermission,
                'showCoreAndCustomFieldArr' => $columnProps,
                'hideShowFieldForFilterArr' => $columnFilterOptionProps,
                'townships' => $changedProductObj,
                'search'=>$conds['searchterm'] ,
                'selectedCity'=>$selected_City?$selected_City : '',
                'customizeDetails' => $customizeDetails,
                'customizeHeaders' => $customizeUisByModule
            ];
        }


        return $dataArr;
    }

    public function create(){
        $checkPermission = $this->checkPermission($this->createAbility,LocationTownship::class, "admin.index");
        $cities = $this->locationCityService->getLocationCities(null, $this->publish);
        $backendSettings = $this->backendSettingService->getBackendSetting();
        $dataArr = [
            'checkPermission' => $checkPermission,
            'cities' => $cities,
            'backendSettings' => $backendSettings,
        ];
        return $dataArr;
    }

    public function edit($townshipId){
        $township = $this->getLocationTownship($townshipId);

        $checkPermission = $this->checkPermission($this->createAbility,$township, "admin.index");

        $cities = $this->locationCityService->getLocationCities(null, $this->publish);
        $backendSettings = $this->backendSettingService->getBackendSetting();

        $dataArr = [
            'checkPermission' => $checkPermission,
            'township' => $township,
            'cities' => $cities,
            'backendSettings' => $backendSettings,
        ];
        return $dataArr;
    }

    public function destroy($townshipId){
        $township = $this->getLocationTownship($townshipId);

        // check permission
        $checkPermission = $this->checkPermission($this->deleteAbility, $township, "admin.index");

        $name = $township->name;
        $township->delete();

        $status = [
            'checkPermission' => $checkPermission,
            'msg' => 'The '.$name.' row has been deleted successfully.',
            'flag' => $this->dangerFlag,
        ];

        return $status;
    }

    public function statusChange($townshipId){

        $township = $this->makePublishOrUnPublish($townshipId);

        $status = [
            'msg' => 'The status of '.$township->name.' row has been updated successfully.',
            'flag' => $this->successFlag,
        ];

        return $status;
    }

    public function importCSV($request){

        $import = new LocationTownshipImport();
        $import->import($request->file('csvFile'));
    }

    public function takingForColumnProps($label, $field, $type, $isShowSorting, $ordering){
        $hideShowCoreAndCustomFieldObj = new \stdClass();
        $hideShowCoreAndCustomFieldObj->label = $label;
        $hideShowCoreAndCustomFieldObj->field = $field;
        $hideShowCoreAndCustomFieldObj->type = $type;
        $hideShowCoreAndCustomFieldObj->sort = $isShowSorting;
        $hideShowCoreAndCustomFieldObj->ordering = $ordering;
        if ($type !== "Image" && $type !== "MultiSelect" && $type !== "Action"){
            $hideShowCoreAndCustomFieldObj->action = 'Action';
        }

        return $hideShowCoreAndCustomFieldObj;
    }

    public function takingForColumnAndFilterOption(){

        // for table
        $hideShowCoreFieldForColumnArr = [];
        $hideShowCustomFieldForColumnArr = [];

        $showProductCols = [];

        // for eye
        $hideShowFieldForColumnFilterArr = [];

        // for control
        $controlFieldArr = [];
        $controlFieldObj = $this->takingForColumnProps(__('core__be_action'), "action", "Action", false, 0);
        array_push($controlFieldArr, $controlFieldObj);

        $code = $this->code;
        $hiddenForCoreAndCustomField = $this->hiddenShownForCoreAndCustomField($this->hide, $code);
        $shownForCoreAndCustomField = $this->hiddenShownForCoreAndCustomField($this->show, $code);
        $hideShowForCoreAndCustomFields = $shownForCoreAndCustomField->merge($hiddenForCoreAndCustomField);

        foreach ($hideShowForCoreAndCustomFields as $showFields){
            if ($showFields->coreField !== null) {

                $label = $showFields->coreField->label_name;
                $field = $showFields->coreField->field_name;
                $colName = $showFields->coreField->field_name;
                $type = $showFields->coreField->data_type;
                $isShowSorting = $showFields->coreField->is_show_sorting;
                $ordering = $showFields->coreField->ordering;


                $cols = $colName;
                $id = $showFields->id;
                $hidden = $showFields->is_show ? false : true;
                $moduleName = $showFields->coreField->module_name;
                $keyId = $showFields->coreField->id;

                $coreFieldObjForColumnsProps = $this->takingForColumnProps($label, $field, $type,$isShowSorting, $ordering);
                $coreFieldObjForColumnFilter = $this->takingForColumnFilterProps($id, $label, $field, $hidden, $moduleName, $keyId);

                array_push($hideShowFieldForColumnFilterArr, $coreFieldObjForColumnFilter);
                array_push($hideShowCoreFieldForColumnArr, $coreFieldObjForColumnsProps);
                array_push($showProductCols, $cols);

            }if ($showFields->customizeField !== null) {

                $label = $showFields->customizeField->name;
                $uiHaveAttribute = [$this->dropDownUi, $this->radioUi];
                if (in_array($showFields->customizeField->ui_type_id, $uiHaveAttribute)){
                    $field = $showFields->customizeField->core_keys_id."@@name";
                } else {
                    $field = $showFields->customizeField->core_keys_id;
                }
                if($showFields->customizeField->ui_type_id == $this->multiSelectUi){
                    $type = "MultiSelect";
                } elseif($showFields->customizeField->ui_type_id == $this->imageUi) {
                    $type = "Image";
                } else {
                    $type = $showFields->customizeField->data_type;
                }

                $id = $showFields->id;
                $hidden = $showFields->is_show ? false : true;
                $moduleName = $showFields->customizeField->module_name;
                $keyId = $showFields->customizeField->core_keys_id;
                $isShowSorting = $showFields->customizeField->is_show_sorting;
                $ordering = $showFields->customizeField->ordering;

                $customFieldObjForColumnsProps = $this->takingForColumnProps($label, $field, $type,$isShowSorting, $ordering);
                $customFieldObjForColumnFilter = $this->takingForColumnFilterProps($id, $label, $field, $hidden, $moduleName, $keyId);

                array_push($hideShowFieldForColumnFilterArr, $customFieldObjForColumnFilter);
                array_push($hideShowCustomFieldForColumnArr, $customFieldObjForColumnsProps);

            }
        }

        // for columns props
        $showCoreAndCustomFieldArr = array_merge($hideShowCoreFieldForColumnArr, $controlFieldArr, $hideShowCustomFieldForColumnArr);
        $sortedColumnArr = columnOrdering("ordering", $showCoreAndCustomFieldArr);

        // for eye
        $hideShowCoreAndCustomFieldArr = array_merge($hideShowFieldForColumnFilterArr);

        $dataArr = [
            "arrForColumnProps" => $sortedColumnArr,
            "arrForColumnFilterProps" => $hideShowCoreAndCustomFieldArr,
            "showCoreField" => $showProductCols,
        ];
        return $dataArr;

    }

    public function hiddenShownForCoreAndCustomField($isShown, $code){
        $hiddenShownForFields = ScreenDisplayUiSetting::with(['customizeField' => function ($q){
            $q->where($this->customUiIsDelCol, $this->unDelete);
        }, 'coreField' => function($q){
            $q->where($this->coreFieldFilterModuleNameCol, $this->code);
        }])
            ->where($this->coreFieldFilterModuleNameCol, $code)
            ->where($this->screenDisplayUiIsShowCol, $isShown)
            ->get();
        return $hiddenShownForFields;
    }

    public function takingForColumnFilterProps($id, $label, $field, $hidden, $moduleName, $keyId){
        $hideShowCoreAndCustomFieldObj = new \stdClass();
        $hideShowCoreAndCustomFieldObj->id = $id;
        $hideShowCoreAndCustomFieldObj->label = $label;
        $hideShowCoreAndCustomFieldObj->key = $field;
        $hideShowCoreAndCustomFieldObj->hidden = $hidden;
        $hideShowCoreAndCustomFieldObj->module_name = $moduleName;
        $hideShowCoreAndCustomFieldObj->key_id = $keyId;

        return $hideShowCoreAndCustomFieldObj;
    }

    // for api
    public function noDataError($offset, $data){
        if ($offset > 0 && $data->isEmpty()) {
            // no paginate data
            $data = [];
            return responseDataApi($data);

        } else if($data->isEmpty()) {
            // no data db
            return responseMsgApi(__('core__no_data'), $this->noContentStatusCode, $this->successStatus);
        }
    }

    public function searchFromApi($request)
    {
        $offset = $request->offset;
        $limit = $request->limit;

        $conds['keyword'] = $request->keyword;
        $conds['location_city_id'] = $request->city_id;
        $conds['order_by'] = $request->order_by;
        $conds['order_type'] = $request->order_type;

        $locationTownshipApiRelation = $this->locationTownshipApiRelation;
        $townships = $this->getLocationTownships($locationTownshipApiRelation, $this->publish, $limit, $offset, $conds);

        return $townships;
    }
}
