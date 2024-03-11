<?php

namespace Modules\Core\Http\Services;

use App\Config\ps_config;
use App\Http\Services\PsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\CoreFieldFilterSetting;
use Modules\Core\Entities\CustomizeUi;
use Modules\Core\Entities\CustomizeUiDetail;
use Modules\Core\Entities\ScreenDisplayUiSetting;
use Modules\Core\Transformers\Backend\Model\CustomizeUiDetail\CustomizeUiDetailWithKeyResource;

class CustomizeUiDetailService extends PsService
{

    private $customizeUiDetailIdCol, $pagePerpag, $customizeUiCoreKeysIdCol, $customizeUiService, $show, $hide, $delete, $unDelete, $viewAnyAbility, $createAbility, $editAbility, $deleteAbility, $code, $screenDisplayUiKeyCol, $screenDisplayUiIdCol, $screenDisplayUiIsShowCol, $coreFieldFilterModuleNameCol, $customUiIsDelCol;

    public function __construct(CustomizeUiService $customizeUiService)
    {
        $this->customizeUiService = $customizeUiService;
        $this->customizeUiCoreKeysIdCol = CustomizeUi::coreKeysId;

        $this->customizeUiDetailIdCol = CustomizeUiDetail::id;
        $this->pagePerpag = ps_config::pagPerPage;

        $this->show = Constants::show;
        $this->hide = Constants::hide;
        $this->delete = Constants::delete;
        $this->unDelete = Constants::unDelete;
        $this->publish = Constants::publish;
        $this->unPublish = Constants::unPublish;
        $this->enable = Constants::enable;
        $this->disable = Constants::disable;

        $this->viewAnyAbility = Constants::viewAnyAbility;
        $this->createAbility = Constants::createAbility;
        $this->editAbility = Constants::editAbility;
        $this->deleteAbility = Constants::deleteAbility;
        $this->code = Constants::customizeUiDetail;

        $this->screenDisplayUiKeyCol = ScreenDisplayUiSetting::key;
        $this->screenDisplayUiIdCol = ScreenDisplayUiSetting::id;
        $this->screenDisplayUiIsShowCol = ScreenDisplayUiSetting::isShow;

        $this->coreFieldFilterModuleNameCol = CoreFieldFilterSetting::moduleName;

        $this->customUiIsDelCol = CustomizeUi::isDelete;

    }

    public function getCustomFieldAttrs($customizeHeaderId = null, $withNoPag = null,$conds = null, $pagPerPage = null){
        $customizeDetails = CustomizeUiDetail::when($customizeHeaderId,function ($q, $data){
                                $q->where($this->customizeUiCoreKeysIdCol, $data);
                            })
                            ->when($conds, function ($query, $conds) {
                                $query = $this->searching($query, $conds);
                            })
                            ->latest();
        if ($pagPerPage){
        $customizeDetails = $customizeDetails->paginate($pagPerPage)->onEachSide(1)->withQueryString();

        } elseif ($withNoPag){
            $customizeDetails = $customizeDetails->get();
        }else{
            $customizeDetails = $customizeDetails->get();
        }
        return $customizeDetails;
    }

    public function searching($query, $conds){
        // search term
        if (isset($conds['keyword']) && $conds['keyword']) {
            $conds['searchterm'] = $conds['keyword'];
        }
        if (isset($conds['searchterm']) && $conds['searchterm']) {
            $search = $conds['searchterm'];
            $query->where(function ($query) use ($search) {
                $query->where(CustomizeUiDetail::tableName . '.' . CustomizeUiDetail::name, 'like', '%' . $search . '%');
            });
        }

        // order by
        if (isset($conds['order_by']) && isset($conds['order_type']) && $conds['order_by'] && $conds['order_type']){

            $query->orderBy($conds['order_by'], $conds['order_type']);

        }

        return $query;
    }

    public function getCustomFieldAttr($id = null){
        $customizeDetail = CustomizeUiDetail::when($id, function ($q, $data){
            $q->where($this->customizeUiDetailIdCol, $data);
        })->first();
        return $customizeDetail;
    }

    public function index($request){
        // check permission
        $checkPermission = $this->checkPermission($this->viewAnyAbility, CustomizeUiDetail::class, "admin.index");

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

        $coreKeysId = $request->field;
        $tableId = $request->table;
        $customizeHeader = $this->customizeUiService->getCustomField(null, null, null, $coreKeysId);

        $customizeDetails = CustomizeUiDetailWithKeyResource::collection($this->getCustomFieldAttrs($coreKeysId,false,$conds,$row));

        // taking for column and columnFilterOption
        $columnAndColumnFilter = $this->takingForColumnAndFilterOption();
        $showCustomizeUiDetailCols = $columnAndColumnFilter['showCoreField'];
        $columnProps = $columnAndColumnFilter['arrForColumnProps'];
        $columnFilterOptionProps = $columnAndColumnFilter['arrForColumnFilterProps'];

        // changing item arr object with new format
        $changedObj = $customizeDetails;
        // $owners = $this->userService->getUsers(null, $this->publish, $this->unBan);

        if($conds['order_by'])
        {
            $dataArr = [
                "tableId" => $tableId,
                "customizeHeader" => $customizeHeader,
                "customizeDetails" => $customizeDetails,
                'checkPermission' => $checkPermission,
                'showCustomizeUiDetailCols' => $showCustomizeUiDetailCols,
                'showCoreAndCustomFieldArr' => $columnProps,
                'hideShowFieldForFilterArr' => $columnFilterOptionProps,
                'sort_field' =>$conds['order_by'],
                'sort_order'=>$request->sort_order,
                'search'=>$conds['searchterm'] ,
            ];
        }
        else
        {
            $dataArr = [
                "tableId" => $tableId,
                "customizeHeader" => $customizeHeader,
                "customizeDetails" => $customizeDetails,
                'checkPermission' => $checkPermission,
                'showCustomizeUiDetailCols' => $showCustomizeUiDetailCols,
                'showCoreAndCustomFieldArr' => $columnProps,
                'hideShowFieldForFilterArr' => $columnFilterOptionProps,
                'search'=>$conds['searchterm'] ,
            ];
        }


        return $dataArr;

    }

    public function create($request){
        $checkPermission = $this->checkPermission($this->createAbility, CustomizeUiDetail::class, "admin.index");

        $coreKeysId = $request->field;
        $tableId = $request->table;
        $customizeHeader = $this->customizeUiService->getCustomField(null, null, null, $coreKeysId);
        $dataArr = [
            "tableId" => $tableId,
            "customizeHeader" => $customizeHeader,
            'checkPermission' => $checkPermission,
        ];
        return $dataArr;
    }

    public function store($request){
        DB::beginTransaction();

        try {
            $customizeDetail = new CustomizeUiDetail();
            $customizeDetail->name = $request->name;
            $customizeDetail->core_keys_id = $request->core_keys_id;
            $customizeDetail->added_user_id = Auth::id();
            $customizeDetail->save();

            DB::commit();
            return $customizeDetail;
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public function edit($request){
        $coreKeysId = $request->field;
        $tableId = $request->table;
        $attributeId = $request->attribute;
        $customizeHeader = $this->customizeUiService->getCustomField(null, null, null, $coreKeysId);
        $customizeDetail = $this->getCustomFieldAttr($attributeId);

        $checkPermission = $this->checkPermission($this->editAbility, $customizeDetail, "admin.index");

        $dataArr = [
            "tableId" => $tableId,
            "customizeHeader" => $customizeHeader,
            "customizeDetail" => $customizeDetail,
            'checkPermission' => $checkPermission,
        ];
        return $dataArr;
    }

    public function update($request){
        DB::beginTransaction();

        try {
            $id = $request->attribute;
            $customizeDetail = $this->getCustomFieldAttr($id);
            $customizeDetail->name = $request->name;
            $customizeDetail->core_keys_id = $request->core_keys_id;
            $customizeDetail->added_user_id = Auth::id();
            $customizeDetail->update();

            DB::commit();
            return $customizeDetail;
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];

        }
    }

    public function destroy($request){
        $coreKeysId = $request->field;
        $attributeId = $request->attribute;
        $customizeHeader = $this->customizeUiService->getCustomField(null, null, null, $coreKeysId);
        $customizeDetail = $this->getCustomFieldAttr($attributeId);

        $checkPermission = $this->checkPermission($this->deleteAbility, $customizeDetail, "admin.index");
        if (!empty($checkPermission)){
            $dataArr = [
                'checkPermission' => $checkPermission,
            ];
            return $dataArr;
        }

        $name = $customizeDetail->name;
        $customizeDetail->delete();

        $dataArr = [
            "name" => $name,
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
