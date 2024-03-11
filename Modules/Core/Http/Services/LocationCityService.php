<?php
namespace Modules\Core\Http\Services;

use App\Http\Services\PsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\CustomizeUi;
use Modules\Core\Entities\LocationCity;
use Modules\Core\Entities\LocationCityInfo;
use Modules\Core\Entities\CustomizeUiDetail;
use Modules\Core\Entities\CoreFieldFilterSetting;
use Modules\Core\Entities\ScreenDisplayUiSetting;
use Modules\Core\Http\Services\BackendSettingService;
use Modules\Core\Transformers\Backend\Model\LocationCity\LocationCityWithKeyResource;

class LocationCityService extends PsService
{
    protected $backendSettingService,$customUiDetailCoreKeysIdCol, $cityDescCol, $cityIdCol, $cityTableName, $cityStatusCol, $cityNameCol, $publish, $unPublish, $delete, $unDelete, $noContentStatusCode, $successStatus,  $viewAnyAbility, $createAbility, $editAbility, $deleteAbility, $code, $screenDisplayUiKeyCol, $screenDisplayUiIdCol, $screenDisplayUiIsShowCol, $coreFieldFilterModuleNameCol, $customUiIsDelCol, $show, $hide, $customUiIdCol, $customUiCoreKeysIdCol, $customUiEnableCol, $customUiMandatoryCol, $customUiModuleName, $dropDownUi, $textUi, $radioUi, $checkBoxUi, $dateTimeUi, $numberUi, $textAreaUi, $multiSelectUi, $imageUi, $timeOnlyUi, $dateOnlyUi;

    public function __construct(BackendSettingService $backendSettingService)
    {
        $this->cityTableName = LocationCity::tableName;
        $this->cityStatusCol = LocationCity::status;
        $this->cityNameCol = LocationCity::name;
        $this->cityIdCol = LocationCity::id;
        $this->cityDescCol = LocationCity::description;
        $this->cityAddedUserIdCol = LocationCity::addedUserId;

        $this->backendSettingService = $backendSettingService;

        $this->cityInfoIdCol = LocationCityInfo::id;
        $this->cityInfoItemIdCol = LocationCityInfo::locationCityId;
        $this->cityInfoCoreKeysIdCol = LocationCityInfo::coreKeysId;
        $this->cityInfoTableName = LocationCityInfo::tableName;
        $this->cityInfoValueCol = LocationCityInfo::value;

        $this->publish = Constants::publish;
        $this->unPublish = Constants::unPublish;
        $this->show = Constants::show;
        $this->hide = Constants::hide;
        $this->delete = Constants::delete;
        $this->unDelete = Constants::unDelete;

        $this->noContentStatusCode = Constants::noContentStatusCode;
        $this->successStatus = Constants::successStatus;

        $this->viewAnyAbility = Constants::viewAnyAbility;
        $this->createAbility = Constants::createAbility;
        $this->editAbility = Constants::editAbility;
        $this->deleteAbility = Constants::deleteAbility;
        $this->code = Constants::locationCity;

        $this->screenDisplayUiKeyCol = ScreenDisplayUiSetting::key;
        $this->screenDisplayUiIdCol = ScreenDisplayUiSetting::id;
        $this->screenDisplayUiIsShowCol = ScreenDisplayUiSetting::isShow;

        $this->coreFieldFilterModuleNameCol = CoreFieldFilterSetting::moduleName;

        $this->customUiIdCol = CustomizeUi::id;
        $this->customUiCoreKeysIdCol = CustomizeUi::coreKeysId;
        $this->customUiUiTypeIdCol = CustomizeUi::uiTypeId;
        $this->customUiEnableCol = CustomizeUi::enable;
        $this->customUiIsDelCol = CustomizeUi::isDelete;
        $this->customUiMandatoryCol = CustomizeUi::mandatory;
        $this->customUiModuleName = CustomizeUi::moduleName;

        $this->customUiDetailCoreKeysIdCol = CustomizeUiDetail::coreKeysId;
        $this->customUiDetailTableName = CustomizeUiDetail::tableName;
        $this->customUiDetailIdCol = CustomizeUiDetail::id;



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

    }

    // for Backend
    public function storeCoreFieldValues($request, $status = null){

        $city = new LocationCity();

        if (isset($request->name) && !empty($request->name)) {
            $city->name = $request->name;
        }

        if (isset($request->ordering)) {
            $city->ordering = $request->ordering;
        }

        if (isset($request->lat) && !empty($request->lat)) {
            $city->lat = $request->lat;
        }

        if (isset($request->lng) && !empty($request->lng)) {
            $city->lng = $request->lng;
        }

        if (isset($request->description) && !empty($request->description)) {
            $city->description = $request->description;
        }

        if (isset($request->is_featured) && !empty($request->is_featured)) {
            $city->is_featured = $request->is_featured;
        }

        if (isset($request->status) && !empty($request->status)) {
            $city->status = $request->status;
        }

        if (isset($request->added_user_id) && !empty($request->added_user_id)) {
            $city->added_user_id = $request->added_user_id;
        } else {
            if(Auth::check()){
                $city->added_user_id = Auth::user()->id;
            }else{
                $city->added_user_id = 0;
            }
        }

        $city->save();

        return $city;
    }

    public function updateCoreFieldValues($request){
        $city = $this->getLocationCity($request->id);

        if (isset($request->name) && !empty($request->name)) {
            $city->name = $request->name;
        }

        if (isset($request->ordering)) {
            $city->ordering = $request->ordering;
        }

        if (isset($request->lat) && !empty($request->lat)) {
            $city->lat = $request->lat;
        }

        if (isset($request->lng) && !empty($request->lng)) {
            $city->lng = $request->lng;
        }

        if (isset($request->description) && !empty($request->description)) {
            $city->description = $request->description;
        }

        if (isset($request->is_featured) && !empty($request->is_featured)) {
            $city->is_featured = $request->is_featured;
        }

        if (isset($request->status) && !empty($request->status)) {
            $city->status = $request->status;
        }

        if (isset($request->added_user_id) && !empty($request->added_user_id)) {
            $city->added_user_id = $request->added_user_id;
        } else {
            if (Auth::check()) {
                $city->added_user_id = Auth::user()->id;
            } else {
                $city->added_user_id = 0;
            }
        }

        $city->update();
        return $city;
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            // save in location city table
            $city = $this->storeCoreFieldValues($request);

            // save in location_city_info table
            $customizeHeaders = $this->getCustomizeFields($this->code,null,null,0);
            $customizeHeaderIds = $this->getValueByPluck($customizeHeaders, $this->customUiCoreKeysIdCol);

            $this->storeCustomFieldValues($request, $city, $customizeHeaderIds);

            DB::commit();

        } catch (\Throwable$e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }

        return $city;
    }

    public function update($request)
    {

        DB::beginTransaction();
        try {
            // update in location_cities table
            $city = $this->updateCoreFieldValues($request);

            // save in location_city_infos table
            $data = $this->updateCustomFieldOldValues($request, $city);

            $newDataIndexs = array_diff(array_unique($data['allDataIndex']), array_unique($data['oldDataIndex']));

            $this->storeCustomFieldValues($request, $city, $newDataIndexs);

            DB::commit();
            return $city;
        } catch (\Throwable$e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public function getLocationCities($relation = null, $status =null, $limit = null, $offset = null, $conds = null, $noPagination = null, $pagPerPage = null,$condsIn=null){
        $sql = $this->getSqlForCustomField();
        $sort = '';
        if(isset($conds['order_by'])){
            $sort = $conds['order_by'];
        }

        $cities = LocationCity::select($this->cityTableName.'.*')
        ->when($sql,function($query,$sql){
            $query->selectRaw($sql);
            $query->leftJoin($this->cityInfoTableName, $this->cityTableName.'.'.$this->cityIdCol,'=',$this->cityInfoTableName.'.'.$this->cityInfoItemIdCol);
            $query->leftJoin($this->customUiDetailTableName, "$this->cityInfoTableName.$this->cityInfoValueCol",'=', "$this->customUiDetailTableName.$this->customUiDetailIdCol");
        })
        ->groupBy($this->cityTableName.'.'.$this->cityIdCol)
       ->when($relation, function ($q, $relation) {
            $q->with($relation);
        })
        ->when($status, function ($q, $status){
            $q->where($this->cityStatusCol, $status);
        })
        ->when($limit, function ($query, $limit) {
            $query->limit($limit);
        })->when($conds, function ($query, $conds) {
            $query = $this->searching($query, $conds);
        })->when($condsIn, function ($query, $condsIn) {
            if (isset($condsIn['ids']))
                $query->whereIn($this->cityTableName.'.'.$this->cityIdCol, $condsIn['ids']);

            if (isset($condsIn['added_user_ids']))
                $query->whereIn($this->cityTableName.'.'.$this->cityAddedUserIdCol, $condsIn['added_user_ids']);

        })
        ->when($offset, function ($query, $offset) {
            $query->offset($offset);
        })
        ->when(empty($sort), function ($query, $conds){
            $query->orderBy("added_date","desc")->orderBy("$this->cityTableName.$this->cityStatusCol", 'desc')->orderBy("$this->cityTableName.$this->cityNameCol", 'asc');
        });

        if ($pagPerPage){
        $cities = $cities->paginate($pagPerPage)->onEachSide(1)->withQueryString();

        } else{
            $cities = $cities->get();
        }
        return $cities;

        // $cities = LocationCity::when($relation, function ($q, $relation){
        //                             $q->with($relation);
        //                         })
        //                         ->when($status, function ($q, $status){
        //                             $q->where($this->cityStatusCol, $status);
        //                         })
        //                         ->when($limit, function ($query, $limit) {
        //                             $query->limit($limit);
        //                         })
        //                         ->when($offset, function ($query, $offset) {
        //                             $query->offset($offset);
        //                         })
        //                         ->when($conds, function ($query, $conds) {
        //                             // search term
        //                             if (isset($conds['keyword']) && $conds['keyword']) {
        //                                 $query->where($this->cityNameCol, 'LIKE', '%' . $conds['keyword'] . '%');
        //                             }

        //                             // order by
        //                             if (isset($conds['order_by']) && isset($conds['order_type']) && $conds['order_by'] && $conds['order_type'])
        //                                 $query->orderBy($conds['order_by'], $conds['order_type']);
        //                             else if (isset($conds['order_by']) && $conds['order_by'])
        //                                 $query->orderBy($conds['order_by']);
        //                         })
        //                         ->latest()->get();
        // return $cities;
    }

    public function getLocationCity($id,$dataWithRelation = null){
        $city = LocationCity::when($dataWithRelation, function ($q, $data){
            $q->with($data);
        })->find($id);
        return $city;
    }

    public function getLocationCityList($relation = null, $status = null, $limit = null, $offset = null, $conds = null,$noPagination = null,  $pagPerPage = null){
        $cities = LocationCity::when($relation, function ($q, $relation){
                                    $q->with($relation);
                                })
                                ->when($status, function ($q, $status){
                                    $q->where($this->cityStatusCol, $status);
                                })
                                ->when($limit, function ($query, $limit) {
                                    $query->limit($limit);
                                })
                                ->when($offset, function ($query, $offset) {
                                    $query->offset($offset);
                                })
                                ->when($conds, function ($query, $conds) {
                                    // search term
                                    if (isset($conds['keyword']) && $conds['keyword']) {
                                        $query->where($this->cityNameCol, 'LIKE', '%' . $conds['keyword'] . '%');
                                    }

                                    // order by
                                    if (isset($conds['order_by']) && isset($conds['order_type']) && $conds['order_by'] && $conds['order_type'])
                                        $query->orderBy($conds['order_by'], $conds['order_type']);
                                    else if (isset($conds['order_by']) && $conds['order_by'])
                                        $query->orderBy($conds['order_by']);
                                })
                                ->latest()->get();
        return $cities;
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

                $coreFieldObjForColumnsProps = $this->takingForColumnProps($label, $field, $type, $isShowSorting, $ordering);
                $coreFieldObjForColumnFilter = $this->takingForColumnFilterProps($id, $label, $field, $hidden, $moduleName, $keyId);

                array_push($hideShowFieldForColumnFilterArr, $coreFieldObjForColumnFilter);
                array_push($hideShowCoreFieldForColumnArr, $coreFieldObjForColumnsProps);
                array_push($showProductCols, $cols);

            }if ($showFields->customizeField !== null) {

                $label = $showFields->customizeField->name;
                $isShowSorting = $showFields->customizeField->is_show_sorting;
                $ordering = $showFields->customizeField->ordering;

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

                $customFieldObjForColumnsProps = $this->takingForColumnProps($label, $field, $type, $isShowSorting, $ordering);
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

    public function storeCustomFieldValues($request, $item, $coreKeysIds){

        if (!empty($request->city_relation)){
            foreach ($coreKeysIds as $coreKeysId){
                foreach ($request->city_relation as $key => $value){
                    if (is_array($value)){
                        $coreKeysIdFromReq = $value['core_keys_id'];
                        $valueFromReq = $value['value'];
                    } else {
                        $coreKeysIdFromReq = $key;
                        $valueFromReq = $value;
                    }

                    if ($coreKeysIdFromReq === $coreKeysId){
                        $cityRelation = new LocationCityInfo();

                        if(is_file($valueFromReq)) {
                            $file = $this->checkFileInCustomFieldValue($valueFromReq);
                        }

                        $cityRelation->location_city_id = $item->id;

                        if ($valueFromReq === false) {
                            $cityRelation->value = 0;
                        }
                        if(is_file($valueFromReq)) {
                            if (str_contains($valueFromReq->getMimeType(), 'image')) {
                                $cityRelation->value = $file;
                            }
                        }
                        if(!is_file($valueFromReq) && $valueFromReq !== false) {
                            $cityRelation->value = $valueFromReq;
                        }

                        $customizeHeaders = $this->getCustomizeFields($this->code,null,null,0);
                        foreach ($customizeHeaders as $key2=>$customizeHeader){
                            if($coreKeysIdFromReq === $customizeHeader->core_keys_id){
                                $cityRelation->ui_type_id = $customizeHeader->ui_type_id;
                                $cityRelation->core_keys_id = $customizeHeader->core_keys_id;
                            }
                        }

                        if (Auth::check()) {
                            $cityRelation->added_user_id = Auth::id();
                        } else {
                            $cityRelation->added_user_id = 0;
                        }

                        $cityRelation->save();
                    }
                }
            }
        }
    }

    public function updateCustomFieldOldValues($request, $item){

        $allDataIndex = [];
        $oldDataIndex = [];
        if(isset($request->city_relation)) {
            foreach ($request->city_relation as $key => $value) {
                if (is_array($value)) {
                    $coreKeysIdFromReq = $value['core_keys_id'];
                    $valueFromReq = $value['value'];

                    array_push($allDataIndex, $value['core_keys_id']);
                } else {
                    $coreKeysIdFromReq = $key;
                    $valueFromReq = $value;

                    array_push($allDataIndex, $key);
                }

                if ($valueFromReq !== null && $valueFromReq !== "undefined") {

                    $cityRelations = LocationCityInfo::where('location_city_id', $item->id)->get();

                    foreach ($cityRelations as $key3 => $cityRelation) {

                        array_push($oldDataIndex, $cityRelation->core_keys_id);

                        if ($coreKeysIdFromReq == $cityRelation->core_keys_id) {

                            if (is_file($valueFromReq)) {
                                // del file from local first
                                delImageFromCustomFieldValue($cityRelation, $this->uploadPathForDel, $this->thumb1xPathForDel, $this->thumb2xPathForDel, $this->thumbnail3xPath);

                                // save new file in local again
                                $file = $this->checkFileInCustomFieldValue($valueFromReq);
                            }

                            // update in db start
                            $cityRelation->location_city_id = $item->id;
                            if ($valueFromReq === false) {
                                $cityRelation->value = 0;
                            }
                            if (is_file($valueFromReq)) {
                                if (str_contains($valueFromReq->getMimeType(), 'image')) {
                                    $cityRelation->value = $file;
                                }
                            }
                            if (!is_file($valueFromReq) && $valueFromReq !== false) {
                                $cityRelation->value = $valueFromReq;
                            }

                            $customizeHeaders = CustomizeUi::where('module_name', $this->code)->get();
                            foreach ($customizeHeaders as $key2 => $customizeHeader) {
                                if ($coreKeysIdFromReq === $customizeHeader->core_keys_id) {
                                    $cityRelation->ui_type_id = $customizeHeader->ui_type_id;
                                    $cityRelation->core_keys_id = $customizeHeader->core_keys_id;
                                }
                            }
                            if(Auth::check()){
                                $cityRelation->updated_user_id = Auth::id();
                            }else{
                                $cityRelation->updated_user_id = 0;
                            }
                            $cityRelation->update();
                            // update in db end
                        }
                    }

                }
            }
        }

        $dataArr = [
            'allDataIndex' => $allDataIndex,
            'oldDataIndex' => $oldDataIndex
        ];

        return $dataArr;
    }

    public function checkFileInCustomFieldValue($value){

        if (str_contains($value->getMimeType(),'image')){
            $img = Image::make($value);

            // change file to new name
            $file = uniqid()."_city.".$value->getClientOriginalExtension();

            saveImgAsOriginalThumbNail1x2x3x($value, $file,$this->originPath, $this->thumbnail1xPath, $this->thumbnail2xPath, $this->thumbnail3xPath);

        } else if(str_contains($value->getMimeType(),'video')){
            return 'This is video';
        } else {
            return 'other';
        }
        return $file;

    }

    public function getCustomizeFields($code = null, $dataWithRelation = null, $coreKeysId = null, $isDel = null, $limit = null, $offset = null){
        $customizeHeader  =  CustomizeUi::when($dataWithRelation, function ($q, $data){
                                $q->with($data);
                             })
                            ->when($code, function ($q, $data){
                                $q->where($this->customUiModuleName, $data);
                            })
                            ->when($isDel !== null,  function ($q) use ($isDel){
                                $q->where($this->customUiIsDelCol, $isDel);
                            })
                            ->when($limit, function ($query,$limit){
                                $query->limit($limit);
                            })
                            ->when($offset, function ($query,$offset){
                                $query->offset($offset);
                            })
                            ->when($coreKeysId, function ($q, $data){
                                $q->where($this->customUiCoreKeysIdCol, $data);
                            })
                            ->get();
        return $customizeHeader;
    }

    public function getCustomizeField($code = null, $dataWithRelation = null, $coreKeysId = null){
        $customizeHeader  =  CustomizeUi::when($dataWithRelation, function ($q, $data){
                                $q->with($data);
                            })
                            ->when($code, function ($q, $data){
                                $q->where($this->coreFieldFilterModuleNameCol, $data);
                            })
                            ->when($coreKeysId, function ($q, $data){
                                $q->where($this->customUiCoreKeysIdCol, $data);
                            })
                            ->first();
        return $customizeHeader;
    }

    public function getCustomizeFieldAttrs($customizeHeaderId = null){
        $customizeDetails = CustomizeUiDetail::when($customizeHeaderId,function ($q, $data){
                                                    $q->where($this->customUiCoreKeysIdCol, $data);
                                                })
                                                ->latest()->get();
        return $customizeDetails;
    }

    public function getCustomizeFieldAttr($id){
        $customizeDetail = CustomizeUiDetail::find($id);
        return $customizeDetail;
    }

    public function deleteCustomizeFieldAttrs($customizeDetails){
        if ($customizeDetails->isNotEmpty()) {
            $customizeDetailIds = $customizeDetails->pluck($this->customUiIdCol);
            CustomizeUiDetail::destroy($customizeDetailIds);
        }
    }

    public function getValueByPluck($values, $pluckColumn){
        $pluckedValues = $values->pluck($pluckColumn)->unique()->values();
        return $pluckedValues;
    }

    public function getValuesForCustomizeField($customizeHeaderIds = null, $itemId = null){
        $values = LocationCityInfo::when($customizeHeaderIds,function ($q, $data){
                                    $q->whereIn($this->cityInfoCoreKeysIdCol, $data);
                                })
                                ->when($itemId, function ($q, $data){
                                    $q->where($this->cityInfoItemIdCol,$data);
                                })
                                ->get();
        return $values;
    }

    public function getCoreFieldFilteredLists($code){
        return CoreFieldFilterSetting::where($this->coreFieldFilterModuleNameCol, $code)->get();
    }

    public function deleteCoreFilteredOldData($code){
        $oldcoreFields = CoreFieldFilterSetting::with(["screenDisplayUiSetting"])->where($this->coreFieldFilterModuleNameCol, $code)->get();
        $oldcoreFieldIds = $oldcoreFields->pluck($this->coreFieldFilterIdCol);
        $screenDisplayUiSettingIds = ScreenDisplayUiSetting::whereIn($this->screenDisplayUiKeyCol, $oldcoreFieldIds)->get()->pluck($this->screenDisplayUiIdCol);

        // delete core field from screen_display_ui-settings
        ScreenDisplayUiSetting::destroy($screenDisplayUiSettingIds);

        // delete core field from core_fields
        foreach ($oldcoreFields as $oldcoreField){
            $oldcoreField->delete();
        }
    }

    public function createCoreFieldsForFilter($cityCoreLists, $labels, $coreFieldsForFilter, $categories, $subCategories, $locationCities, $locationTownships, $users, $currencies, $shops, $code, $coreFieldFilterForRelation){
        foreach ($cityCoreLists as $cityCoreListKey=>$cityCoreList) {
            foreach ($coreFieldsForFilter as $coreFieldKey => $coreFieldForFilter) {
                if (($coreFieldForFilter !== null && $coreFieldForFilter !== false) && $coreFieldKey == $cityCoreListKey) {

                    // save in core_field_filter_settings
                    $coreFieldFilterSetting = $this->saveCoreFieldFilterSettings($labels, $cityCoreListKey, $coreFieldKey ,$code, $cityCoreList, $categories, $subCategories, $locationCities, $locationTownships, $users, $currencies, $shops, $coreFieldFilterForRelation);

                    // save in screen_display_ui_settings table
                    $this->saveScreenDisplayUiSettings($code, $coreFieldFilterSetting);
                }
            }
        }
    }

    public function saveCoreFieldFilterSettings($labels, $cityCoreListKey, $coreFieldKey, $code, $cityCoreList, $categories, $subCategories, $locationCities, $locationTownships, $users, $currencies, $shops, $coreFieldFilterForRelation)
    {
        $coreFieldFilterSetting = new CoreFieldFilterSetting();
        $coreFieldFilterSetting->module_name = $code;
        if ($cityCoreListKey === $coreFieldKey) {
            $coreFieldFilterSetting->field_name = $cityCoreList;
        }

        foreach ($labels as $labelKey => $label) {
            if ($coreFieldKey === $labelKey) {
                $coreFieldFilterSetting->label_name = $label;
            }
        }
        $integerFields = ['price', 'ordering', 'lat', 'lng', 'original_price', 'overall_rating', 'status', 'is_available'];
        $dateFields = ['added_date', 'updated_date'];
        if (in_array($coreFieldFilterSetting->field_name, $integerFields)) {
            $coreFieldFilterSetting->data_type = 'Integer';
        } elseif (in_array($coreFieldFilterSetting->field_name, $dateFields)){
            $coreFieldFilterSetting->data_type = 'Date';
        } else {
            $coreFieldFilterSetting->data_type = 'String';
        }

        $coreFieldFilterSetting->save();

        return $coreFieldFilterSetting;
    }

    public function saveScreenDisplayUiSettings($code, $coreFieldFilterSetting){
        $screenDisplayUiSetting = new ScreenDisplayUiSetting();
        $screenDisplayUiSetting->module_name = $code;
        $screenDisplayUiSetting->key = $coreFieldFilterSetting->id;
        $screenDisplayUiSetting->is_show = $this->show;
        $screenDisplayUiSetting->save();

        return $screenDisplayUiSetting;
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

    public function getFilteredCoreFields($code, $limit = null, $offset = null,$isDel=null){
        $coreFields = CoreFieldFilterSetting::when($code, function ($q, $code){
                                                $q->where($this->coreFieldFilterModuleNameCol, $code);
                                            })
                                            ->when($limit, function ($query,$limit){
                                                $query->limit($limit);
                                            })
                                            ->when($isDel !== null,  function ($query) use ($isDel){
                                                $query->where('is_delete', $isDel);
                                            })
                                            ->when($offset, function ($query,$offset){
                                                $query->offset($offset);
                                            })
                                            ->get();
        return $coreFields;
    }

    public function deleteCustomizeFieldData($cityRelations){
        foreach ($cityRelations as $cityRelation) {
            delImageFromCustomFieldValue($cityRelation, $this->uploadPathForDel, $this->thumb1xPathForDel, $this->thumb2xPathForDel, $this->thumbnail3xPath);
            $cityRelation->delete();
        }
    }

    public function searching($query, $conds){
        if (isset($conds['keyword']) && $conds['keyword']) {
            $conds['searchterm'] = $conds['keyword'];
        }
        // search term
        if (isset($conds['searchterm']) && $conds['searchterm']) {
            $search = $conds['searchterm'];
            $query->where(function ($query) use ($search) {
                $query->where($this->cityTableName . '.' . $this->cityNameCol, 'like', '%' . $search . '%')
                    ->orWhere($this->cityTableName . '.' . $this->cityNameCol, 'like', '%' . $search . '%');
            });
        }
        if (isset($conds['added_user_id']) && $conds['added_user_id']) {
            $query->where($this->cityTableName .'.'.$this->cityAddedUserIdCol, $conds['added_user_id']);
        }

        if (isset($conds['lat']) && $conds['lat'] != '' && isset($conds['lng']) && $conds['lng'] != '') {
            $query->selectRaw("*,
            ( 6371 * acos( cos( radians(" . $conds['lat'] . ") ) *
            cos( radians(lat) ) *
            cos( radians(lng) - radians(" . $conds['lng'] . ") ) +
            sin( radians(" . $conds['lat'] . ") ) *
            sin( radians(lat) ) ) )
            AS distance");

            if (isset($conds['miles'])) {
                if($conds['miles'] == ''){
                    $conds['miles'] = 0;
                }
                $query->having('distance', '<', $conds['miles']);
            }
        }


        if (isset($conds['city_relation']) && !empty($conds['city_relation'])) {
            $customizeUis = CustomizeUi::where($this->customUiModuleName, 'loc')->latest()->get();
            foreach ($conds['city_relation'] as $key => $value) {

                if(!empty($value['value'])){
                    foreach ($customizeUis as $CustomizeUiDetail) {
                        if($value['core_keys_id'] == $CustomizeUiDetail->core_keys_id){
                            if ($CustomizeUiDetail->ui_type_id == $this->dropDownUi || $CustomizeUiDetail->ui_type_id == $this->radioUi || $CustomizeUiDetail->ui_type_id == $this->multiSelectUi) {
                                $detail = CustomizeUiDetail::find($value['value']);
                                if($detail){
                                    $query->having($value['core_keys_id'] . '@@name', $detail->name);
                                }
                            } else {
                                $detail = CustomizeUiDetail::find($value['value']);
                                if($detail){
                                    $query->having($value['core_keys_id'], $detail->name);
                                }
                            }
                        }
                    }
                }
            }
        }



        // order by
        if (isset($conds['order_by']) && isset($conds['order_type']) && $conds['order_by'] && $conds['order_type']){

            if($conds['order_by'] == 'id'){
                $query->orderBy($this->cityTableName .'.id', $conds['order_type']);
            }
            else{
                $query->orderBy($conds['order_by'], $conds['order_type']);
            }
        }

        return $query;
    }

    public function getSqlForCustomField(){
        $sql = "";
        $customizeUis = CustomizeUi::where($this->customUiModuleName, 'loc')->latest()->get();

        // $CustomizeUiDetails = CustomizeUiDetail::latest()->get();
        $customizeuideatil_array = [];
        // $customizeuideatil_sql = "";

        foreach ($customizeUis as $CustomizeUiDetail) {
            if ($CustomizeUiDetail->ui_type_id == $this->dropDownUi || $CustomizeUiDetail->ui_type_id == $this->radioUi || $CustomizeUiDetail->ui_type_id == $this->multiSelectUi) {
                $customizeuideatil_array[$CustomizeUiDetail->core_keys_id . '@@name'] = $CustomizeUiDetail->core_keys_id;
            }
        }

        foreach (array_unique($customizeuideatil_array) as $key => $customizeuideatil) {
            $sql .= "max(case when psx_location_city_infos.core_keys_id = '$customizeuideatil' then psx_customize_ui_details.name end) as '$key',";
        }
        foreach ($customizeUis as $key => $customizeUi) {
            if ($key + 1 == count($customizeUis)) {
                $sql .= "max(case when psx_location_city_infos.core_keys_id = '$customizeUi->core_keys_id' then psx_location_city_infos.value end) as '$customizeUi->core_keys_id'";
            } else {
                $sql .= "max(case when psx_location_city_infos.core_keys_id = '$customizeUi->core_keys_id' then psx_location_city_infos.value end) as '$customizeUi->core_keys_id' ,";

            }
        }
        return $sql;
    }

    //=========================
    public function index($routeName,$request){

        // $code = $this->code;
        $customizeUis = CustomizeUi::where($this->customUiModuleName,'loc')->where($this->customUiUiTypeIdCol, 'uit00001')->latest()->get();
        $cityRelations = [];
//        $uis = [];
//        foreach($customizeUis as $customizeUi){
//
//            $uis[$customizeUi->core_keys_id] = CustomizeUiDetail::where('core_keys_id',$customizeUi->core_keys_id)->get()->toArray();
//        }
        $uis = CustomizeUiDetail::whereIn($this->customUiDetailCoreKeysIdCol, $customizeUis->pluck($this->customUiDetailCoreKeysIdCol))->get()->groupBy($this->customUiDetailCoreKeysIdCol)->toArray();

        // check permission
        $checkPermission = $this->checkPermission($this->viewAnyAbility,LocationCity::class, "admin.index");

        $customizeUisByModule = CustomizeUi::where($this->customUiModuleName,'loc')->latest()->get();
        $customizeDetails = CustomizeUiDetail::whereIn($this->customUiDetailCoreKeysIdCol, $customizeUisByModule->pluck($this->customUiDetailCoreKeysIdCol))->get()->groupBy($this->customUiDetailCoreKeysIdCol)->toArray();

        // Search and filter
        $conds['searchterm'] = $request->input('search') ?? '';
        // if($request->item_type){
        //     $cityRelations[] =  ['core_keys_id' => 'ps-itm00001','value'=>$request->item_type];
        // }
        // if($request->item_condition){
        //     $cityRelations[] =  ['core_keys_id' => 'ps-itm00003','value'=>$request->item_condition];
        // }

        if(!empty($cityRelations))
        {
            $conds['city_relation'] =$cityRelations;
        }
        $conds['order_by']= null;
        $conds['order_type'] = null;
        $row = $request->input('row') ?? Constants::dataTableDefaultRow;

        if($request->sort_field)
        {
            $conds['order_by'] = $request->sort_field;
            $conds['order_type'] = $request->sort_order;
        }

        $cities = LocationCityWithKeyResource::collection($this->getLocationCities($cityRelations, null, null,null, $conds , false, $row ));

        // taking for column and columnFilterOption
        $columnAndColumnFilter = $this->takingForColumnAndFilterOption();
        $showProductCols = $columnAndColumnFilter['showCoreField'];
        $columnProps = $columnAndColumnFilter['arrForColumnProps'];
        $columnFilterOptionProps = $columnAndColumnFilter['arrForColumnFilterProps'];

        // changing item arr object with new format
        $changedProductObj = $cities;
        // $owners = $this->userService->getUsers(null, $this->publish, $this->unBan);

        if($conds['order_by'])
        {
            $dataArr = [
                "checkPermission" => $checkPermission,
                'showCoreAndCustomFieldArr' => $columnProps,
                'hideShowFieldForFilterArr' => $columnFilterOptionProps,
                'cities' => $cities,
                // 'owners' => $owners,
                'sort_field' =>$conds['order_by'],
                'sort_order'=>$request->sort_order,
                'search'=>$conds['searchterm'] ,
                'uis'=>$uis,
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
                'cities' => $cities,
                // 'owners' => $owners,
                'items' => $changedProductObj,
                'search'=>$conds['searchterm'] ,
                'uis'=>$uis,
                'customizeDetails' => $customizeDetails,
                'customizeHeaders' => $customizeUisByModule
            ];
        }

        return $dataArr;
    }


    public function create($routeName){
        $checkPermission = $this->checkPermission($this->createAbility,LocationCity::class, "admin.index");

        $code = $this->code;

        $coreFieldFilterSettings = $this->getCoreFieldFilteredLists($code);
        $customizeHeader = $this->getCustomizeFields($code,null,null,0);
        $customizeDetail = $this->getCustomizeFieldAttrs();
        $locTableColumns = getAllCoreFields($this->cityTableName);
        $backendSettings = $this->backendSettingService->getBackendSetting();

        $dataArr = [
            "checkPermission" => $checkPermission,
            "customizeHeaders" => $customizeHeader,
            "customizeDetails" => $customizeDetail,
            'coreFieldFilterSettings' => $coreFieldFilterSettings,
            "locTableColumns" => $locTableColumns,
            'backendSettings' => $backendSettings,
        ];

        return $dataArr;
    }

    public function edit($id, $routeName){
        $dataWithRelation = ['cityRelation'];
        $city = $this->getLocationCity($id, $dataWithRelation);
        $code = $this->code;

        $checkPermission = $this->checkPermission($this->editAbility,$city, "admin.index");

        $coreFieldFilterSettings = $this->getCoreFieldFilteredLists($code);
        $customizeHeader = $this->getCustomizeFields($code,null,null,0);
        $customizeDetail = $this->getCustomizeFieldAttrs();
        $backendSettings = $this->backendSettingService->getBackendSetting();

        $dataArr = [
            "checkPermission" => $checkPermission,
            'city'   => $city,
            "customizeHeaders" => $customizeHeader,
            "customizeDetails" => $customizeDetail,
            'coreFieldFilterSettings' => $coreFieldFilterSettings,
            'backendSettings' => $backendSettings,
        ];

        return $dataArr;
    }

    public function destroy($id, $routeName){
        //delete in location_cities table
        $city = $this->getLocationCity($id);

        $checkPermission = $this->checkPermission($this->deleteAbility,$city, "admin.index");

        //delete in location_city_infos table
        $cityRelations = $this->getValuesForCustomizeField('',$city->id);

        $name = $city->name;
        $city->delete();

        $this->deleteCustomizeFieldData($cityRelations);

        $dataArr = [
            "checkPermission" => $checkPermission,
            'title'   => $name,

        ];

        return $dataArr;
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
        $conds['order_by'] = $request->order_by;
        $conds['order_type'] = $request->order_type;
        $relations = ['cityRelation'];
        $cities = $this->getLocationCities($relations, $this->publish, $limit, $offset, $conds, true);

        return $cities;

    }
}
