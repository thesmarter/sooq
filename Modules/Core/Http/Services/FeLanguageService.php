<?php
namespace Modules\Core\Http\Services;

use App\Http\Services\PsService;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Modules\Core\Constants\Constants;

use Modules\Core\Entities\Language;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Entities\CoreFieldFilterSetting;
use Modules\Core\Entities\CustomizeUi;
use Modules\Core\Entities\FeLanguageString;
use Modules\Core\Entities\ScreenDisplayUiSetting;
use Modules\Core\Transformers\Backend\Model\Language\LanguageWithKeyResource;
use Illuminate\Support\Facades\Gate;
use Modules\Core\Entities\LanguageString;

class FeLanguageService extends PsService
{
    protected  $userTableName,$userNameCol,$langStatusCol, $langTableName, $publish, $unPublish, $default, $unDefault, $warning, $success, $danger, $show, $hide, $delete, $unDelete, $viewAnyAbility, $createAbility, $editAbility, $deleteAbility, $code, $screenDisplayUiKeyCol, $screenDisplayUiIdCol, $screenDisplayUiIsShowCol, $coreFieldFilterModuleNameCol, $customUiIsDelCol;

    public function __construct()
    {

        $this->publish = Constants::publish;
        $this->unPublish = Constants::unPublish;
        $this->default = Constants::default;
        $this->unDefault = Constants::unDefault;
        $this->warning = Constants::warning;
        $this->success = Constants::success;
        $this->danger = Constants::danger;
        $this->langTableName = Language::tableName;
        $this->langStatusCol = Language::status;
        $this->langNameCol = Language::name;
        $this->langSymbolCol = Language::symbol;
        $this->langStringkeyCol = FeLanguageString::key;

        $this->langStringLanguageIdCol = FeLanguageString::languageId;

        $this->show = Constants::show;
        $this->hide = Constants::hide;
        $this->delete = Constants::delete;
        $this->unDelete = Constants::unDelete;

        $this->viewAnyAbility = Constants::viewAnyAbility;
        $this->createAbility = Constants::createAbility;
        $this->editAbility = Constants::editAbility;
        $this->deleteAbility = Constants::deleteAbility;
        $this->code = Constants::language;

        $this->screenDisplayUiKeyCol = ScreenDisplayUiSetting::key;
        $this->screenDisplayUiIdCol = ScreenDisplayUiSetting::id;
        $this->screenDisplayUiIsShowCol = ScreenDisplayUiSetting::isShow;

        $this->coreFieldFilterModuleNameCol = CoreFieldFilterSetting::moduleName;

        $this->customUiIsDelCol = CustomizeUi::isDelete;

        $this->userTableName = User::tableName;
        $this->userIdCol = User::id;
        $this->userNameCol = User::name;

    }

    public function create($request)
    {

        DB::beginTransaction();
        try {
            $language = new Language();
            $language->symbol = $request->symbol;
            $language->name = $request->name;
            $language->added_user_id = Auth::user()->id;
            $language->save();

            $activeLanguage = Language::where($this->langStatusCol, 1)->first();

            $languageStrings = FeLanguageString::where($this->langStringLanguageIdCol, $activeLanguage->id)->get();

            foreach ($languageStrings as $languageString){
                $languageStringData[] = [
                    'key' => $languageString->key,
                    'value' => $languageString->value,
                    'language_id' => $language->id,
                    'added_user_id' => Auth::id(),
                ];
            }
            FeLanguageString::insert($languageStringData);

            // update json file
            $fileName = $language->symbol . '.json';
            $lang_str = FeLanguageString::where($this->langStringLanguageIdCol, $language->id)->get();
            generateFEangStrJson($fileName, $lang_str);

            $belanguageStrings = LanguageString::where($this->langStringLanguageIdCol, $activeLanguage->id)->get();
            foreach ($belanguageStrings as $languageString){
                $belanguageStringData[] = [
                    'key' => $languageString->key,
                    'value' => $languageString->value,
                    'language_id' => $language->id,
                    'added_user_id' => Auth::id(),
                ];
            }
            LanguageString::insert($belanguageStringData);

            // update json file
            $fileName = $language->symbol . '.json';
            $lang_str = LanguageString::where($this->langStringLanguageIdCol, $language->id)->get();
            generateLangStrJson($fileName, $lang_str);

            DB::commit();
        } catch (\Throwable $e){
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }

        return $language;
    }

    public function update($id,$request)
    {
        DB::beginTransaction();
        try {
            $language = $this->getLanguage($id);
            $language->symbol = $request->symbol;
            $language->name = $request->name;
            $language->added_user_id = Auth::user()->id;
            $language->update();

            // update json file
            $fileName = $language->symbol . '.json';
            $lang_str = FeLanguageString::where($this->langStringLanguageIdCol, $language->id)->get();
            generateFEangStrJson($fileName, $lang_str);
            DB::commit();
        } catch (\Throwable $e){
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }

        return $language;
    }

    public function getLanguages($relations = null,$pagPerPage = null,$conds = null){
        $languages = Language::when(isset($conds['order_by']) && $conds['order_by'] , function ($q) use($conds){
            if($conds['order_by'] == 'added_user_id' || $conds['order_by'] == 'updated_user_id')
            {
                $q->leftJoin($this->userTableName, "$this->userNameCol.$this->userIdCol", '=', "$this->langTableName.".$conds['order_by']);
                $q->select("$this->userTableName.$this->userNameCol as owner", "$this->langTableName.*");
            }

        })->when($conds, function ($query, $conds) {
            $query = $this->searching($query, $conds);
        })->when($relations, function ($q, $relations){
            $q->with($relations);
        })->latest();

        if ($pagPerPage){
            $languages = $languages->paginate($pagPerPage)->onEachSide(1)->withQueryString();
        } else{
            $languages = $languages->get();
        }
        return $languages;
    }

    // public function getActiveLanguage(){
    //     $language = Language::where('status', $this->publish)->first();
    //     return $language;
    // }

    public function getlanguageTable($request){

        $language = Language::where('symbol', $request->symbol)->first();

        $conds['key'] = $request->key;
        $conds['language_id'] = $language->id;

        $languageString = FeLanguageString::when($conds, function ($query, $conds) {
                // search term
                if(isset($conds['keyword']) && $conds['keyword']){
                    $query->where($this->langStringkeyCol, 'LIKE', '%' . $conds['keyword'] . '%');
                }
                if (isset($conds['language_id']) && $conds['language_id']) {
                    $query->where($this->langStringLanguageIdCol, $conds['language_id']);
                }
                if (isset($conds['key']) && $conds['key']) {
                    $query->where($this->langStringkeyCol, $conds['key']);
                }
            })->with(['language'])->first();

        $dataArr = [
            "languageString" => $languageString,
        ];
        return $dataArr;
    }


    public function getLanguage($id){
        $language = Language::find($id);
        return $language;
    }

    public function delete($id){
        $language = $this->getLanguage($id);
        $name = $language->name;
        if($language->status == $this->publish){
            // $msg = 'The '.$name.' row cannot be deleted because of activated language.';
            $msg = __('core__be_default_active_lang_delete',['attribute'=>$name]);
            return $msg;
        }
        $language->delete();

    }

    public function createView(){
    // check permission start
    $checkPermission = $this->checkPermission($this->createAbility,Language::class, "admin.index");
    // check permission end
    $dataArr = [
            "checkPermission" => $checkPermission,
        ];
        return $dataArr;
    }

    public function makePublishOrUnpublish($id){
        $language = $this->getLanguage($id);
        if($language->status == $this->publish){
            // $msg = ' Sorry, the status of '.$language->name.' row cannot be changed because it is active language.';
            $msg = __('core__be_default_active_lang',['attribute'=>$language->name]);
            return $msg;
        }else{
            DB::table($this->langTableName)->update([$this->langStatusCol => $this->unPublish]);

            $language->status = $this->publish;
            $language->updated_user_id = Auth::user()->id;
        }
        $language->update();

    }



    // ----------
    public function index($request){

        $conds['searchterm'] = $request->input('search') ?? '';
        $conds['order_by']= null;
        $conds['order_type'] = null;
        $row = $request->input('row') ?? Constants::dataTableDefaultRow;
        if($request->sort_field)
        {
            $conds['order_by'] = $request->sort_field;
            $conds['order_type'] = $request->sort_order;
        }
        // check permission start
        $checkPermission = $this->checkPermission($this->viewAnyAbility,Language::class, "admin.index");
        // check permission end

        $relations = ['owner', 'editor'];
        $languages = LanguageWithKeyResource::collection($this->getLanguages($relations,$row,$conds));

        // taking for column and columnFilterOption
        $columnAndColumnFilter = $this->takingForColumnAndFilterOption();
        $showLanguageCols = $columnAndColumnFilter['showCoreField'];
        $columnProps = $columnAndColumnFilter['arrForColumnProps'];
        $columnFilterOptionProps = $columnAndColumnFilter['arrForColumnFilterProps'];
        if($conds['order_by'])
        {
            $dataArr = [
                'checkPermission' => $checkPermission,
                'frontend_languages' => $languages,
                'showLanguageCols' => $showLanguageCols,
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
                'frontend_languages' => $languages,
                'showLanguageCols' => $showLanguageCols,
                'showCoreAndCustomFieldArr' => $columnProps,
                'hideShowFieldForFilterArr' => $columnFilterOptionProps,
                'search'=>$conds['searchterm'] ,
            ];
        }

        return $dataArr;

    }

    public function searching($query, $conds){
        // search term
        if (isset($conds['searchterm']) && $conds['searchterm']) {
            $search = $conds['searchterm'];
            $query->where(function ($query) use ($search) {
                $query->where($this->langNameCol, 'like','%'.$search.'%');
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
            $query->orderBy($this->langStatusCol, 'desc')->orderBy('name', 'asc');
        }
        return $query;
    }

    public function edit($id){
        // $checkPermission = $this->checkPermission($this->editAnyAbility, Language::class, "admin.index");
        $language = $this->getLanguage($id);
         // check permission start
         $checkPermission = $this->checkPermission($this->editAbility, $language, "admin.index");
         // check permission end

        $dataArr = [
            "language" => $language,
            "checkPermission" => $checkPermission,
    ];
        return $dataArr;
    }

    public function destroy($id){
        // $checkPermission = $this->checkPermission($this->deleteAnyAbility, Language::class, "admin.index");
        $name = $this->getLanguage($id)->name;
        $language = $this->getLanguage($id);
        $msg = $this->delete($id);
        $checkPermission = $this->checkPermission($this->deleteAbility, $language, "admin.index");
        if (!empty($checkPermission)){
            $dataArr = [
                "checkPermission" => $checkPermission,
            ];
            return $dataArr;
        }

        // if default currency
        if ($msg){
            $dataArr = [
                "msg" => $msg,
                "flag" => $this->warning,
            ];
            return $dataArr;
        }

        // $msg = 'The '.$name.' row has been deleted successfully.';
        $msg=__('core__be_delete_success',['attribute'=>$name]);
        $dataArr = [
            "msg" => $msg,
            "flag" => $this->success,
        ];
        return $dataArr;
    }

    public function statusChange($id){
        $name = $this->getLanguage($id)->name;

        $msg = $this->makePublishOrUnpublish($id);

        // if default currency
        if ($msg){
            $dataArr = [
                "msg" => $msg,
                "flag" => $this->warning
            ];
            return $dataArr;
        }

        // $msg = 'The status of '.$name.' row has been updated successfully.';
        $msg=__('core__be_status_attribute_updated',['attribute'=>$name]);
        $dataArr = [
            "msg" => $msg,
            "flag" => $this->success
        ];
        return $dataArr;
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
        $controlFieldObj = $this->takingForColumnProps(__('action_label'), "action", "Action", false, 0);
        array_push($controlFieldArr, $controlFieldObj);

        // $controlFieldObj = $this->takingForColumnProps(__('lang_string'), "language_string", "Language String", false, 0);
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
}
