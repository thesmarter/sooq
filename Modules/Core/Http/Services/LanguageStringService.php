<?php
namespace Modules\Core\Http\Services;

use App\Http\Services\PsService;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\Language;
use Modules\Core\Entities\LanguageString;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Entities\CoreFieldFilterSetting;
use Modules\Core\Entities\CustomizeUi;
use Modules\Core\Entities\ScreenDisplayUiSetting;
use Modules\Core\Imports\LanguageStringImport;
use Modules\Core\Transformers\Backend\Model\LanguageString\LanguageStringWithKeyResource;


class LanguageStringService extends PsService
{

    protected $langStringLanguageIdCol, $langStringIdCol, $langStringKeyCol, $languageService, $publish, $unPublish, $default, $unDefault, $warning, $success, $danger, $show, $hide, $delete, $unDelete, $viewAnyAbility, $createAbility, $editAbility, $deleteAbility, $code, $screenDisplayUiKeyCol, $screenDisplayUiIdCol, $screenDisplayUiIsShowCol, $coreFieldFilterModuleNameCol, $customUiIsDelCol;

    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;
        $this->publish = Constants::publish;
        $this->unPublish = Constants::unPublish;
        $this->default = Constants::default;
        $this->unDefault = Constants::unDefault;
        $this->warning = Constants::warning;
        $this->success = Constants::success;
        $this->danger = Constants::danger;
        $this->langStringIdCol = LanguageString::id;
        $this->langStringKeyCol = LanguageString::key;
        $this->langStringLanguageIdCol = LanguageString::languageId;
        $this->langStringValueCol = LanguageString::value;


        $this->langTableName = Language::tableName;

        $this->csvFile = Constants::csvFile;

        $this->show = Constants::show;
        $this->hide = Constants::hide;
        $this->delete = Constants::delete;
        $this->unDelete = Constants::unDelete;

        $this->viewAnyAbility = Constants::viewAnyAbility;
        $this->createAbility = Constants::createAbility;
        $this->editAbility = Constants::editAbility;
        $this->deleteAbility = Constants::deleteAbility;
        $this->code = Constants::languageString;

        $this->screenDisplayUiKeyCol = ScreenDisplayUiSetting::key;
        $this->screenDisplayUiIdCol = ScreenDisplayUiSetting::id;
        $this->screenDisplayUiIsShowCol = ScreenDisplayUiSetting::isShow;

        $this->coreFieldFilterModuleNameCol = CoreFieldFilterSetting::moduleName;

        $this->customUiIsDelCol = CustomizeUi::isDelete;

        $this->userTableName = User::tableName;
        $this->userIdCol = User::id;
        $this->userNameCol = User::name;

    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            $language_string = new LanguageString();
            $language_string->key = $request->key;
            $language_string->value = $request->value;
            $language_string->language_id = $request->language_id;
            $language_string->added_user_id = Auth::user()->id;
            $language_string->save();

            // will added for another languages start
            $backend_languages = Language::where("id","<>",$request->language_id)->get();
            foreach ($backend_languages as $backend_language){
                if ($backend_language->id !== $request->language_id){
                    $backend_language_string = new LanguageString();
                    $backend_language_string->key = $request->key;
                    $backend_language_string->value = $request->value;
                    $backend_language_string->language_id = $backend_language->id;
                    $backend_language_string->added_user_id = Auth::user()->id;
                    $backend_language_string->save();
                }

                // update json file
                $fileName = $backend_language->symbol . '.json';
                $lang_str = LanguageString::where($this->langStringLanguageIdCol, $backend_language->id)->get();
                generateLangStrJson($fileName, $lang_str);
            }
            DB::commit();
            // will added for another language end
        } catch (\Throwable $e){
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }

        return $language_string;
    }

    public function update($languageStringId, $request)
    {
       DB::beginTransaction();
        try {
            $language_string = LanguageString::findOrFail($languageStringId);
            $language_string->key = $request->key;
            $language_string->value = $request->value;
            $language_string->language_id = $request->language_id;
            $language_string->added_user_id = Auth::user()->id;
            $language_string->update();

            // update json file
            $fileName = Language::where('id', $request->language_id)->first()->symbol . '.json';
            $lang_str = LanguageString::where($this->langStringLanguageIdCol, $request->language_id)->get();
            generateLangStrJson($fileName, $lang_str);
            DB::commit();
        }catch (\Throwable $e){
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
        return $language_string;
    }

    public function getLanguageStrings($id, $relations = null,$pagPerPage = null,$conds = null){
        $language = $this->languageService->getLanguage($id);
        $languageStrings = LanguageString::where($this->langStringLanguageIdCol, $language->id)
                                            ->when(isset($conds['order_by']) && $conds['order_by'] , function ($q) use($conds){
                                                if($conds['order_by'] == 'added_user_id' || $conds['order_by'] == 'updated_user_id')
                                                {
                                                    $q->leftJoin($this->userTableName, $this->userTableName.'.'.$this->userIdCol,'=', $this->langTableName.'.'.$conds['order_by']);
                                                    $q->select($this->userTableName.'.'.$this->userNameCol.' as owner', $this->langTableName.'.*');
                                                }

                                            })->when($conds, function ($query, $conds) {
                                                $query = $this->searching($query, $conds);
                                            })->when($relations, function ($q, $relations){
                                                $q->with($relations);
                                            })
                                            ->latest();

        if ($pagPerPage){
            $languageStrings = $languageStrings->paginate($pagPerPage)->onEachSide(1)->withQueryString();
        } else{
            $languageStrings = $languageStrings->get();
        }
        return $languageStrings;
    }
    public function searching($query, $conds){
        // search term
        if (isset($conds['searchterm']) && $conds['searchterm']) {
            $search = $conds['searchterm'];
            $query->where(function ($query) use ($search) {
                $query->where($this->langStringKeyCol, 'like','%'.$search.'%')
                ->orWhere($this->langStringValueCol, 'like', '%'.$search.'%');
            });
        }
        // order by
        if (isset($conds['order_by']) && isset($conds['order_type']) && $conds['order_by'] && $conds['order_type']){

            if($conds['order_by'] == 'add_user_id' || $conds['order_by'] == 'updated_user_id'){
                $query->orderBy('owner', $conds['order_type']);
            }
            else{

                $query->orderBy($conds['order_by'], $conds['order_type']);
            }

        }else{
            $query->orderBy($this->langStringValueCol, 'asc');
        }
        return $query;
    }

    public function getLanguageString($id = null, $key = null, $languageId = null){
        $languageStrings = LanguageString::when($id,function ($q, $id){
                                                $q->where($this->langStringIdCol, $id);
                                            })
                                            ->when($key,function ($q, $key){
                                                $q->where($this->langStringKeyCol, $key);
                                            })
                                            ->when($languageId,function ($q, $languageId){
                                                $q->where($this->langStringLanguageIdCol, $languageId);
                                            })
                                            ->first();
        return $languageStrings;
    }

    // -----------------------------
	public function index($id,$request){

        $conds['searchterm'] = $request->input('search') ?? '';
        $conds['order_by']= null;
        $conds['order_type'] = null;
        $row = $request->input('row') ?? Constants::dataTableDefaultRow;
        if($request->sort_field)
        {
            $conds['order_by'] = $request->sort_field;
            $conds['order_type'] = $request->sort_order;
        }
        // check permission
        $checkPermission = $this->checkPermission($this->viewAnyAbility, LanguageString::class, "admin.index");



        $language = $this->languageService->getLanguage($id);
        $relations = ['owner', 'editor', 'language'];
        $language_strings = LanguageStringWithKeyResource::collection($this->getLanguageStrings($id, $relations,$row,$conds));

        // taking for column and columnFilterOption
        $columnAndColumnFilter = $this->takingForColumnAndFilterOption();
        $showLanguageStringCols = $columnAndColumnFilter['showCoreField'];
        $columnProps = $columnAndColumnFilter['arrForColumnProps'];
        $columnFilterOptionProps = $columnAndColumnFilter['arrForColumnFilterProps'];
        if($conds['order_by'])
        {
            $dataArr = [
                'checkPermission' => $checkPermission,
                'language' => $language,
                'language_strings' => $language_strings,
                'showLanguageStringCols' => $showLanguageStringCols,
                'showCoreAndCustomFieldArr' => $columnProps,
                'hideShowFieldForFilterArr' => $columnFilterOptionProps,
                'sort_field' =>$conds['order_by'],
                'sort_order'=>$request->sort_order,
                'search'=>$conds['searchterm'] ,
            ];
        }
        else{
            $dataArr = [
                'checkPermission' => $checkPermission,
                'language' => $language,
                'language_strings' => $language_strings,
                'showLanguageStringCols' => $showLanguageStringCols,
                'showCoreAndCustomFieldArr' => $columnProps,
                'hideShowFieldForFilterArr' => $columnFilterOptionProps,
                'sort_field' =>$conds['order_by'],
                'sort_order'=>$request->sort_order,
                'search'=>$conds['searchterm'] ,
            ];
        }
        return $dataArr;

    }

    public function create($id){
        $language = $this->languageService->getLanguage($id);
        $code = $this->code;
        $coreFieldFilterSettings = $this->getCoreFieldFilteredLists($code);
        // check permission start
        $checkPermission = $this->checkPermission($this->createAbility,LanguageString::class, "admin.index");
        // check permission end
        $dataArr = [
            'language' => $language,
            'checkPermission' => $checkPermission,
            'coreFieldFilterSettings'=>$coreFieldFilterSettings,

        ];
        return $dataArr;
    }

    public function edit($languageId, $languageStringId){
        $language = $this->languageService->getLanguage($languageId);
         // check permission start
         $checkPermission = $this->checkPermission($this->editAbility, $language, "admin.index");
         // check permission end

        $languageString = $this->getLanguageString($languageStringId);
        $dataArr = [
            'language' => $language,
            'language_string' => $languageString,
            'checkPermission' => $checkPermission,
        ];
        return $dataArr;
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
    public function destroy($languageId , $languageStringId){
        $languageString = $this->getLanguageString($languageStringId);
        $name = $languageString->key;
        $languageString->delete();
         // check permission start
        //  $checkPermission = $this->checkPermission($this->deleteAbility, $languageString, "admin.index");
        //  if (!empty($checkPermission)){
        //      $dataArr = [
        //          "checkPermission" => $checkPermission,
        //      ];
        //      return $dataArr;
        //  }
         // check permission end

        $backend_languages = $this->languageService->getLanguages();
        foreach ($backend_languages as $backend_language){
            if ($backend_language->id !== $languageString->language_id){

                $backendLanguageStringValue = $this->getLanguageString(null, $languageString->key);
                if (!empty($backendLanguageStringValue)){

                    // will delete mobile lang string for another lang
                    $backendLanguageStringValue->delete();

                }
            }

            // update json file
            $fileName = $backend_language->symbol . '.json';
            $lang_str = $this->getLanguageStrings($backend_language->id);
            generateLangStrJson($fileName, $lang_str);
        }
        return $name;
    }

    public function getlanguageStringsByKey($request){

        $languages = Language::all();

        $conds['key'] = $request->key;

        $temps = [];
        foreach ($languages as $temp){
            array_push($temps, $temp->id);
        }
        $conds_in['language_ids'] = $temps;

        $languageStrings = [];
        foreach ($languages as $language){
            $languageString = LanguageString::where($this->langStringLanguageIdCol, $language->id)->with(['language'])->where($this->langStringKeyCol, $conds['key'])->first();
            if(!$languageString){
                //if not exist need to save one
                $language_string = new LanguageString();
                $language_string->key = $request->key;
                $language_string->value = $request->key;
                $language_string->language_id = $language->id;
                $language_string->added_user_id = Auth::user()->id;
                $language_string->save();

                $languageString = LanguageString::where($this->langStringIdCol, $language_string->id)->with(['language'])->first();
            }
            array_push($languageStrings, $languageString);
        }

        return $languageStrings;
    }

    public function updateLanguageStrings($request){

        $values = $request->values;
        $key = $request->key;

        DB::beginTransaction();
        try {
            $defaultLanguageString = $this->generateDefaultLanguageString($values);
            foreach ($values as $langString){
                $languageString = $this->getLanguageString($langString['id']);
                // $languageString->key = $key;
                $languageString->value =  $langString['value'] == '' ? $defaultLanguageString : $langString['value'];
                // print_r($languageString->value);
                // print_r($langString['value']);
                // print_r($defaultLanguageString);
                // exit;
                $languageString->added_user_id = Auth::user()->id;
                $languageString->update();

                 // update json file
                $fileName = Language::where($this->langStringIdCol, $langString['language_id'])->first()->symbol . '.json';
                $lang_str = LanguageString::where($this->langStringLanguageIdCol, $langString['language_id'])->get();
                generateLangStrJson($fileName, $lang_str);

            }
            DB::commit();
            $dataArr = [
                "flag" => 'success',
                "msg" => __('language_strings_updated_success'),

            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            $dataArr = [
                "flag" => 'error',
                'msg' => $e->getMessage(),
            ];
        }

        return $dataArr;

    }

    public function generateDefaultLanguageString($values){
        $defaultLang = Language::where('status', 1)->first();

        foreach ($values as $langString){
            if($langString['language_id'] == $defaultLang->id && $langString['value'] != ''){
                return $langString['value'];
            }
        }

        foreach ($values as $langString){
            if($langString['value'] != ''){
                return $langString['value'];
            }
        }

        return '';

    }

    public function storeLanguageStrings($request, $key){

        $values = $request->values;

        DB::beginTransaction();
        try {
            $defaultLanguageString = $this->generateDefaultLanguageString($values);

            foreach ($values as $langString){
                $languageString = $this->getLanguageString(null, $key,$langString['language_id']);
                if($languageString){
                    $languageString->value = $langString['value'];
                    $languageString->added_user_id = Auth::user()->id;
                    $languageString->update();
                }else{
                    $languageString = new LanguageString();
                    $languageString->language_id = $langString['language_id'];
                    $languageString->key = $key;
                    $languageString->value = $langString['value'] == '' ? $defaultLanguageString : $langString['value'];
                    $languageString->added_user_id = Auth::id();
                    $languageString->save();
                }

                 // update json file
                $fileName = Language::where($this->langStringIdCol, $langString['language_id'])->first()->symbol . '.json';
                $lang_str = LanguageString::where($this->langStringLanguageIdCol, $langString['language_id'])->get();
                generateLangStrJson($fileName, $lang_str);

            }
            DB::commit();
            $dataArr = [
                "flag" => 'success',
                "msg" => __('language_strings_updated_success'),

            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            $dataArr = [
                "flag" => 'error',
                'msg' => $e->getMessage(),
            ];
        }

        return $dataArr;

    }

    public function updateAllLanguageStrings(){

        $languages = $this->languageService->getLanguages();

        DB::beginTransaction();
        try {
            foreach ($languages as $language){
                $fileName = $language->symbol . '.json';
                $lang_str = $this->getLanguageStrings($language->id);
                generateLangStrJson($fileName, $lang_str);
            }
            DB::commit();
        } catch (\Throwable $e){
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public function importAllLanguageStrings($data, $sameProject = true){

        // $fileName = $request->recentImportedFileName;
        // $file = file_get_contents($fileName);
        // $dataFromJsonFileFromZip = json_decode($file);
        if(!$sameProject){
            deleteBuilderLanguageStrings();
        }


        $languageStrings = $data;

        $languages = $this->languageService->getLanguages();

        DB::beginTransaction();
        try {
            $serverRefresh = false;
            foreach ($languages as $language){

                $needRefresh = $this->importLanguageString($language->id,$languageStrings);

                if($needRefresh){
                    $fileName = $language->symbol . '.json';
                    $lang_str = $this->getLanguageStrings($language->id);
                    generateLangStrJson($fileName, $lang_str);
                    $serverRefresh = true;
                }

            }
            DB::commit();

            return $serverRefresh;
        } catch (\Throwable $e){
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public function importCSV($languageId, $request){

        $language = $this->languageService->getLanguage($languageId);
        $import = new LanguageStringImport($language);
        $import->import($request->file($this->csvFile));

        // update json file
        $fileName = $language->symbol . '.json';
        $lang_str = LanguageString::where($this->langStringLanguageIdCol, $language->id)->get();
        generateLangStrJson($fileName, $lang_str);


    }

    public function exportJson($languageId){

        $language = Language::where($this->langStringIdCol, $languageId)->first();
        $langStrs = LanguageString::where($this->langStringLanguageIdCol, $language->id)->get();

        $language_strings = [];

        // for col (key, value)
        $language_strings["key"] = "value";

        foreach ($langStrs as $language_string){
            $language_strings[$language_string->key] = $language_string->value;
        }

        $jsonEncoded = json_encode($language_strings,JSON_UNESCAPED_UNICODE);

        $file = 'be_' . $language->symbol . '_tran.json';

        file_put_contents($file,$jsonEncoded);

        return response()->download($file);

    }

    public function exportCSV($languageId)
    {
        $language = Language::where($this->langStringIdCol, $languageId)->first();
        $langStrs = LanguageString::where($this->langStringLanguageIdCol, $language->id)->get();

        $language_strings = "";
        
        // for col (key, value)
        $language_strings .= 'key,value'."\n";

        foreach ($langStrs as $language_string){
            $language_strings .= $language_string->key.','.$language_string->value."\n";
        }
        $file = 'be_' . $language->symbol . '_tran.csv';

        file_put_contents($file,$language_strings);

        return response()->download($file);
    }

    public function importLanguageString($languageId, $rows){

        $currentKeys = [];
        $existKeys = [];

        $needRefresh = false;

        foreach ($rows as $row){
            array_push($currentKeys,$row->key);
        }

        //filter exist keys
        $langStrs = LanguageString::where($this->langStringLanguageIdCol, $languageId)->whereIn($this->langStringKeyCol, $currentKeys)->get();

        //for update
        foreach ($langStrs as $langStr){
            array_push($existKeys,$langStr->key);

            // foreach ($rows as $row){
            //     if($row->key == $langStr->key){
            //         $langStr->value = $row->value;

            //         $langStr->update();
            //     }
            // }

        }

        //for new keys
        foreach ($rows as $row){
            //if not in exist keys
            if (!in_array($row->key, $existKeys)) {
                $language_string = new LanguageString();
                $language_string->key = $row->key;
                $language_string->value = $row->value;
                $language_string->language_id = $languageId;
                $language_string->is_from_builder = 1;
                $language_string->added_user_id = Auth::user()->id;
                $language_string->save();

                $needRefresh = true;
            }
        }

        return $needRefresh;


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
    public function getCoreFieldFilteredLists($code){
        return CoreFieldFilterSetting::where($this->coreFieldFilterModuleNameCol, $code)->get();
    }
}
