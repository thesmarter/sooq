<?php
namespace Modules\Core\Http\Services;

use App\Http\Services\PsService;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\CoreFieldFilterSetting;
use Modules\Core\Entities\VendorMenu;
use Modules\Core\Entities\VendorModule;
use Modules\Core\Entities\CustomizeUi;
use Modules\Core\Entities\Language;
use Modules\Core\Entities\ScreenDisplayUiSetting;
use Modules\Core\Transformers\Backend\Model\VendorMenu\VendorMenuWithKeyResource;
use Modules\Core\Entities\MobileSetting;

class VendorMenuService extends PsService
{
    protected $iconService, $yes, $no, $moduleService, $unPublish, $publish, $successFlag, $dangerFlag, $coreModuleIdCol, $subMenuGroupService, $show, $hide, $delete, $unDelete, $viewAnyAbility, $createAbility, $editAbility, $deleteAbility, $code, $screenDisplayUiKeyCol, $screenDisplayUiIdCol, $screenDisplayUiIsShowCol, $coreFieldFilterModuleNameCol, $customUiIsDelCol;

    public function __construct(VendorSubMenuGroupService $subMenuGroupService, VendorModuleService $moduleService, IconService $iconService)
    {
        $this->subMenuGroupService = $subMenuGroupService;
        $this->moduleService =  $moduleService;
        $this->iconService = $iconService;
        $this->coreModuleIdCol = VendorMenu::id;
        $this->dangerFlag = Constants::danger;
        $this->successFlag = Constants::success;
        $this->publish = Constants::publish;
        $this->unPublish = Constants::unPublish;
        $this->show = Constants::show;
        $this->hide = Constants::hide;
        $this->delete = Constants::delete;
        $this->unDelete = Constants::unDelete;
        $this->yes = Constants::yes;
        $this->no = Constants::no;

        $this->viewAnyAbility = Constants::viewAnyAbility;
        $this->createAbility = Constants::createAbility;
        $this->editAbility = Constants::editAbility;
        $this->deleteAbility = Constants::deleteAbility;
        $this->code = Constants::vendorMenu;

        $this->screenDisplayUiKeyCol = ScreenDisplayUiSetting::key;
        $this->screenDisplayUiIdCol = ScreenDisplayUiSetting::id;
        $this->screenDisplayUiIsShowCol = ScreenDisplayUiSetting::isShow;

        $this->coreFieldFilterModuleNameCol = CoreFieldFilterSetting::moduleName;

        $this->customUiIsDelCol = CustomizeUi::isDelete;

        $this->userTableName = User::tableName;
        $this->userIdCol = User::id;
        $this->userNameCol = User::name;

        $this->languageTableName = Language::tableName;
        $this->languageIdCol = Language::id;

        $this->coreMenuIdCol = VendorMenu::id;
    }

    public function store($request)
    {
        DB::beginTransaction();

        try {
            
            $menu = new VendorMenu();
            $menu->module_name = $request->module_name;
            $menu->module_desc = $request->module_desc;
            $menu->module_lang_key = $request->module_lang_key;
            $menu->icon_id = '1';
            $menu->module_id = $request->module_id;
            $menu->ordering = $request->ordering;
            $menu->is_show_on_menu = $request->is_show_on_menu;
            $menu->core_sub_menu_group_id = $request->core_sub_menu_group_id;
            $menu->added_user_id = Auth::user()->id;
            $menu->save();
            $module = $this->moduleService->getModule($menu->module_id);
            $module->menu_id = $menu->id;
            $module->update();

            DB::commit();
        } catch (\Throwable $e){
            dd($e->getMessage());
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }

        return $menu;
    }

    public function update($menuId,$request)
    {
        DB::beginTransaction();

        try {
            $menu = $this->getVendorMenu($menuId);
            $menu->module_name = $request->module_name;
            $menu->module_desc = $request->module_desc;
            $menu->module_lang_key = $request->module_lang_key;
            $menu->icon_id = $request->icon_id;
            $menu->module_id = $request->module_id;
            $menu->ordering = $request->ordering;
            $menu->is_show_on_menu = $request->is_show_on_menu;
            $menu->core_sub_menu_group_id = $request->core_sub_menu_group_id;
            $menu->added_user_id = Auth::user()->id;
            $menu->update();

            $oldModule = $this->moduleService->getModule($request->old_module_id);
            $oldModule->menu_id = 0;
            $oldModule->update();

            $module = $this->moduleService->getModule($menu->module_id);
            $module->menu_id = $menu->id;
            $module->update();

            DB::commit();
        } catch (\Throwable $e){
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }

        return $menu;
    }

    public function getVendorMenus($relation = null,$pagPerPage = null,$conds=null){
        $id = '0';
        if(MobileSetting::first()->is_show_subcategory == '0'){
            $subcategoryModule = VendorModule::where('route_name','subcategory.index')->first();
            if($subcategoryModule){
                $subcategoryMenu = VendorMenu::where('module_id',$subcategoryModule->id)->first();
                if($subcategoryMenu){
                    $id = $subcategoryMenu->id;
                }
            }
        }

        $modules = VendorMenu::when(isset($conds['order_by']) && $conds['order_by'] , function ($q) use($conds){
                                if($conds['order_by'] == 'added_user_id' || $conds['order_by'] == 'updated_user_id')
                                {
                                    $q->leftJoin($this->userTableName, $this->userTableName.'.'.$this->userIdCol, '=', $this->languageTableName.'.'.$conds['order_by']);
                                    $q->select($this->userTableName.'.'.$this->userNameCol.' as owner', $this->languageTableName.'.*');
                                }
                                })
                                ->wherenot('id',$id)                                
                                ->when($conds, function ($query, $conds) {
                                    $query = $this->searching($query, $conds);
                                })->when($relation, function ($q, $relation){
                                    $q->with($relation);
                                });
        if ($pagPerPage){
            $modules = $modules->paginate($pagPerPage)->onEachSide(1)->withQueryString();

        } else{
            $modules = $modules->get();
        }
        return $modules;
    }
    public function searching($query, $conds){
        // search term
        if (isset($conds['searchterm']) && $conds['searchterm']) {
            $search = $conds['searchterm'];
            $query->where(function ($query) use ($search) {
                $query->where($this->coreFieldFilterModuleNameCol,'like','%'.$search.'%')
                ->orwhere('module_desc','like','%'.$search.'%');
            });
        }
        if(isset($conds['sub_menu_id'])  && $conds['sub_menu_id'] != 'all')
        {
           $query->where('core_sub_menu_group_id', $conds['sub_menu_id']);
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
            $query->orderBy('is_show_on_menu', 'desc')->orderBy($this->coreFieldFilterModuleNameCol, 'asc');
        }
        return $query;
    }

    public function getVendorMenu($id = null){
        $module = VendorMenu::when($id, function ($q, $id){
            $q->where($this->coreModuleIdCol, $id);
        })->first();
        return $module;
    }

	public function index($request){
        // check permission
        $checkPermission = $this->checkPermission($this->viewAnyAbility, VendorMenu::class, "admin.index");
        $conds['searchterm'] = $request->input('search') ?? '';

        $conds['order_by']= null;
        $conds['order_type'] = null;
        $row = $request->input('row') ?? Constants::dataTableDefaultRow;

        $conds['sub_menu_id'] = $request->input('sub_menu_filter') == 'all'? null  : $request->sub_menu_filter;

        if($request->sort_field)
        {
            $conds['order_by'] = $request->sort_field;
            $conds['order_type'] = $request->sort_order;
        }


        $sub_menu_groups = $this->subMenuGroupService->getSubMenuGroups();
        // dd($sub_menu_groups);
        $relations = ['core_sub_menu_group', 'owner', 'editor'];
        $modules = VendorMenuWithKeyResource::collection($this->getVendorMenus($relations,$row,$conds));

        // taking for column and columnFilterOption
        $columnAndColumnFilter = $this->takingForColumnAndFilterOption();
        $showModuleCols = $columnAndColumnFilter['showCoreField'];
        $columnProps = $columnAndColumnFilter['arrForColumnProps'];
        $columnFilterOptionProps = $columnAndColumnFilter['arrForColumnFilterProps'];
        if($conds['order_by'])
        {
            $dataArr = [
                'checkPermission' => $checkPermission,
                'modules' => $modules,
                'sub_menu_groups' => $sub_menu_groups,
                'showModuleCols' => $showModuleCols,
                'showCoreAndCustomFieldArr' => $columnProps,
                'hideShowFieldForFilterArr' => $columnFilterOptionProps,
                'sort_field' =>$conds['order_by'],
                'sort_order'=>$request->sort_order,
                'search'=>$conds['searchterm'] ,
                'selectedSubMenu'=>$conds['sub_menu_id'] ,
            ];
        }
        else{
            $dataArr = [
                'checkPermission' => $checkPermission,
                'modules' => $modules,
                'sub_menu_groups' => $sub_menu_groups,
                'showModuleCols' => $showModuleCols,
                'showCoreAndCustomFieldArr' => $columnProps,
                'hideShowFieldForFilterArr' => $columnFilterOptionProps,
                'search'=>$conds['searchterm'] ,
                'selectedSubMenu'=>$conds['sub_menu_id'] ,
            ];
        }
        return $dataArr;

    }

    public function create(){
        $subMenuGroups = $this->subMenuGroupService->getSubMenuGroups();
        $icons = $this->iconService->getIcons();
        $modules = $this->moduleService->getModules(null, null, null, $this->publish, $this->yes);
        $code = $this->code;
        $coreFieldFilterSettings = $this->getCoreFieldFilteredLists($code);
        // dd($coreFieldFilterSettings);

        $checkPermission = $this->checkPermission($this->createAbility,VendorMenu::class, "admin.index");
        // check permission end
        $dataArr = [
            'sub_menu_groups' => $subMenuGroups,
            'modules' => $modules,
            'icons' => $icons,
            'checkPermission' => $checkPermission,
            'coreFieldFilterSettings' => $coreFieldFilterSettings,
        ];
        return $dataArr;
    }

    public function edit($menuId){
        $menu = $this->getVendorMenu($menuId);
        $subMenuGroups = $this->subMenuGroupService->getSubMenuGroups();
        $code = $this->code;
        $icons = $this->iconService->getIcons();
        $modules = $this->moduleService->getModules(null, null, null, $this->publish, $this->yes);
        $forSelectedModules = $this->moduleService->getModules();
        $coreFieldFilterSettings = $this->getCoreFieldFilteredLists($code);
        // check permission start
        $checkPermission = $this->checkPermission($this->editAbility, $menu, "admin.index");
        $dataArr = [
            'menu' => $menu,
            'modules' => $modules,
            'forSelectedModules' => $forSelectedModules,
            'icons' => $icons,
            'sub_menu_groups' => $subMenuGroups,
            'checkPermission' => $checkPermission,
            'coreFieldFilterSettings' => $coreFieldFilterSettings,
        ];
        return $dataArr;
    }

    public function destroy($menuId){
        $menu = $this->getVendorMenu($menuId);
        $module = $this->moduleService->getModule($menu->module_id);

        $name = $menu->module_desc;
        $checkPermission = $this->checkPermission($this->deleteAbility, $menu, "admin.index");
        if (!empty($checkPermission)){
            $dataArr = [
                "checkPermission" => $checkPermission,
            ];
            return $dataArr;
        }

        DB::beginTransaction();

        try {
            $menu->delete();

            $module->menu_id = 0;
            $module->update();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $status = [
                'msg' => $e->getMessage(),
                'flag' => $this->dangerFlag,
            ];

            return $status;
        }

        $status = [
            'msg' => 'The '.$name.' row has been deleted successfully.',
            'flag' => $this->dangerFlag,
            "checkPermission" => $checkPermission,
        ];

        return $status;
    }
    public function getCoreFieldFilteredLists($code){
        return CoreFieldFilterSetting::where($this->coreFieldFilterModuleNameCol, $code)->get();
    }

    public function statusChange($moduleId){
        $module = $this->getVendorMenu($moduleId);
        if($module->is_show_on_menu == $this->publish){
            $module->is_show_on_menu = $this->unPublish;
        }else{
            $module->is_show_on_menu = $this->publish;
        }
        $module->updated_user_id = Auth::user()->id;
        $module->update();

        $status = [
            'msg' => 'The status of '.$module->module_desc.' row has been updated successfully.',
            'flag' => $this->successFlag,
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
