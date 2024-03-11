<?php

namespace Modules\Core\Http\Services;

use App\Http\Services\PsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\CoreFieldFilterSetting;
use Modules\Core\Entities\CustomizeUi;
use Modules\Core\Entities\MobileLanguage;
use Modules\Core\Entities\MobileLanguageString;
use Modules\Core\Entities\ScreenDisplayUiSetting;
use Modules\Core\Transformers\Backend\Model\MobileLanguage\MobileLanguageWithKeyResource;

class MobileLanguageService extends PsService
{
    protected  $successStatus, $noContentStatusCode, $enable, $show, $hide, $delete, $unDelete, $langEnableCol, $langMobileLanguageIdCol, $langIdCol, $langStatusCol, $langTableName, $publish, $unPublish, $default, $unDefault, $warning, $success, $danger, $viewAnyAbility, $createAbility, $editAbility, $deleteAbility, $code, $screenDisplayUiKeyCol, $screenDisplayUiIdCol, $screenDisplayUiIsShowCol, $coreFieldFilterModuleNameCol, $customUiIsDelCol;
    public function __construct()
    {
        $this->enable = Constants::enable;
        $this->publish = Constants::publish;
        $this->unPublish = Constants::unPublish;
        $this->default = Constants::default;
        $this->unDefault = Constants::unDefault;
        $this->show = Constants::show;
        $this->hide = Constants::hide;
        $this->delete = Constants::delete;
        $this->unDelete = Constants::unDelete;
        $this->warning = Constants::warning;
        $this->success = Constants::success;
        $this->danger = Constants::danger;
        $this->langTableName = MobileLanguage::tableName;
        $this->langStatusCol = MobileLanguage::status;
        $this->langIdCol = MobileLanguage::id;
        $this->langEnableCol = MobileLanguage::enable;
        $this->langMobileLanguageIdCol = MobileLanguage::mobileLanguageId;
        $this->langMobileLanguageSymbolCol = MobileLanguage::symbol;
        $this->langMobileLanguageNameCol = MobileLanguage::name;
        $this->langMobileLanguageCodeCol = MobileLanguage::languageCode;
        $this->langMobileLanguageCountryCodeCol = MobileLanguage::countryCode;

        $this->successStatus = Constants::successStatus;
        $this->noContentStatusCode = Constants::noContentStatusCode;

        $this->viewAnyAbility = Constants::viewAnyAbility;
        $this->createAbility = Constants::createAbility;
        $this->editAbility = Constants::editAbility;
        $this->deleteAbility = Constants::deleteAbility;
        $this->code = Constants::mobileLanguage;

        $this->screenDisplayUiKeyCol = ScreenDisplayUiSetting::key;
        $this->screenDisplayUiIdCol = ScreenDisplayUiSetting::id;
        $this->screenDisplayUiIsShowCol = ScreenDisplayUiSetting::isShow;

        $this->coreFieldFilterModuleNameCol = CoreFieldFilterSetting::moduleName;

        $this->customUiIsDelCol = CustomizeUi::isDelete;
    }

    public function store($request)
    {
        DB::beginTransaction();

        try {
            $mobileLanguage = new MobileLanguage();
            $mobileLanguage->symbol = $request->symbol;
            $mobileLanguage->name = $request->name;
            $mobileLanguage->country_code = $request->countryCode;
            $mobileLanguage->code = Carbon::now()->getPreciseTimestamp(3);
            $mobileLanguage->language_code = $request->languageCode;
            $mobileLanguage->added_user_id = Auth::user()->id;
            $mobileLanguage->save();

            $activeLanguage = MobileLanguage::where($this->langStatusCol, 1)->first();

            $mobileLanguageStrings = MobileLanguageString::where($this->langMobileLanguageIdCol, $activeLanguage->id)->get();

            foreach ($mobileLanguageStrings as $mobileLanguageString){
                $mobileLanguageStringData[] = [
                    'key' => $mobileLanguageString->key,
                    'value' => $mobileLanguageString->value,
                    'mobile_language_id' => $mobileLanguage->id,
                    'added_user_id' => Auth::id(),
                ];
            }
            MobileLanguageString::insert($mobileLanguageStringData);

            DB::commit();
        } catch (\Throwable $e){
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }

        return $mobileLanguage;
    }

    public function update($id,$request)
    {
        // echo $id;
        // echo json_encode($request->all());
        $mobileLanguage = MobileLanguage::find($id);
        // echo json_encode($mobileLanguage);exit;
        DB::beginTransaction();

        try {
            $mobileLanguage->symbol = $request->symbol;
            $mobileLanguage->name = $request->name;
            $mobileLanguage->code = Carbon::now()->getPreciseTimestamp(3);
            $mobileLanguage->country_code = $request->countryCode;
            $mobileLanguage->language_code = $request->languageCode;
            $mobileLanguage->added_user_id = Auth::user()->id;
            $mobileLanguage->update();

            DB::commit();
        } catch (\Throwable $e){
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }

        return $mobileLanguage;
    }

    public function searching($query, $conds){
        // search by keyword

        if (isset($conds['keyword']) && $conds['keyword']) {
            $conds['searchterm'] = $conds['keyword'];
        }
        // search term
        if (isset($conds['searchterm']) && $conds['searchterm']) {
            $search = $conds['searchterm'];
            $query->where(function ($query) use ($search) {
                $query->where($this->langTableName . '.' . $this->langMobileLanguageSymbolCol, 'like', '%' . $search . '%')
                ->orWhere($this->langTableName . '.' . $this->langMobileLanguageNameCol, 'like', '%' . $search . '%')
                ->orWhere($this->langTableName . '.' . $this->langMobileLanguageCodeCol, 'like', '%' . $search . '%')
                    ->orWhere($this->langTableName . '.' . $this->langMobileLanguageCountryCodeCol, 'like', '%' . $search . '%');
            });
        }

        // order by
        if (isset($conds['order_by']) && isset($conds['order_type']) && $conds['order_by'] && $conds['order_type']){

            if($conds['order_by'] == 'id'){
                $query->orderBy($this->langTableName . '.id', $conds['order_type']);
            }
            else{
                $query->orderBy($conds['order_by'], $conds['order_type']);
            }
        }else{
            $query->orderBy($this->langTableName . '.status', 'desc')->orderBy($this->langTableName . '.enable', 'desc')->orderBy($this->langTableName . '.name', 'asc');
        }

        return $query;
    }

    public function getMobileLanguages($enable = null, $limit = null, $offset = null, $conds = null,$noPagination = null,  $pagPerPage = null){
        $mobileLanguages = MobileLanguage:: when($enable, function ($query, $enable) {
                                                $query->where($this->langEnableCol, $enable);
                                            })
                                            ->when($conds, function ($query, $conds) {
                                                $query = $this->searching($query, $conds);
                                            })
                                            ->when($limit, function ($query, $limit) {
                                                $query->limit($limit);
                                            })
                                            ->when($offset, function ($query, $offset) {
                                                $query->offset($offset);
                                            })
                                            ->latest();
                                            if ($pagPerPage){
                                                $mobileLanguages = $mobileLanguages->paginate($pagPerPage)->onEachSide(1)->withQueryString();

                                                } elseif ($noPagination){
                                                    $mobileLanguages = $mobileLanguages->get();
                                                }else{
                                                    $mobileLanguages = $mobileLanguages->get();
                                                }
                                                return $mobileLanguages;
    }

    public function getMobileLanguage($id){
        $mobileLanguage = MobileLanguage::when($id, function ($q, $id){
                                            $q->where($this->langIdCol, $id);
                                        })->first();
        return $mobileLanguage;
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

    public function delete($id){
        $language = $this->getMobileLanguage($id);
        $name = $language->name;
        if($language->status == $this->publish){
            $msg = 'The '.$name.' row cannot be deleted because of activated language.';
            return $msg;
        }
        $language->delete();
        $mobileLanguageStrings = MobileLanguageString::where($this->langMobileLanguageIdCol, $language->id)->get();
        foreach ($mobileLanguageStrings as $mobileLanguageString){
            $mobileLanguageString->delete();
        }

        // $msg = 'The '.$name.' row has been deleted successfully.';
        $msg = __('core__be_delete_success',['attribute'=>$name]);
            return $msg;
    }

    public function makeDefault($id){
        $mobileLanguage = $this->getMobileLanguage($id);
        if($mobileLanguage->status == $this->publish){
            // $msg = ' Sorry, the status of '.$mobileLanguage->name.' row cannot be changed because it is active language.';
            $msg = __('core__be_default_active_lang',['attribute'=>$mobileLanguage->name]);
            return $msg;
        }else{
            DB::table($this->langTableName)->update([$this->langStatusCol => $this->unPublish]);

            $mobileLanguage->status = $this->publish;
            $mobileLanguage->updated_user_id = Auth::user()->id;
        }
        $mobileLanguage->update();

    }

    public function makePublishOrUnPublish($id){
        $mobileLanguage = $this->getMobileLanguage($id);
        if($mobileLanguage->status !== $this->publish){
            if($mobileLanguage->enable == $this->publish){
                $mobileLanguage->enable = $this->unPublish;
            }else{
                $mobileLanguage->enable = $this->publish;
            }
        }else{
            // $msg = ' Sorry, the '.$mobileLanguage->name.' row cannot be able to unpublish in default language.';
            $msg = __('core__be_undefault_currency',['attribute'=>$mobileLanguage->name]);
            return $msg;
        }
        $mobileLanguage->updated_user_id = Auth::user()->id;
        $mobileLanguage->update();
        $msg = __('core__be_enable_mb_lang_label',['attribute'=>$mobileLanguage->name]);
        return $msg;
        // return 'The enable of '.$mobileLanguage->name.' row has been updated successfully.';
    }

        // ----------
        public function index($routeName,$request){
            // check permission
            $checkPermission = $this->checkPermission($this->viewAnyAbility, MobileLanguage::class, "admin.index");

            // search filter
            $conds['searchterm'] = $request->input('search') ?? '';

            $conds['order_by']= null;
            $conds['order_type'] = null;
            $row = $request->input('row') ?? Constants::dataTableDefaultRow;

            if($request->sort_field)
            {
                $conds['order_by'] = $request->sort_field;
                $conds['order_type'] = $request->sort_order;
            }

            $relations = ['owner', 'editor'];
            $languages = MobileLanguageWithKeyResource::collection($this->getMobileLanguages(null,null,null,$conds,false,$row));

            // echo json_encode($languages);exit;

            // taking for column and columnFilterOption
            $columnAndColumnFilter = $this->takingForColumnAndFilterOption();
            $showProductCols = $columnAndColumnFilter['showCoreField'];
            $columnProps = $columnAndColumnFilter['arrForColumnProps'];
            $columnFilterOptionProps = $columnAndColumnFilter['arrForColumnFilterProps'];

            // changing item arr object with new format
            $changedCurrencyObj = $languages;
            // $owners = $this->userService->getUsers(null, $this->publish, $this->unBan);

            if($conds['order_by'])
            {
                $dataArr = [
                    "checkPermission" => $checkPermission,
                    'showCoreAndCustomFieldArr' => $columnProps,
                    'hideShowFieldForFilterArr' => $columnFilterOptionProps,

                    // 'owners' => $owners,
                    'mobile_languages' => $changedCurrencyObj,
                    'sort_field' =>$conds['order_by'],
                    'sort_order'=>$request->sort_order,
                    'search'=>$conds['searchterm'] ,
                    // 'uis'=>$uis,
                ];
            }
            else
            {
                $dataArr = [
                    "checkPermission" => $checkPermission,
                    'showCoreAndCustomFieldArr' => $columnProps,
                    'hideShowFieldForFilterArr' => $columnFilterOptionProps,

                    // 'owners' => $owners,
                    'mobile_languages' => $changedCurrencyObj,
                    'search'=>$conds['searchterm'] ,
                    // 'uis'=>$uis,
                ];
            }


            return $dataArr;

        }

    public function create(){
        // check permission
        $checkPermission = $this->checkPermission($this->createAbility,MobileLanguage::class, "admin.index");
        $dataArr = [
            "checkPermission" => $checkPermission,
        ];
        return $dataArr;
    }

    public function edit($id){
        $mobileLanguage = $this->getMobileLanguage($id);

        // check permission
        $checkPermission = $this->checkPermission($this->editAbility, $mobileLanguage, "admin.index");

        $dataArr = [
            "mobileLanguage" => $mobileLanguage,
            "checkPermission" => $checkPermission,
        ];
        return $dataArr;
    }

    public function destroy($id){
        $mobileLanguage = $this->getMobileLanguage($id);

        // check permission
        $checkPermission = $this->checkPermission($this->deleteAbility, $mobileLanguage, "admin.index");

        $name = $mobileLanguage->name;
        $msg = $this->delete($id);

        if ($msg){
            $dataArr = [
                "msg" => $msg,
                "flag" => $this->warning,
                "checkPermission" => $checkPermission,
            ];
            return $dataArr;
        }

        $dataArr = [
            "msg" => $name,
            "flag" => $this->success,
            "checkPermission" => $checkPermission,
        ];
        return $dataArr;
    }

    public function statusChange($id){
        $name = $this->getMobileLanguage($id)->name;

        $msg = $this->makeDefault($id);

        if ($msg){
            $dataArr = [
                "msg" => $msg,
                "flag" => $this->warning
            ];
            return $dataArr;
        }

        // $msg = 'The status of '.$name.' row has been updated successfully.';
        $msg = __('core__be_status_attribute_updated',['attribute'=>$name]);
        $dataArr = [
            "msg" => $msg,
            "flag" => $this->success
        ];
        return $dataArr;
    }

    public function enableDisable($id){
        $msg = $this->makePublishOrUnPublish($id);

        $status = [
            'msg' => $msg,
            'flag' => $this->success,
        ];
        return $status;
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

        // $controlFieldObj = $this->takingForColumnProps("Langauge String", "language_string", "Language String", false, 0);
        // array_push($controlFieldArr, $controlFieldObj);

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
                $uiHaveAttribute = [$this->dropDownUi, $this->radioUi];
                if (in_array($showFields->customizeField->ui_type_id, $uiHaveAttribute)){
                    $field = $showFields->customizeField->core_keys_id."@@name";
                } else {
                    $field = $showFields->customizeField->core_keys_id;
                }
                $type = $showFields->customizeField->data_type;
                $isShowSorting = $showFields->customizeField->is_show_sorting;
                $ordering = $showFields->customizeField->ordering;

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
        }, 'coreField'=> function($q){
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

    public function indexFromApi($request){
        $offset = $request->offset;
        $limit = $request->limit;

        $mobileLanguages = $this->getMobileLanguages($this->enable, $limit, $offset);

        return $mobileLanguages;
    }

    public function searchFromApi($request){
        $offset = $request->offset;
        $limit = $request->limit;

        $conds['keyword'] = $request->keyword;
        $conds['order_by'] = $request->order_by;
        $conds['order_type'] = $request->order_type;

        $mobileLanguages = $this->getMobileLanguages(true, $limit, $offset, $conds);

        return $mobileLanguages;

    }

}
