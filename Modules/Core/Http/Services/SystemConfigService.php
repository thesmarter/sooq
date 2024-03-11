<?php

namespace Modules\Core\Http\Services;

use App\Http\Services\PsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\AdPostType;
use Modules\Core\Entities\CoreFieldFilterSetting;
use Modules\Core\Entities\CustomizeUi;
use Modules\Core\Entities\ScreenDisplayUiSetting;
use Modules\Core\Entities\SystemConfig;
use Modules\Core\Transformers\Backend\Model\SystemConfig\SystemConfigWithKeyResource;
use Modules\Core\Transformers\Backend\Model\MobileSetting\MobileSettingWithKeyResource;
use Modules\Core\Entities\Setting;

class SystemConfigService extends PsService
{
    protected $show, $hide, $mobileSettingService, $delete, $unDelete, $viewAnyAbility, $createAbility, $editAbility, $deleteAbility, $code, $screenDisplayUiKeyCol, $screenDisplayUiIdCol, $screenDisplayUiIsShowCol, $coreFieldFilterModuleNameCol, $customUiIsDelCol;

    public function __construct(MobileSettingService $mobileSettingService,)
    {
        $this->show = Constants::show;
        $this->hide = Constants::hide;
        $this->delete = Constants::delete;
        $this->unDelete = Constants::unDelete;

        $this->viewAnyAbility = Constants::viewAnyAbility;
        $this->createAbility = Constants::createAbility;
        $this->editAbility = Constants::editAbility;
        $this->deleteAbility = Constants::deleteAbility;
        $this->code = Constants::systemConfig;

        $this->screenDisplayUiKeyCol = ScreenDisplayUiSetting::key;
        $this->screenDisplayUiIdCol = ScreenDisplayUiSetting::id;
        $this->screenDisplayUiIsShowCol = ScreenDisplayUiSetting::isShow;

        $this->coreFieldFilterModuleNameCol = CoreFieldFilterSetting::moduleName;

        $this->customUiIsDelCol = CustomizeUi::isDelete;

        $this->mobileSettingService = $mobileSettingService;
    }

    public function store($request)
    {

        DB::beginTransaction();
        try {
            $system_config = new SystemConfig();

            if (isset($request->lat) && !empty($request->lat))
                $system_config->lat = $request->lat;

            if (isset($request->lng) && !empty($request->lng))
                $system_config->lng = $request->lng;

            if (isset($request->is_approved_enable) && !empty($request->is_approved_enable))
                $system_config->is_approved_enable = $request->is_approved_enable;

            if (isset($request->is_sub_location) && !empty($request->is_sub_location))
                $system_config->is_sub_location = $request->is_sub_location;

            if (isset($request->is_thumb2x_3x_generate) && !empty($request->is_thumb2x_3x_generate))
                $system_config->is_thumb2x_3x_generate = $request->is_thumb2x_3x_generate;

            if (isset($request->is_sub_subscription) && !empty($request->is_sub_subscription))
                $system_config->is_sub_subscription = $request->is_sub_subscription;

            if (isset($request->is_paid_app) && !empty($request->is_paid_app))
                $system_config->is_paid_app = $request->is_paid_app;

            if (isset($request->is_block_user) && !empty($request->is_block_user))
                $system_config->is_block_user = $request->is_block_user;

            if (isset($request->max_img_upload_of_item) && !empty($request->max_img_upload_of_item))
                $system_config->max_img_upload_of_item = $request->max_img_upload_of_item;

            if (isset($request->ad_type) && !empty($request->ad_type))
                $system_config->ad_type = $request->ad_type;

            if (isset($request->promo_cell_interval_no) && !empty($request->promo_cell_interval_no))
                $system_config->promo_cell_interval_no = $request->promo_cell_interval_no;

            if (isset($request->one_day_per_price) && !empty($request->one_day_per_price))
                $system_config->one_day_per_price = $request->one_day_per_price;
            $system_config->added_user_id = Auth::user()->id;
            $system_config->save();

            DB::commit();
            return $system_config;
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public function update($id, $request)
    {
        DB::beginTransaction();
        try {
        $system_config = $this->getSystemConfig($id);
        if (isset($request->lat) && !empty($request->lat)) {
            $system_config->lat = $request->lat;
        }

        if (isset($request->lng) && !empty($request->lng)) {
            $system_config->lng = $request->lng;
        }

        if (isset($request->is_approved_enable)) {
            $system_config->is_approved_enable = $request->is_approved_enable;
        }

        if (isset($request->is_sub_location)) {
            $system_config->is_sub_location = $request->is_sub_location;
        }

        if (isset($request->is_thumb2x_3x_generate)) {
            $system_config->is_thumb2x_3x_generate = $request->is_thumb2x_3x_generate;
        }

        if (isset($request->is_sub_subscription)) {
            $system_config->is_sub_subscription = $request->is_sub_subscription;
        }

        if (isset($request->is_paid_app)) {
            $system_config->is_paid_app = $request->is_paid_app;
        }

        if (isset($request->is_block_user)) {
            $system_config->is_block_user = $request->is_block_user;
        }

        if (isset($request->max_img_upload_of_item)) {
            $system_config->max_img_upload_of_item = $request->max_img_upload_of_item;
        }

        if (isset($request->ad_type)) {
            $system_config->ad_type = $request->ad_type;
        }

        if (isset($request->promo_cell_interval_no)) {
            $system_config->promo_cell_interval_no = $request->promo_cell_interval_no;
        }

        if (isset($request->one_day_per_price)) {
            $system_config->one_day_per_price = $request->one_day_per_price;
        }

        if (isset($request->selected_price_type)) {
            $selected_price_data= array(
                'id' => $request->selected_price_type
            );
        }

        if(isset($request->selected_chat_type)){
            $selected_chat_data= array(
                'id' => $request->selected_chat_type
            );
        }
        $system_setting = $this->getJsonSystemConfig();        
        $selected_setting = json_decode($system_setting->setting, true); 
        $selected_setting['selected_price_type'] = $selected_price_data;
        $selected_setting['selected_chat_data'] = $selected_chat_data;

        $system_setting->setting = $selected_setting;
        $system_setting->save();





        $system_config->updated_user_id = Auth::user()->id;
        $system_config->update();

            DB::commit();
            return $system_config;
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public function getSystemConfig($id = null)
    {
        $mobileSetting = SystemConfig::when($id, function ($q, $id) {
            $q->where('id', $id);
        })
            ->first();
        return $mobileSetting;
    }

    public function getAdTypes()
    {
        return AdPostType::all();
    }

    public function getJsonSystemConfig() {

        return Setting::where('setting_env', Constants::SYSTEM_CONFIG)->first();

    }

    public function getSystemSettingJson(){

        $setting = Setting::where('setting_env', Constants::SYSTEM_CONFIG)->first();
        return json_decode($setting->setting, true);

    }

    public function getSystemRefSelectionJson(){

        $setting = Setting::where('setting_env', Constants::SYSTEM_CONFIG)->first();
        return json_decode($setting->ref_selection, true);

    }

    public function index()
    {
        // check permission
        $checkPermission = $this->checkPermission($this->viewAnyAbility, SystemConfig::class, "admin.index");

        $system_config = $this->getSystemConfig();

        if ($system_config) {
            $system_config = new SystemConfigWithKeyResource($system_config);
        }



        $adTypes = $this->getAdTypes();

        //for mobile setting
        $mbCoreFieldFilterSettings = $this->getCoreFieldFilteredLists(Constants::mobileSetting);
        $mobile_setting = $this->mobileSettingService->getMobileSetting();
        if ($mobile_setting) {
            $mobile_setting = new MobileSettingWithKeyResource($mobile_setting);
        }

        // taking for column and columnFilterOption
        $columnAndColumnFilter = $this->takingForColumnAndFilterOption();
        $showSystemConfigCols = $columnAndColumnFilter['showCoreField'];
        $columnProps = $columnAndColumnFilter['arrForColumnProps'];
        $columnFilterOptionProps = $columnAndColumnFilter['arrForColumnFilterProps'];
        
        $dataArr = [
            'checkPermission' => $checkPermission,
            'system_config' => $system_config,
            'showSystemConfigCols' => $showSystemConfigCols,
            'adTypes' => $adTypes,
            'showCoreAndCustomFieldArr' => $columnProps,
            'hideShowFieldForFilterArr' => $columnFilterOptionProps,
            'mbCoreFieldFilterSettings' => $mbCoreFieldFilterSettings,
            'mobile_setting' => $mobile_setting
        ];
        return $dataArr;
    }

    public function getCoreFieldFilteredLists($code)
    {
        return CoreFieldFilterSetting::where($this->coreFieldFilterModuleNameCol, $code)->get();
    }

    public function takingForColumnProps($label, $field, $type, $isShowSorting, $ordering)
    {
        $hideShowCoreAndCustomFieldObj = new \stdClass();
        $hideShowCoreAndCustomFieldObj->label = $label;
        $hideShowCoreAndCustomFieldObj->field = $field;
        $hideShowCoreAndCustomFieldObj->type = $type;
        $hideShowCoreAndCustomFieldObj->sort = $isShowSorting;
        $hideShowCoreAndCustomFieldObj->ordering = $ordering;
        if ($type !== "Image" && $type !== "MultiSelect" && $type !== "Action") {
            $hideShowCoreAndCustomFieldObj->action = 'Action';
        }

        return $hideShowCoreAndCustomFieldObj;
    }

    public function takingForColumnAndFilterOption()
    {

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

        foreach ($hideShowForCoreAndCustomFields as $showFields) {
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
            }
            if ($showFields->customizeField !== null) {

                $label = $showFields->customizeField->name;
                $uiHaveAttribute = [$this->dropDownUi, $this->radioUi];
                if (in_array($showFields->customizeField->ui_type_id, $uiHaveAttribute)) {
                    $field = $showFields->customizeField->core_keys_id . "@@name";
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

    public function hiddenShownForCoreAndCustomField($isShown, $code)
    {
        $hiddenShownForFields = ScreenDisplayUiSetting::with(['customizeField' => function ($q) {
            $q->where($this->customUiIsDelCol, $this->unDelete);
        }, 'coreField' => function ($q) {
            $q->where($this->coreFieldFilterModuleNameCol, $this->code);
        }])
            ->where($this->coreFieldFilterModuleNameCol, $code)
            ->where($this->screenDisplayUiIsShowCol, $isShown)
            ->get();
        return $hiddenShownForFields;
    }

    public function takingForColumnFilterProps($id, $label, $field, $hidden, $moduleName, $keyId)
    {
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
