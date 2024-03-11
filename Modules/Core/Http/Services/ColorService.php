<?php
namespace Modules\Core\Http\Services;

use App\Http\Services\PsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\Color;
use Modules\Core\Entities\CoreFieldFilterSetting;
use Modules\Core\Entities\CustomizeUi;
use Modules\Core\Entities\MobileSetting;
use Modules\Core\Entities\ScreenDisplayUiSetting;
use Modules\Core\Transformers\Backend\Model\Color\ColorWithKeyResource;

class ColorService extends PsService
{

    protected $tableName, $colorIdCol, $colorKeyCol, $colorValueCol, $colorTitleCol, $colorAddedDateCol, $feColorCol, $mbColorCol, $noContentStatusCode, $successStatus, $delete, $unDelete, $customUiIsDelCol, $radioUi, $dropDownUi, $show, $hide, $code, $viewAnyAbility, $createAbility, $editAbility, $deleteAbility, $screenDisplayUiKeyCol, $screenDisplayUiIdCol, $screenDisplayUiIsShowCol, $coreFieldFilterModuleNameCol;

    public function __construct()
    {
        $this->tableName = Color::tableName;
        $this->colorIdCol = Color::id;
        $this->colorKeyCol = Color::key;
        $this->colorValueCol = Color::value;
        $this->colorTitleCol = Color::title;
        $this->mbColorCol = Color::mbColor;
        $this->feColorCol = Color::feColor;
        $this->colorAddedDateCol = Color::addedDate;

        $this->noContentStatusCode = Constants::noContentStatusCode;
        $this->successStatus = Constants::successStatus;

        $this->code = Constants::color;
        $this->viewAnyAbility = Constants::viewAnyAbility;
        $this->createAbility = Constants::createAbility;
        $this->editAbility = Constants::editAbility;
        $this->deleteAbility = Constants::deleteAbility;

        $this->screenDisplayUiKeyCol = ScreenDisplayUiSetting::key;
        $this->screenDisplayUiIdCol = ScreenDisplayUiSetting::id;
        $this->screenDisplayUiIsShowCol = ScreenDisplayUiSetting::isShow;

        $this->coreFieldFilterModuleNameCol = CoreFieldFilterSetting::moduleName;
        $this->show = Constants::show;
        $this->hide = Constants::hide;
        $this->delete = Constants::delete;
        $this->unDelete = Constants::unDelete;
        $this->dropDownUi = Constants::dropDownUi;
        $this->radioUi = Constants::radioUi;

        $this->customUiIsDelCol = CustomizeUi::isDelete;
    }

    public function create(){
        // check permission
        $checkPermission = $this->checkPermission($this->createAbility, Color::class, "admin.index");
        $dataArr = [
            "checkPermission" => $checkPermission,
        ];
        return $dataArr;
    }

    public function edit($id){
        $color = $this->getColor($id);
        // check permission
        $checkPermission = $this->checkPermission($this->editAbility, $color, "admin.index");

        $dataArr = [
            "color" => $color,
            "checkPermission" => $checkPermission,
        ];
        return $dataArr;
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            $color = new Color();
            $color->key = $request->key;
            $color->value = $request->value;
            $color->title = $request->title;
            $color->is_light_color = $request->is_light_color;
            $color->is_common_color = $request->is_common_color;
            $color->is_dark_color = $request->is_dark_color;
            $color->added_user_id = Auth::user()->id;
            $color->save();

            DB::commit();
            return $color;
        }catch (\Throwable $e){
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }

        return $color;
    }

    public function update($request)
    {
        DB::beginTransaction();
        try {
            $color = Color::find($request["id"]);
            if(isset($request['key']) && !empty($request['key'])){
                $color->key = $request['key'];
            }
            if(isset($request['value']) && !empty($request['value'])){
                $color->value = $request['value'];
            }
            if(isset($request['title']) && !empty($request['title'])){
                $color->title = $request['title'];
            }
            if(isset($request['fe_color']) && !empty($request['fe_color'])){
                $color->fe_color = $request['fe_color'];
            }
            if(isset($request['mb_color']) && !empty($request['mb_color'])){
                $color->mb_color = $request['mb_color'];
            }
            $color->updated_user_id = Auth::user()->id;
            $color->update();

            // update color_change_code in mobile_settings
            $mobile_setting = MobileSetting::first();
            $mobile_setting->color_change_code = Carbon::now()->getPreciseTimestamp(3);
            $mobile_setting->update();

            DB::commit();
        }catch (\Throwable $e){

            DB::rollBack();
            return ['error' => $e->getMessage()];
        }


        // return $color;
    }

    public function destroy($id){
        $color = Color::find($id);

        // check permission
        $checkPermission = $this->checkPermission($this->deleteAbility, $color, "admin.index");
        if (!empty($checkPermission)){
            $dataArr = [
                "checkPermission" => $checkPermission,
            ];
            return $dataArr;
        }
        $name = $color->title;

        $color->delete();

        $dataArr = [
            "name" => $name,
            "checkPermission" => $checkPermission,
        ];
        return $dataArr;
    }
    // ----------
    public function index(){

        $code = $this->code;
        $coreFieldFilterSettings = $this->getCoreFieldFilteredLists($code);
        // check permission
        $checkPermission = $this->checkPermission($this->viewAnyAbility, CoreAbout::class, "admin.index");

        $color = new ColorWithKeyResource($this->getColors());

        // taking for column and columnFilterOption
        $columnAndColumnFilter = $this->takingForColumnAndFilterOption();
        $showAboutCols = $columnAndColumnFilter['showCoreField'];
        $columnProps = $columnAndColumnFilter['arrForColumnProps'];
        $columnFilterOptionProps = $columnAndColumnFilter['arrForColumnFilterProps'];

        $dataArr = [
            'checkPermission' => $checkPermission,
            'color' => $color,
            'showAboutCols' => $showAboutCols,
            'showCoreAndCustomFieldArr' => $columnProps,
            'hideShowFieldForFilterArr' => $columnFilterOptionProps,
            'coreFieldFilterSettings' => $coreFieldFilterSettings
        ];
        return $dataArr;
    }

    public function getCoreFieldFilteredLists($code){
        return CoreFieldFilterSetting::where($this->coreFieldFilterModuleNameCol, $code)->get();
    }

    public function getColor($id){
        $color = Color::where($this->colorIdCol, $id)->first();
        return $color;
    }

    public function getColorByKey($key){
        $color = Color::where('key', $key)->first();
        return $color;
    }
    public function getColors($limit = null, $offset = null, $conds = null,$noPagination = null,  $pagPerPage = null){
        $colors = Color::when($limit, function ($query, $limit) {
                                    $query->limit($limit);
                                })
                                ->when($offset, function ($query, $offset) {
                                    $query->offset($offset);
                                })
                                ->when($conds, function ($query, $conds) {
                                    $query = $this->searching($query, $conds);
                                });
                                if ($pagPerPage){
                                $colors = $colors->paginate($pagPerPage)->onEachSide(1)->withQueryString();

                                } elseif ($noPagination){
                                    $colors = $colors->get();
                                }else{
                                    $colors = $colors->get();
                                }
        return $colors;
    }

    public function searching($query, $conds){
        // search term
        if (isset($conds['keyword']) && $conds['keyword']) {
            $conds['searchterm'] = $conds['keyword'];
        }

        if (isset($conds['searchterm']) && $conds['searchterm']) {
            $search = $conds['searchterm'];
            $query->where(function ($query) use ($search) {
                $query->where($this->tableName . '.' . $this->colorTitleCol, 'like', '%' . $search . '%');
            });
        }

        if(isset($conds[$this->colorKeyCol]) && $conds[$this->colorKeyCol]){
            $query->where($this->tableName .'.'.$this->colorKeyCol, $conds[$this->colorKeyCol]);
        }

        if(isset($conds[$this->colorValueCol]) && $conds[$this->colorValueCol]){
            $query->where($this->tableName .'.'.$this->colorValueCol, $conds[$this->colorValueCol]);
        }

        if(isset($conds[$this->colorTitleCol]) && $conds[$this->colorTitleCol]){
            $query->where($this->tableName .'.'.$this->colorTitleCol, $conds[$this->colorTitleCol]);
        }

        // if(isset($conds[$this->colorIsLightColorCol])){
        //     $query->where($this->tableName .'.'.$this->colorIsLightColorCol, $conds[$this->colorIsLightColorCol]);
        // }

        if(isset($conds[$this->feColorCol])){
            $query->where($this->tableName .'.'.$this->feColorCol, $conds[$this->feColorCol]);
        }

        if(isset($conds[$this->mbColorCol])){
            $query->where($this->tableName .'.'.$this->mbColorCol, $conds[$this->mbColorCol]);
        }

        if(isset($conds['selected_date']) && $conds['selected_date']){
            $date_filter=$conds['selected_date'];
            $new_date=date('Y-m-d', strtotime($date_filter));

            $query->whereDate($this->tableName . '.added_date','=',$new_date);
        }

        if(isset($conds['added_date']) && $conds['added_date']){
            $date_filter = $conds['added_date'];
            $query->where($this->tableName .'.'.$this->colorAddedDateCol, $date_filter);
        }

        if(isset($conds['date_range']) && $conds['date_range']){
            $date_filter = $conds['date_range'];
            if($date_filter[1] == ''){
                $date_filter[1] = Carbon::now();
            }
            $query->whereBetween($this->tableName . '.'.$this->colorAddedDateCol, $date_filter);
        }
        // order by
        if (isset($conds['order_by']) && isset($conds['order_type']) && $conds['order_by'] && $conds['order_type']){

            if($conds['order_by'] == 'id'){
                $query->orderBy($this->tableName.'.id', $conds['order_type']);
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
                $type = $showFields->customizeField->data_type;
                $isShowSorting = $showFields->customizeField->is_show_sorting;
                $ordering = $showFields->customizeField->ordering;

                $id = $showFields->id;
                $hidden = $showFields->is_show ? false : true;
                $moduleName = $showFields->customizeField->module_name;
                $keyId = $showFields->customizeField->core_keys_id;

            $customFieldObjForColumnsProps = $this->takingForColumnProps($label, $field, $type ,$isShowSorting, $ordering);
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

}
