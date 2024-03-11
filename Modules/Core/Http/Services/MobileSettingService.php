<?php
namespace Modules\Core\Http\Services;

use App\Http\Services\PsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\MobileSetting;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\CoreFieldFilterSetting;
use Modules\Core\Entities\CustomizeUi;
use Modules\Core\Entities\ScreenDisplayUiSetting;
use Modules\Core\Transformers\Backend\Model\Color\ColorWithKeyResource;
use Modules\Core\Transformers\Backend\Model\MobileSetting\MobileSettingWithKeyResource;
use stdClass;
use Modules\Core\Entities\Module;
use Modules\Core\Entities\CoreMenu;

class MobileSettingService extends PsService
{
    protected $colorService, $mobileLanguageService, $enable, $show, $hide, $delete, $unDelete, $viewAnyAbility, $createAbility, $editAbility, $deleteAbility, $code, $screenDisplayUiKeyCol, $screenDisplayUiIdCol, $screenDisplayUiIsShowCol, $coreFieldFilterModuleNameCol, $customUiIsDelCol;

    public function __construct(MobileLanguageService $mobileLanguageService, ColorService $colorService)
    {
        $this->mobileLanguageService = $mobileLanguageService;

        $this->enable = Constants::enable;
        $this->show = Constants::show;
        $this->hide = Constants::hide;
        $this->delete = Constants::delete;
        $this->unDelete = Constants::unDelete;

        $this->viewAnyAbility = Constants::viewAnyAbility;
        $this->createAbility = Constants::createAbility;
        $this->editAbility = Constants::editAbility;
        $this->deleteAbility = Constants::deleteAbility;
        $this->code = Constants::mobileSetting;

        $this->screenDisplayUiKeyCol = ScreenDisplayUiSetting::key;
        $this->screenDisplayUiIdCol = ScreenDisplayUiSetting::id;
        $this->screenDisplayUiIsShowCol = ScreenDisplayUiSetting::isShow;

        $this->coreFieldFilterModuleNameCol = CoreFieldFilterSetting::moduleName;

        $this->customUiIsDelCol = CustomizeUi::isDelete;

        $this->colorService = $colorService;
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {

            $mobile_setting = new MobileSetting();
            $mobile_setting->apple_appstore_url = $request->apple_appstore_url;
            $mobile_setting->ios_appstore_id = $request->ios_appstore_id;
            $mobile_setting->google_playstore_url = $request->google_playstore_url;
            $mobile_setting->is_show_admob = $request->is_show_admob;
            $mobile_setting->fb_key = $request->fb_key;
            $mobile_setting->date_format = $request->date_format;
            $mobile_setting->price_format = $request->price_format;
            $mobile_setting->default_razor_currency = $request->default_razor_currency;
            $mobile_setting->is_razor_support_multi_currency = $request->is_razor_support_multi_currency;
            $mobile_setting->is_show_subcategory = $request->is_show_subcategory;
            $mobile_setting->is_show_discount = $request->is_show_discount;
            $mobile_setting->show_phone_login = $request->show_phone_login;
            $mobile_setting->show_google_login = $request->show_google_login;
            $mobile_setting->show_apple_login = $request->show_apple_login;
            $mobile_setting->show_facebook_login = $request->show_facebook_login;
            $mobile_setting->is_use_google_map = $request->is_use_google_map;
            $mobile_setting->is_show_item_video = $request->is_show_item_video;
            $mobile_setting->item_detail_view_count_for_ads = $request->item_detail_view_count_for_ads;
            $mobile_setting->is_show_ads_in_item_detail = $request->is_show_ads_in_item_detail;
            $mobile_setting->after_item_count_admob_once = $request->after_item_count_admob_once;
            //$mobile_setting->is_show_token_id = $request->is_show_token_id;
            $mobile_setting->is_use_thumbnail_as_placeholder = $request->is_use_thumbnail_as_placeholder;
            $mobile_setting->blue_mark_size = $request->blue_mark_size;
            $mobile_setting->block_item_loading_limit = $request->block_item_loading_limit;
            $mobile_setting->follower_item_loading_limit = $request->follower_item_loading_limit;
            $mobile_setting->block_slider_loading_limit = $request->block_slider_loading_limit;
            $mobile_setting->featured_item_loading_limit = $request->featured_item_loading_limit;
            $mobile_setting->popular_item_loading_limit = $request->popular_item_loading_limit;
            $mobile_setting->recent_item_loading_limit = $request->recent_item_loading_limit;
            $mobile_setting->category_loading_limit = $request->category_loading_limit;
            $mobile_setting->default_loading_limit = $request->default_loading_limit;
            $mobile_setting->discount_item_loading_limit = $request->discount_item_loading_limit;
            $mobile_setting->mile = $request->mile;
            $mobile_setting->video_duration = $request->video_duration;
            $mobile_setting->is_show_owner_info = $request->is_show_owner_info;
            $mobile_setting->is_force_login = $request->is_force_login;
            $mobile_setting->is_language_config = $request->is_language_config;
            $mobile_setting->no_filter_with_location_on_map = $request->no_filter_with_location_on_map;
            $mobile_setting->chat_image_size = $request->chat_image_size;
            $mobile_setting->profile_image_size = $request->profile_image_size;
            $mobile_setting->upload_image_size = $request->upload_image_size;
            $mobile_setting->promote_first_choice_day = $request->promote_first_choice_day;
            $mobile_setting->promote_second_choice_day = $request->promote_second_choice_day;
            $mobile_setting->promote_third_choice_day = $request->promote_third_choice_day;
            $mobile_setting->promote_fourth_choice_day = $request->promote_fourth_choice_day;
            $mobile_setting->lat = $request->lat;
            $mobile_setting->lng = $request->lng;
            $mobile_setting->collection_item_loading_limit = $request->collection_item_loading_limit;
            $mobile_setting->shop_loading_limit = $request->shop_loading_limit;
            $mobile_setting->show_main_menu = $request->show_main_menu;
            $mobile_setting->show_special_collections = $request->show_special_collections;
            $mobile_setting->show_featured_items = $request->show_featured_items;
            $mobile_setting->show_best_choice_slider = $request->show_best_choice_slider;
            $mobile_setting->default_flutter_wave_currency = $request->default_flutter_wave_currency;
            $mobile_setting->default_order_time = $request->default_order_time;
            $mobile_setting->trending_item_loading_limit = $request->trending_item_loading_limit;
            $mobile_setting->version_no = $request->version_no;
            $mobile_setting->version_force_update = $request->version_force_update;
            $mobile_setting->version_title = $request->version_title;
            $mobile_setting->version_message = $request->version_message;
            $mobile_setting->version_need_clear_data = $request->version_need_clear_data;
            $mobile_setting->deli_boy_version_no = $request->deli_boy_version_no;
            $mobile_setting->deli_boy_version_force_update = $request->deli_boy_version_force_update;
            $mobile_setting->deli_boy_version_title = $request->deli_boy_version_title;
            $mobile_setting->deli_boy_version_message = $request->deli_boy_version_message;
            $mobile_setting->deli_boy_version_need_clear_data = $request->deli_boy_version_need_clear_data;
            $mobile_setting->color_change_code = Carbon::now()->getPreciseTimestamp(3);
            $mobile_setting->added_user_id = Auth::user()->id;

            $mobile_setting->save();

            DB::commit();
        } catch (\Throwable $e){
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }

        return $mobile_setting;
    }

    public function update($id, $request)
    {
        if(isset($request->mobileColors)){
            foreach(json_decode($request->mobileColors) as $mobileColor){
                $mobile_color = [
                    "id" => $mobileColor->id,
                    "key" => $mobileColor->key,
                    "value" => $mobileColor->value,
                    "title" => $mobileColor->title,
                    "fe_color" => $mobileColor->fe_color,
                    "mb_color" => $mobileColor->mb_color,
                ];
                $this->colorService->update($mobile_color);
            }
        }
        DB::beginTransaction();
        try {
            $mobile_setting = $this->getMobileSetting($id);
            $mobile_setting->google_playstore_url = $request->google_playstore_url;
            $mobile_setting->apple_appstore_url = $request->apple_appstore_url;
            $mobile_setting->ios_appstore_id = $request->ios_appstore_id;

            $mobile_setting->promote_first_choice_day = $request->promote_first_choice_day;
            $mobile_setting->promote_second_choice_day = $request->promote_second_choice_day;
            $mobile_setting->promote_third_choice_day = $request->promote_third_choice_day;
            $mobile_setting->promote_fourth_choice_day = $request->promote_fourth_choice_day;

            $mobile_setting->profile_image_size = $request->profile_image_size;
            $mobile_setting->upload_image_size = $request->upload_image_size;
            $mobile_setting->chat_image_size = $request->chat_image_size;
            $mobile_setting->blue_mark_size = $request->blue_mark_size;

            $mobile_setting->item_detail_view_count_for_ads = $request->item_detail_view_count_for_ads;
            $mobile_setting->is_show_admob = $request->is_show_admob;
            $mobile_setting->is_show_ads_in_item_detail = $request->is_show_ads_in_item_detail;
            $mobile_setting->android_admob_banner_ad_unit_id = $request->android_admob_banner_ad_unit_id; //new
            $mobile_setting->android_admob_native_unit_id = $request->android_admob_native_unit_id; //new
            $mobile_setting->andorid_admob_interstitial_ad_unit_id = $request->andorid_admob_interstitial_ad_unit_id; //new
            $mobile_setting->ios_admob_banner_ad_unit_id = $request->ios_admob_banner_ad_unit_id; //new
            $mobile_setting->ios_admob_native_ad_unit_id = $request->ios_admob_native_ad_unit_id; //new
            $mobile_setting->ios_admob_interstitial_ad_unit_id = $request->ios_admob_interstitial_ad_unit_id; //new

            $mobile_setting->show_facebook_login = $request->show_facebook_login;
            $mobile_setting->show_phone_login = $request->show_phone_login;
            $mobile_setting->show_google_login = $request->show_google_login;
            $mobile_setting->show_apple_login = $request->show_apple_login;

            $mobile_setting->show_main_menu = $request->show_main_menu;
            $mobile_setting->show_featured_items = $request->show_featured_items;

            $mobile_setting->default_loading_limit = $request->default_loading_limit;
            $mobile_setting->category_loading_limit = $request->category_loading_limit;
            $mobile_setting->recent_item_loading_limit = $request->recent_item_loading_limit;
            $mobile_setting->popular_item_loading_limit = $request->popular_item_loading_limit;
            $mobile_setting->discount_item_loading_limit = $request->discount_item_loading_limit;
            $mobile_setting->featured_item_loading_limit = $request->featured_item_loading_limit;
            $mobile_setting->block_slider_loading_limit = $request->block_slider_loading_limit;
            $mobile_setting->follower_item_loading_limit = $request->follower_item_loading_limit;
            $mobile_setting->block_item_loading_limit = $request->block_item_loading_limit;
            $mobile_setting->recent_search_keyword_limit = $request->recent_search_keyword_limit; //new

            $mobile_setting->version_no = $request->version_no;
            $mobile_setting->version_title = $request->version_title;
            $mobile_setting->version_message = $request->version_message;
            $mobile_setting->version_force_update = $request->version_force_update;
            $mobile_setting->version_need_clear_data = $request->version_need_clear_data;

            $mobile_setting->data_config_data_source_type = $request->data_config_data_source_type; //new
            $mobile_setting->data_config_day = $request->data_config_day; //new

            $mobile_setting->is_slider_auto_play = $request->is_slider_auto_play; //new
            $mobile_setting->auto_play_interval = $request->auto_play_interval; //new

            $mobile_setting->fb_key = $request->fb_key;
            $mobile_setting->price_format = $request->price_format;
            $mobile_setting->date_format = $request->date_format;
            $mobile_setting->mile = $request->mile;
            $mobile_setting->is_use_google_map = $request->is_use_google_map;
            $mobile_setting->is_show_subcategory = $request->is_show_subcategory;
            $mobile_setting->is_show_discount = $request->is_show_discount;
            $mobile_setting->is_use_thumbnail_as_placeholder = $request->is_use_thumbnail_as_placeholder;
            $mobile_setting->no_filter_with_location_on_map = $request->no_filter_with_location_on_map;
            $mobile_setting->is_show_owner_info = $request->is_show_owner_info;
            $mobile_setting->is_show_item_video = $request->is_show_item_video;
            $mobile_setting->is_demo_for_payment = $request->is_demo_for_payment; //new
            $mobile_setting->is_language_config = $request->is_language_config;
            $mobile_setting->loading_shimmer_item_count = $request->loading_shimmer_item_count; //new
            $mobile_setting->phone_list_count = $request->phone_list_count; //new
            $mobile_setting->video_duration = $request->video_duration;

            //$mobile_setting->is_show_token_id = $request->is_show_token_id;
            $mobile_setting->is_force_login = $request->is_force_login;
            $mobile_setting->is_razor_support_multi_currency = $request->is_razor_support_multi_currency;
            $mobile_setting->default_razor_currency = $request->default_razor_currency;
            $mobile_setting->after_item_count_admob_once = $request->after_item_count_admob_once;
            $mobile_setting->lat = $request->lat;
            $mobile_setting->lng = $request->lng;
            $mobile_setting->collection_item_loading_limit = $request->collection_item_loading_limit;
            $mobile_setting->shop_loading_limit = $request->shop_loading_limit;
            $mobile_setting->show_special_collections = $request->show_special_collections;
            $mobile_setting->show_best_choice_slider = $request->show_best_choice_slider;
            $mobile_setting->default_flutter_wave_currency = $request->default_flutter_wave_currency;
            $mobile_setting->default_order_time = $request->default_order_time;
            $mobile_setting->trending_item_loading_limit = $request->trending_item_loading_limit;
            $mobile_setting->deli_boy_version_no = $request->deli_boy_version_no;
            $mobile_setting->deli_boy_version_force_update = $request->deli_boy_version_force_update;
            $mobile_setting->deli_boy_version_title = $request->deli_boy_version_title;
            $mobile_setting->deli_boy_version_message = $request->deli_boy_version_message;
            $mobile_setting->deli_boy_version_need_clear_data = $request->deli_boy_version_need_clear_data;

            $mobile_setting->updated_user_id = Auth::user()->id;

            $mobile_setting->update();

            if($mobile_setting->is_show_subcategory == '1'){
                $subcategoryModule = Module::where('route_name','subcategory.index')->first();
                if($subcategoryModule && $subcategoryModule->status == '0'){
                    $subcategoryModule->status = '1';
                    $subcategoryModule->update();
                }
                if($subcategoryModule){
                    $subcategoryMenu = CoreMenu::where('module_id',$subcategoryModule->id)->first();
                    if($subcategoryMenu && $subcategoryMenu->is_show_on_menu == '0'){
                        $subcategoryMenu->is_show_on_menu = '1';
                        $subcategoryMenu->update();
                    }
                }

            }else{
                // dd('here');
                $subcategoryModule = Module::where('route_name','subcategory.index')->first();
                if($subcategoryModule && $subcategoryModule->status == '1'){
                    $subcategoryModule->status = '0';
                    $subcategoryModule->update();
                }
                if($subcategoryModule){
                    $subcategoryMenu = CoreMenu::where('module_id',$subcategoryModule->id)->first();
                    if($subcategoryMenu && $subcategoryMenu->is_show_on_menu == '1'){
                        $subcategoryMenu->is_show_on_menu = '0';
                        $subcategoryMenu->update();
                    }
                }
            }

            DB::commit();
        } catch(\Throwable $e){
            DB::rollBack();
            // dd($e->getMessage());
            return ['error' => $e->getMessage()];
        }

        return $mobile_setting;
    }

    public function getMobileSetting($id = null){
        $mobileSetting = MobileSetting::when($id, function ($q, $id){
            $q->where('id', $id);
        })
            ->first();
        return $mobileSetting;
    }

    public function index(){
        // check permission
        $checkPermission = $this->checkPermission($this->viewAnyAbility, MobileSetting::class, "admin.index");
        $code = $this->code;
        $mobile_setting = $this->getMobileSetting();
        if($mobile_setting){
            $mobile_setting = new MobileSettingWithKeyResource($mobile_setting);
        }

        $available_languages = $this->mobileLanguageService->getMobileLanguages($this->enable);
        $coreFieldFilterSettings = $this->getCoreFieldFilteredLists($code);

        $mobileColorsConds['mb_color'] = 1;
        $mobileColors = $this->colorService->getColors(null, null, $mobileColorsConds);

        // $lightColorConds['is_light_color'] = 1;
        // $lightColor = $this->colorService->getColors(null, null, $lightColorConds);

        // $darkColorConds['is_dark_color'] = 1;
        // $darkColor = $this->colorService->getColors(null, null, $darkColorConds);

        // $commonColorConds['is_common_color'] = 1;
        // $commonColor = $this->colorService->getColors(null, null, null);

        // taking for column and columnFilterOption
        $columnAndColumnFilter = $this->takingForColumnAndFilterOption();
        $showMobileSettingCols = $columnAndColumnFilter['showCoreField'];
        $columnProps = $columnAndColumnFilter['arrForColumnProps'];
        $columnFilterOptionProps = $columnAndColumnFilter['arrForColumnFilterProps'];

        $dataArr = [
            'checkPermission' => $checkPermission,
            'mobile_setting' => $mobile_setting,
            'available_languages' => $available_languages,
            'showMobileSettingCols' => $showMobileSettingCols,
            'showCoreAndCustomFieldArr' => $columnProps,
            'hideShowFieldForFilterArr' => $columnFilterOptionProps,
            'coreFieldFilterSettings'=>$coreFieldFilterSettings,
            'mobileColors' => $mobileColors,
        ];
        return $dataArr;
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

    public function getCoreFieldFilteredLists($code){
        return CoreFieldFilterSetting::where($this->coreFieldFilterModuleNameCol, $code)->get();
    }
}
