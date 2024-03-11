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
use Modules\Core\Transformers\Backend\Model\MobileLanguageString\MobileLanguageStringWithKeyResource;
use Modules\Core\Imports\MobileLanguageStringImport;

class MobileLanguageStringService extends PsService
{
    protected $mobileLangStringMobileLanguageIdCol, $mobileLangStringIdCol, $mobileLangStringKeyCol, $mobileLanguageService, $publish, $unPublish, $default, $unDefault, $warningFlag, $successFlag, $dangerFlag, $viewAnyAbility, $createAbility, $editAbility, $deleteAbility, $code, $screenDisplayUiKeyCol, $screenDisplayUiIdCol, $screenDisplayUiIsShowCol, $coreFieldFilterModuleNameCol, $customUiIsDelCol, $hide, $show, $delete, $unDelete;
    public function __construct(MobileLanguageService $mobileLanguageService)
    {
        $this->mobileLanguageService = $mobileLanguageService;
        $this->publish = Constants::publish;
        $this->unPublish = Constants::unPublish;
        $this->default = Constants::default;
        $this->unDefault = Constants::unDefault;
        $this->show = Constants::show;
        $this->hide = Constants::hide;
        $this->delete = Constants::delete;
        $this->unDelete = Constants::unDelete;
        $this->warningFlag = Constants::warning;
        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;
        $this->mobileLangStringIdCol = MobileLanguageString::id;
        $this->mobileLangStringKeyCol = MobileLanguageString::key;
        $this->mobileLangStringTableName = MobileLanguageString::tableName;
        $this->mobileLangStringValueCol = MobileLanguageString::value;
        $this->mobileLangStringMobileLanguageIdCol = MobileLanguageString::mobileLanguageId;

        $this->mobileLangIdCol = MobileLanguage::id;

        $this->viewAnyAbility = Constants::viewAnyAbility;
        $this->createAbility = Constants::createAbility;
        $this->editAbility = Constants::editAbility;
        $this->deleteAbility = Constants::deleteAbility;
        $this->code = Constants::mobileLanguageString;

        $this->screenDisplayUiKeyCol = ScreenDisplayUiSetting::key;
        $this->screenDisplayUiIdCol = ScreenDisplayUiSetting::id;
        $this->screenDisplayUiIsShowCol = ScreenDisplayUiSetting::isShow;

        $this->coreFieldFilterModuleNameCol = CoreFieldFilterSetting::moduleName;

        $this->customUiIsDelCol = CustomizeUi::isDelete;
        $this->csvFile = Constants::csvFile;

        $this->noContentStatusCode = Constants::noContentStatusCode;

    }

    public function store($request)
    {
        DB::beginTransaction();

        try {
            /// For one language start

            // add new mobile language string start
            $mobile_language_string = new MobileLanguageString();
            $mobile_language_string->key = $request->key;
            $mobile_language_string->value = $request->value;
            $mobile_language_string->mobile_language_id = $request->language_id;
            $mobile_language_string->added_user_id = Auth::user()->id;
            $mobile_language_string->save();
            // add new mobile language string end

            // update code for mobile language start
            $mobile_language = MobileLanguage::findOrFail($request->language_id);
            $mobile_language->code = Carbon::now()->getPreciseTimestamp(3);
            $mobile_language->update();
            // update code for mobile language end

            /// For one language end

            /// For another languages start

            // will added for another languages start
            $mobileLanguages = $this->mobileLanguageService->getMobileLanguages();
            foreach ($mobileLanguages as $mobileLanguage){
                if ($mobileLanguage->id != $request->language_id){
                    $mobile_language_string = new MobileLanguageString();
                    $mobile_language_string->key = $request->key;
                    $mobile_language_string->value = $request->value;
                    $mobile_language_string->mobile_language_id = $mobileLanguage->id;
                    $mobile_language_string->added_user_id = Auth::user()->id;
                    $mobile_language_string->save();

                    // update code for mobile language start
                    $mobile_language = MobileLanguage::findOrFail($mobile_language->id);
                    $mobile_language->code = Carbon::now()->getPreciseTimestamp(3);
                    $mobile_language->update();
                    // update code for mobile language end
                }
            }
            // will added for another language end

            /// For another languages end

            DB::commit();
        } catch (\Throwable $e){
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
        return $mobile_language_string;
    }

    public function update($mobileLanguageStringId,$request)
    {
        DB::beginTransaction();

        try {
            $mobile_language_string = $this->getMobileLanguageString($mobileLanguageStringId);
            $mobile_language_string->key = $request->key;
            $mobile_language_string->value = $request->value;
            $mobile_language_string->mobile_language_id = $request->language_id;
            $mobile_language_string->added_user_id = Auth::user()->id;
            $mobile_language_string->update();

            // update code for mobile language start
            $this->updateCode($request->language_id);
            // update code for mobile language end

            DB::commit();
        } catch (\Throwable $e){
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }

        return $mobile_language_string;
    }


    public function getMobileLanguageString($id = null, $key = null, $languageId = null){
        $mobileLanguageString = MobileLanguageString::when($id,function ($q, $id){
            $q->where($this->mobileLangStringIdCol, $id);
        })
            ->when($key,function ($q, $key){
                $q->where($this->mobileLangStringKeyCol, $key);
            })
            ->when($languageId,function ($q, $languageId){
                $q->where($this->mobileLangStringMobileLanguageIdCol, $languageId);
            })
            ->first();
        return $mobileLanguageString;
    }

    public function getMobileLanguageStrings($mobileLanguageId, $relation = null, $limit = null, $offset = null,$conds = null,$noPagination = null,  $pagPerPage = null){
        $mobileLanguage = $this->mobileLanguageService->getMobileLanguage($mobileLanguageId);

        $languageStrings = MobileLanguageString::where($this->mobileLangStringMobileLanguageIdCol, $mobileLanguage->id)
                                                ->when($relation, function ($q, $relation){
                                                    $q->with($relation);
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
                                                    $languageStrings = $languageStrings->paginate($pagPerPage)->onEachSide(1)->withQueryString();

                                                    } elseif ($noPagination){
                                                        $languageStrings = $languageStrings->get();
                                                    }else{
                                                        $languageStrings = $languageStrings->get();
                                                    }
                                                    return $languageStrings;

    }

    public function updateCode($languageId){
        // update code for mobile language start
        $mobile_language = $this->mobileLanguageService->getMobileLanguage($languageId);
//        $mobile_language = MobileLanguage::findOrFail($request->language_id);
        $mobile_language->code = Carbon::now()->getPreciseTimestamp(3);
        $mobile_language->update();
        // update code for mobile language end
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
                $query->where($this->mobileLangStringTableName . '.' . $this->mobileLangStringKeyCol, 'like', '%' . $search . '%')
                    ->orWhere($this->mobileLangStringTableName . '.' . $this->mobileLangStringValueCol, 'like', '%' . $search . '%');
            });
        }

        // order by
        if (isset($conds['order_by']) && isset($conds['order_type']) && $conds['order_by'] && $conds['order_type']){

            if($conds['order_by'] == 'id'){
                $query->orderBy($this->mobileLangStringTableName . '.id', $conds['order_type']);
            }
            else{
                $query->orderBy($conds['order_by'], $conds['order_type']);
            }
        }else{
            $query->orderBy($this->mobileLangStringTableName . '.status', 'desc')->orderBy($this->mobileLangStringTableName . '.value', 'asc');
        }

        return $query;
    }

    public function index($routeName,$request){
        // check permission
        $checkPermission = $this->checkPermission($this->viewAnyAbility, MobileLanguageString::class, "admin.index");

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

        $mobileLanguage = $this->mobileLanguageService->getMobileLanguage($request->mobile_language);
        $realations = ['mobileLanguage', 'owner', 'editor'];
        $mobileLanguageStrings = MobileLanguageStringWithKeyResource::collection($this->getMobileLanguageStrings($request->mobile_language , $realations,null,null,$conds,false,$row));

        // echo json_encode($languages);exit;

        // taking for column and columnFilterOption
        $columnAndColumnFilter = $this->takingForColumnAndFilterOption();
        $showProductCols = $columnAndColumnFilter['showCoreField'];
        $columnProps = $columnAndColumnFilter['arrForColumnProps'];
        $columnFilterOptionProps = $columnAndColumnFilter['arrForColumnFilterProps'];

        $changedCurrencyObj = $mobileLanguageStrings;

        if($conds['order_by'])
        {
            $dataArr = [
                "checkPermission" => $checkPermission,
                'showCoreAndCustomFieldArr' => $columnProps,
                'hideShowFieldForFilterArr' => $columnFilterOptionProps,
                'mobile_language' => $mobileLanguage,
                'mobile_language_strings' => $changedCurrencyObj,
                'sort_field' =>$conds['order_by'],
                'sort_order'=>$request->sort_order,
                'search'=>$conds['searchterm'] ,
            ];
        }
        else
        {
            $dataArr = [
                "checkPermission" => $checkPermission,
                'showCoreAndCustomFieldArr' => $columnProps,
                'hideShowFieldForFilterArr' => $columnFilterOptionProps,
                'mobile_language' => $mobileLanguage,
                'mobile_language_strings' => $changedCurrencyObj,
                'search'=>$conds['searchterm'] ,
            ];
        }
        return $dataArr;

    }

    public function create($mobileLanguageId){
        $mobileLanguage = $this->mobileLanguageService->getMobileLanguage($mobileLanguageId);
        $checkPermission = $this->checkPermission($this->createAbility,MobileLanguageString::class, "admin.index");
        $dataArr = [
            'mobile_language' => $mobileLanguage,
            "checkPermission" => $checkPermission,
        ];
        return $dataArr;
    }

    public function edit($mobileLanguageId, $mobileLanguageStringId){
        $mobileLanguage = $this->mobileLanguageService->getMobileLanguage($mobileLanguageId);
        $mobileLanguageString = $this->getMobileLanguageString($mobileLanguageStringId);

        $checkPermission = $this->checkPermission($this->editAbility,$mobileLanguageString, "admin.index");

        $dataArr = [
            'mobile_language' => $mobileLanguage,
            'mobile_language_string' => $mobileLanguageString,
            "checkPermission" => $checkPermission,
        ];
        return $dataArr;
    }

    public function destroy($mobileLanguageId, $mobileLanguageStringId){
        $mobileLanguage = $this->mobileLanguageService->getMobileLanguage($mobileLanguageId);
        $mobileLanguageString = $this->getMobileLanguageString($mobileLanguageStringId);

        // check permission
        $checkPermission = $this->checkPermission($this->deleteAbility, $mobileLanguageString, "admin.index");

        $name = $mobileLanguageString->key;
        $mobileLanguageString->delete();


        // update code for mobile language start
        $this->updateCode($mobileLanguage->id);
        // update code for mobile language end


        // will add for another languages start
        $mobileLanguages = $this->mobileLanguageService->getMobileLanguages();
        foreach ($mobileLanguages as $mobileLanguage){
            if ($mobileLanguage->id != $mobileLanguageString->language_id){

                $mobileLanguageStringValue = MobileLanguageString::where('key',$mobileLanguageString->key)->first();
                if (!empty($mobileLanguageStringValue)){

                    // will delete mobile lang string for another lang
                    $mobileLanguageStringValue->delete();

                    // update code for mobile language start
                    $this->updateCode($mobileLanguage->id);
                    // update code for mobile language end

                }
            }
        }
        // will add for another language end


        $status = [
            // 'msg' => 'The '.$name.' row has been deleted successfully.',
            'msg'=>__('core__be_delete_success',['attribute'=>$name]),
            'flag' => $this->dangerFlag,
            "checkPermission" => $checkPermission,
        ];
        // dd($status);
        return $status;
    }

    public function importCSV($languageId, $request){

        $language = MobileLanguage::where($this->mobileLangIdCol, $languageId)->first();

         // update code for mobile language start
         $this->updateCode($language->id);
         // update code for mobile language end

         $file = file_get_contents($request->file($this->csvFile));
        //  dd($file);
        $languageStrings = json_decode($file);

        $fileKeys = [];
        $existKeys = [];

        // dd($languageStrings);

        foreach ($languageStrings as $key=>$value){
            array_push($fileKeys,$key);
        }

        //filter exist keys
        $langStrs = MobileLanguageString::where($this->mobileLangStringMobileLanguageIdCol, $language->id)->whereIn($this->mobileLangStringKeyCol, $fileKeys)->get();

        //for update
        foreach ($langStrs as $langStr){
            array_push($existKeys,$langStr->key);

            foreach ($languageStrings as $k=>$v){
                if($k == $langStr->key){
                    $langStr->value = $v;

                    $langStr->update();
                }
            }
        }

        //for new keys
        foreach ($languageStrings as $newkey=>$newvalue){
            //if not in exist keys
            if (!in_array($newkey, $existKeys)) {
                $language_string = new MobileLanguageString();
                $language_string->key = $newkey;
                $language_string->value = $newvalue;
                $language_string->mobile_language_id = $language->id;
                $language_string->added_user_id = Auth::user()->id;
                $language_string->save();
            }
        }
    }

    public function exportJson($languageId){

        $language = MobileLanguage::where($this->mobileLangIdCol, $languageId)->first();
        $langStrs = MobileLanguageString::where($this->mobileLangStringMobileLanguageIdCol, $language->id)->get();

        $language_strings = [];

        // for col (key, value)
        $language_strings["key"] = "value";

        foreach ($langStrs as $language_string){
            $language_strings[$language_string->key] = $language_string->value;
        }

        $jsonEncoded = json_encode($language_strings,JSON_UNESCAPED_UNICODE);

        $file = $language->symbol . '_tran.json';

        file_put_contents($file,$jsonEncoded);

        return response()->download($file);

    }

    public function exportCSV($languageId)
    {
        $language = MobileLanguage::where($this->mobileLangIdCol, $languageId)->first();
        $langStrs = MobileLanguageString::where($this->mobileLangStringMobileLanguageIdCol, $language->id)->get();

        $language_strings = "";

        // for col (key, value)
        $language_strings .= 'key,value'."\n";

        foreach ($langStrs as $language_string){
            $language_strings .= $language_string->key.','.$language_string->value."\n";
        }
        $file = $language->symbol . '_tran.csv';

        file_put_contents($file,$language_strings);

        return response()->download($file);
    }

    public function statusChange($id){
        $mobile_language_string = $this->getMobileLanguageString($id);

        $name = $mobile_language_string->key;

        if($mobile_language_string->status == $this->publish){
            $mobile_language_string->status = $this->unPublish;
        }else{
            $mobile_language_string->status = $this->publish;
        }

        $mobile_language_string->update();

        // $msg = 'The status of '.$name.' row has been updated successfully.';
        $msg = __('core__be_status_attribute_updated',['attribute'=>$name]);
        $dataArr = [
            "msg" => $msg,
            "flag" => $this->successFlag
        ];
        return $dataArr;
    }

    public function makeColumnHideShown($request){
        // echo json_encode($request->value);exit;
        $hideShowFields = $request->value;
//        foreach ($hideShowFields as $hideShowField){
//            $ScreenDisplayUiSetting = ScreenDisplayUiSetting::find($hideShowField[$this->screenDisplayUiIdCol]);
//            $ScreenDisplayUiSetting->is_show = $hideShowField['hidden'] ? $this->hide : $this->show;
//            $ScreenDisplayUiSetting->update();
//        }
        foreach ($hideShowFields as $hideShowField){
            $hideShowFieldData[] = [
                'id' => $hideShowField['id'],
                'is_show' => $hideShowField['hidden'] ? $this->hide : $this->show,
            ];
        }
        ScreenDisplayUiSetting::upsert(
            $hideShowFieldData,
            ['id'], ['is_show']
        );
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
    public function indexFromApi($request){

        $mobileLanguageId = $request->mobile_language;

        $mobileLanguage = $this->mobileLanguageService->getMobileLanguage($mobileLanguageId);

        if(!$mobileLanguage) {
            // no data db
            return  ['error' => __('mobile_language_string__api_no_data') ,'status' =>  $this->noContentStatusCode ];
        }

        $mobileLanguageStrings = $this->getMobileLanguageStrings($mobileLanguageId, null)
                                        ->map(function ($lang, $key){
                                            return [
                                                $lang['key'] => $lang['value']
                                            ];
                                        })->collapse();

        // $mobileLanguageStrings = new MobileLanguageStringApiResource($mobileLanguageStrings);

        if($mobileLanguageStrings->isEmpty()) {
            // no data db
            return  ['error' => __('mobile_language_string__api_no_data') ,'status' =>  $this->noContentStatusCode ];
        } else {
            return $mobileLanguageStrings;
        }

    }

}
