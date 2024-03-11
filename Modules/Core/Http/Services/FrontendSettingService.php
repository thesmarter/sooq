<?php
namespace Modules\Core\Http\Services;

use App\Http\Services\PsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Entities\CoreImage;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\CustomizeUi;
use Modules\Core\Entities\FrontendSetting;
use Modules\Core\Http\Services\ColorService;
use Modules\Core\Http\Services\ImageService;
use Modules\Core\Entities\CoreFieldFilterSetting;
use Modules\Core\Entities\ScreenDisplayUiSetting;
use Modules\Core\Transformers\Backend\Model\FrontendSetting\FrontendSettingWithKeyResource;
use Carbon\Carbon;

class FrontendSettingService extends PsService
{
    protected $available_languages = [
        // ['id'=>'1','language_code'=> 'en', 'country_code' => 'US', 'name' => 'English'],
        // ['id'=>'2','language_code'=> 'ar', 'country_code' => 'DZ', 'name' => 'Arabic'],
        // ['id'=>'3','language_code'=> 'hi', 'country_code' => 'IN', 'name' => 'Hindi'],
        // ['id'=>'4','language_code'=> 'de', 'country_code' => 'DE', 'name' => 'German'],
        // ['id'=>'5','language_code'=> 'es', 'country_code' => 'ES', 'name' => 'Spainish'],
        // ['id'=>'6','language_code'=> 'fr', 'country_code' => 'FR', 'name' => 'French'],
        // ['id'=>'7','language_code'=> 'id', 'country_code' => 'ID', 'name' => 'Indonesian'],
        // ['id'=>'8','language_code'=> 'it', 'country_code' => 'IT', 'name' => 'Italian'],
        // ['id'=>'9','language_code'=> 'ja', 'country_code' => 'JP', 'name' => 'Japanese'],
        // ['id'=>'10','language_code'=> 'ko', 'country_code' => 'KR', 'name' => 'Korean'],
        // ['id'=>'11','language_code'=> 'ms', 'country_code' => 'MY', 'name' => 'Malay'],
        // ['id'=>'12','language_code'=> 'pt', 'country_code' => 'PT', 'name' => 'Portuguese'],
        // ['id'=>'13','language_code'=> 'ru', 'country_code' => 'RU', 'name' => 'Russian'],
        // ['id'=>'14','language_code'=> 'th', 'country_code' => 'TH', 'name' => 'Thai'],
        // ['id'=>'15','language_code'=> 'tr', 'country_code' => 'TR', 'name' => 'Turkish'],
        // ['id'=>'16','language_code'=> 'zh', 'country_code' => 'CN', 'name' => 'Chinese'],
        ['language_code'=> 'en', 'country_code' => 'US', 'name' => 'English'],
        ['language_code'=> 'ar', 'country_code' => 'DZ', 'name' => 'Arabic'],
        ['language_code'=> 'hi', 'country_code' => 'IN', 'name' => 'Hindi'],
        ['language_code'=> 'de', 'country_code' => 'DE', 'name' => 'German'],
        ['language_code'=> 'es', 'country_code' => 'ES', 'name' => 'Spainish'],
        ['language_code'=> 'fr', 'country_code' => 'FR', 'name' => 'French'],
        ['language_code'=> 'id', 'country_code' => 'ID', 'name' => 'Indonesian'],
        ['language_code'=> 'it', 'country_code' => 'IT', 'name' => 'Italian'],
        ['language_code'=> 'ja', 'country_code' => 'JP', 'name' => 'Japanese'],
        ['language_code'=> 'ko', 'country_code' => 'KR', 'name' => 'Korean'],
        ['language_code'=> 'ms', 'country_code' => 'MY', 'name' => 'Malay'],
        ['language_code'=> 'pt', 'country_code' => 'PT', 'name' => 'Portuguese'],
        ['language_code'=> 'ru', 'country_code' => 'RU', 'name' => 'Russian'],
        ['language_code'=> 'th', 'country_code' => 'TH', 'name' => 'Thai'],
        ['language_code'=> 'tr', 'country_code' => 'TR', 'name' => 'Turkish'],
        ['language_code'=> 'zh', 'country_code' => 'CN', 'name' => 'Chinese'],
    ];

    protected $colorService, $imageService,$show, $hide, $delete, $unDelete, $viewAnyAbility, $createAbility, $editAbility, $deleteAbility, $code, $screenDisplayUiKeyCol, $screenDisplayUiIdCol, $screenDisplayUiIsShowCol, $coreFieldFilterModuleNameCol, $customUiIsDelCol, $appBrandingImgType;

    public function __construct(ImageService $imageService, ColorService $colorService)
    {
        $this->show = Constants::show;
        $this->hide = Constants::hide;
        $this->delete = Constants::delete;
        $this->unDelete = Constants::unDelete;

        $this->imageService = $imageService;
        $this->coreImageImgParentIdCol = CoreImage::imgParentId;
        $this->coreImageImgTypeCol = CoreImage::imgType;
        $this->logoImgType = 'frontend-logo';
        $this->iconImgType = 'frontend-icon';
        $this->bannerImgType = 'frontend-banner';
        $this->metaImgType = 'backend-meta-image';
        $this->appBrandingImgType = 'app-branding-image';


        $this->viewAnyAbility = Constants::viewAnyAbility;
        $this->createAbility = Constants::createAbility;
        $this->editAbility = Constants::editAbility;
        $this->deleteAbility = Constants::deleteAbility;
        $this->code = Constants::frontendSetting;

        $this->screenDisplayUiKeyCol = ScreenDisplayUiSetting::key;
        $this->screenDisplayUiIdCol = ScreenDisplayUiSetting::id;
        $this->screenDisplayUiIsShowCol = ScreenDisplayUiSetting::isShow;

        $this->coreFieldFilterModuleNameCol = CoreFieldFilterSetting::moduleName;

        $this->customUiIsDelCol = CustomizeUi::isDelete;

        $this->frontendSettingIdCol = FrontendSetting::id;

        $this->colorService = $colorService;
    }
    public function store($request)
    {
        DB::beginTransaction();
        try {
            $frontend_setting = new FrontendSetting();
            $frontend_setting->map_key = $request->map_key;
            $frontend_setting->google_playstore_url = $request->google_playstore_url;
            $frontend_setting->app_store_url = $request->app_store_url;
            $frontend_setting->gps_enable = $request->gps_enable;
            $frontend_setting->show_main_menu = $request->show_main_menu;
            $frontend_setting->show_special_collections = $request->show_special_collections;
            $frontend_setting->show_featured_items = $request->show_featured_items;
            $frontend_setting->show_best_choice_slider = $request->show_best_choice_slider;
            $frontend_setting->fcm_server_key = $request->fcm_server_key;
            $frontend_setting->firebase_web_push_key_pair = $request->firebase_web_push_key_pair;
            $frontend_setting->ad_client = $request->ad_client;
            $frontend_setting->ad_slot = $request->ad_slot;
            $frontend_setting->is_ads_on = $request->is_ads_on;
            $frontend_setting->firebase_config = $request->firebase_config;
            $frontend_setting->copyright = $request->copyright;
            $frontend_setting->price_format = $request->price_format;
            $frontend_setting->banner_src = $request->banner_src;
            $frontend_setting->mile = $request->mile;
            $frontend_setting->is_enable_video_setting = $request->is_enable_video_setting;
            $frontend_setting->show_user_profile = $request->show_user_profile;
            $frontend_setting->no_filter_with_location_on_map = $request->no_filter_with_location_on_map;
            $frontend_setting->enable_notification = $request->enable_notification;
            $frontend_setting->google_setting = $request->google_setting;
            $frontend_setting->app_store_setting = $request->app_store_setting;
            $frontend_setting->google_map = $request->google_map;
            $frontend_setting->open_street_map = $request->open_street_map;
            $frontend_setting->default_language = $request->default_language;
            // $frontend_setting->selected_language = implode(",",$request->selected_language);
            $frontend_setting->promote_first_choice_day = $request->promote_first_choice_day;
            $frontend_setting->promote_second_choice_day = $request->promote_second_choice_day;
            $frontend_setting->promote_third_choice_day = $request->promote_third_choice_day;
            $frontend_setting->promote_fourth_choice_day = $request->promote_fourth_choice_day;
            $frontend_setting->frontend_version_no = $request->frontend_version_no;
            $frontend_setting->frontend_meta_title = $request->frontend_meta_title;
            $frontend_setting->frontend_meta_description = $request->frontend_meta_description;
            $frontend_setting->added_user_id = Auth::user()->id;
            $frontend_setting->save();

            DB::commit();

            // update frontend-logo
            if ($request->file('frontend_logo')){

                $file = $request->file('frontend_logo');

                $conds[$this->coreImageImgParentIdCol] = $frontend_setting->id;
                $conds[$this->coreImageImgTypeCol] = $this->logoImgType;
                $image = CoreImage::where($conds)->first();

                // if image, delete existing file
                if(!empty($image)){
                    // delete image from storage folder
                    $this->imageService->deleteImage($image->img_path);
                }

                $data[$this->coreImageImgParentIdCol] = $frontend_setting->id;
                $data[$this->coreImageImgTypeCol] = $this->logoImgType;
                $this->imageService->update(null,null,$file, $data, $image);
            }

             // update frontend-icon
             if ($request->file('frontend_icon')){

                $file = $request->file('frontend_icon');

                $conds[$this->coreImageImgParentIdCol] = $frontend_setting->id;
                $conds[$this->coreImageImgTypeCol] = $this->iconImgType;
                $image = CoreImage::where($conds)->first();

                // if image, delete existing file
                if(!empty($image)){
                    // delete image from storage folder
                    $this->imageService->deleteImage($image->img_path);
                }

                $data[$this->coreImageImgParentIdCol] = $frontend_setting->id;
                $data[$this->coreImageImgTypeCol] = $this->iconImgType;
                $this->imageService->update(null,null,$file, $data, $image);
            }

            // update frontend-banner
            if ($request->file('frontend_banner')){

                $file = $request->file('frontend_banner');

                $conds[$this->coreImageImgParentIdCol] = $frontend_setting->id;
                $conds[$this->coreImageImgTypeCol] = $this->bannerImgType;
                $image = CoreImage::where($conds)->first();

                // if image, delete existing file
                if(!empty($image)){
                    // delete image from storage folder
                    $this->imageService->deleteImage($image->img_path);
                }

                $data[$this->coreImageImgParentIdCol] = $frontend_setting->id;
                $data[$this->coreImageImgTypeCol] = $this->bannerImgType;
                $this->imageService->update(null,null,$file, $data, $image);
            }

            // update meta image
            if($request->file('frontend_meta_img')){

                $file = $request->file('frontend_meta_img');

                $conds[$this->coreImageImgParentIdCol] = $frontend_setting->id;
                $conds[$this->coreImageImgTypeCol] = $this->metaImgType;
                $image = CoreImage::where($conds)->first();

                // if image, delete existing file
                if(!empty($image)){
                    // delete image from storage folder
                    $this->imageService->deleteImage($image->img_path);
                }


                $data[$this->coreImageImgParentIdCol] = $frontend_setting->id;
                $data[$this->coreImageImgTypeCol] = $this->metaImgType;
                $this->imageService->update(null,null,$file,$data,$image);
            }

        } catch (\Throwable $e){
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }

        return $frontend_setting;
    }

    public function generateColors($request){

        if(isset($request->frontendColors)){
            foreach(json_decode($request->frontendColors) as $frontendColor){
                $frontend_color = [
                    "id" => $frontendColor->id,
                    "key" => $frontendColor->key,
                    "value" => $frontendColor->value,
                    "title" => $frontendColor->title,
                    "fe_color" => $frontendColor->fe_color,
                    "mb_color" => $frontendColor->mb_color,
                ];
                $this->colorService->update($frontend_color);
            }
        }

        $frontend_setting = FrontendSetting::first();
        $oldFilename = 'css/custom_color_'.$frontend_setting->color_changed_code.'.css';


        $frontend_setting->color_changed_code  = Carbon::now()->getPreciseTimestamp(3);
        $frontend_setting->updated_user_id = Auth::user()->id;
        $frontend_setting->update();

        syncFrontendColors();
    }

    public function update($id, $request)
    {
        if(isset($request->frontendColors)){
            foreach(json_decode($request->frontendColors) as $frontendColor){
                $frontend_color = [
                    "id" => $frontendColor->id,
                    "key" => $frontendColor->key,
                    "value" => $frontendColor->value,
                    "title" => $frontendColor->title,
                    "fe_color" => $frontendColor->fe_color,
                    "mb_color" => $frontendColor->mb_color,
                ];
                $this->colorService->update($frontend_color);
            }
        }
            $frontend_setting = $this->getFrontendSetting($id);
            $frontend_setting->map_key = $request->map_key;
            $frontend_setting->google_playstore_url = $request->google_playstore_url;
            $frontend_setting->app_store_url = $request->app_store_url;
            $frontend_setting->gps_enable = $request->gps_enable;
            $frontend_setting->show_main_menu = $request->show_main_menu;
            $frontend_setting->show_special_collections = $request->show_special_collections;
            $frontend_setting->show_featured_items = $request->show_featured_items;
            $frontend_setting->show_best_choice_slider = $request->show_best_choice_slider;
            $frontend_setting->fcm_server_key = $request->fcm_server_key;
            $frontend_setting->firebase_web_push_key_pair = $request->firebase_web_push_key_pair;
            $frontend_setting->ad_client = $request->ad_client;
            $frontend_setting->ad_slot = $request->ad_slot;
            $frontend_setting->is_ads_on = $request->is_ads_on;
            $frontend_setting->firebase_config = $request->firebase_config;
            $frontend_setting->copyright = $request->copyright;
            $frontend_setting->price_format = $request->price_format;
            $frontend_setting->banner_src = $request->banner_src;
            $frontend_setting->mile = $request->mile;
            $frontend_setting->is_enable_video_setting = $request->is_enable_video_setting;
            $frontend_setting->show_user_profile = $request->show_user_profile;
            $frontend_setting->no_filter_with_location_on_map = $request->no_filter_with_location_on_map;
            $frontend_setting->enable_notification = $request->enable_notification;
            $frontend_setting->google_setting = $request->google_setting;
            $frontend_setting->app_store_setting = $request->app_store_setting;
            $frontend_setting->google_map = $request->google_map;
            $frontend_setting->open_street_map = $request->open_street_map;
            $frontend_setting->default_language = $request->default_language;
            // $frontend_setting->selected_language = implode(",",$request->selected_language);
            // $frontend_setting->selected_language = json_encode($request->selected_language);
            $frontend_setting->promote_first_choice_day = $request->promote_first_choice_day;
            $frontend_setting->promote_second_choice_day = $request->promote_second_choice_day;
            $frontend_setting->promote_third_choice_day = $request->promote_third_choice_day;
            $frontend_setting->promote_fourth_choice_day = $request->promote_fourth_choice_day;
            $frontend_setting->frontend_version_no = $request->frontend_version_no;
            $frontend_setting->frontend_meta_title = $request->frontend_meta_title;
            $frontend_setting->frontend_meta_description = $request->frontend_meta_description;
            $frontend_setting->updated_user_id = Auth::user()->id;
            $frontend_setting->update();

             // update frontend-logo
             if ($request->file('frontend_logo')){

                $file = $request->file('frontend_logo');

                $conds[$this->coreImageImgParentIdCol] = $frontend_setting->id;
                $conds[$this->coreImageImgTypeCol] = $this->logoImgType;
                $image = CoreImage::where($conds)->first();

                // if image, delete existing file
                if(!empty($image)){
                    // delete image from storage folder
                    $this->imageService->deleteImage($image->img_path);
                }

                $data[$this->coreImageImgParentIdCol] = $frontend_setting->id;
                $data[$this->coreImageImgTypeCol] = $this->logoImgType;
                $this->imageService->update(null,null,$file, $data, $image);
            }

             // update frontend-icon
             if ($request->file('frontend_icon')){

                $file = $request->file('frontend_icon');

                $conds[$this->coreImageImgParentIdCol] = $frontend_setting->id;
                $conds[$this->coreImageImgTypeCol] = $this->iconImgType;
                $image = CoreImage::where($conds)->first();

                // if image, delete existing file
                if(!empty($image)){
                    // delete image from storage folder
                    $this->imageService->deleteImage($image->img_path);
                }

                $data[$this->coreImageImgParentIdCol] = $frontend_setting->id;
                $data[$this->coreImageImgTypeCol] = $this->iconImgType;
                $this->imageService->update(null,null,$file, $data, $image);
            }

            // update frontend-banner
            if ($request->file('frontend_banner')){

                $file = $request->file('frontend_banner');

                $conds[$this->coreImageImgParentIdCol] = $frontend_setting->id;
                $conds[$this->coreImageImgTypeCol] = $this->bannerImgType;
                $image = CoreImage::where($conds)->first();

                // if image, delete existing file
                if(!empty($image)){
                    // delete image from storage folder
                    $this->imageService->deleteImage($image->img_path);
                }

                $data[$this->coreImageImgParentIdCol] = $frontend_setting->id;
                $data[$this->coreImageImgTypeCol] = $this->bannerImgType;
                $this->imageService->update(null,null,$file, $data, $image,'frontend_banner');
            }

             // update meta image
             if($request->file('frontend_meta_image')){

                $file = $request->file('frontend_meta_image');

                $conds[$this->coreImageImgParentIdCol] = $frontend_setting->id;
                $conds[$this->coreImageImgTypeCol] = $this->metaImgType;
                $image = CoreImage::where($conds)->first();

                // if image, delete existing file
                if(!empty($image)){
                    // delete image from storage folder
                    $this->imageService->deleteImage($image->img_path);
                }


                $data[$this->coreImageImgParentIdCol] = $frontend_setting->id;
                $data[$this->coreImageImgTypeCol] = $this->metaImgType;
                $this->imageService->update(null,null,$file,$data,$image);
            }

            // update footer image
            if($request->file('app_branding_image')){

                $file = $request->file('app_branding_image');

                $conds[$this->coreImageImgParentIdCol] = $frontend_setting->id;
                $conds[$this->coreImageImgTypeCol] = $this->appBrandingImgType;
                $image = CoreImage::where($conds)->first();

                // if image, delete existing file
                if(!empty($image)){
                    // delete image from storage folder
                    $this->imageService->deleteImage($image->img_path);
                }


                $data[$this->coreImageImgParentIdCol] = $frontend_setting->id;
                $data[$this->coreImageImgTypeCol] = $this->appBrandingImgType;
                $this->imageService->update(null,null,$file,$data,$image);
            }


        return $frontend_setting;
    }

    public function getFrontendSetting($id = null){
        $frontendSetting = FrontendSetting::with(['frontend_logo','frontend_banner','frontend_icon','frontend_meta_image','app_branding_image'])
                            ->when($id, function ($q, $id){
                                                $q->where($this->frontendSettingIdCol, $id);
                                            })
                                            ->first();
        return $frontendSetting;
    }

    public function index(){
        // check permission
        $checkPermission = $this->checkPermission($this->viewAnyAbility, FrontendSetting::class, "admin.index");

        $code = $this->code;
        $coreFieldFilterSettings = $this->getCoreFieldFilteredLists($code);
        // check permission
        $frontend_setting = $this->getFrontendSetting();

        // taking for column and columnFilterOption
        $columnAndColumnFilter = $this->takingForColumnAndFilterOption();
        $showMobileSettingCols = $columnAndColumnFilter['showCoreField'];
        $columnProps = $columnAndColumnFilter['arrForColumnProps'];
        $columnFilterOptionProps = $columnAndColumnFilter['arrForColumnFilterProps'];

        $conds = [
            'module_name' => Constants::frontendSetting,
            'enable' => 1,
            'mandatory' => 1,
            'is_core_field' => 1,
        ];

        $frontendColorsConds['fe_color'] = 1;
        $frontendColors = $this->colorService->getColors(null, null, $frontendColorsConds);

        $core_headers = CoreFieldFilterSetting::where($conds)->get();

        $validation = [];

        foreach($core_headers as $core_header){
            if($core_header->field_name == 'frontend_logo'){
                array_push($validation, 'frontend_logo');
            }
            if($core_header->field_name == 'frontend_icon'){
                array_push($validation, 'frontend_icon');
            }
            if($core_header->field_name == 'frontend_banner'){
                array_push($validation, 'frontend_banner');
            }
        }

        $dataArr = [
            'checkPermission' => $checkPermission,
            'frontend_setting' => $frontend_setting,
            'validation' => $validation,
            'coreFieldFilterSettings' => $coreFieldFilterSettings,
            'available_languages' => $this->available_languages,
            'frontendColors' => $frontendColors
        ];
        return $dataArr;
    }

    public function getCoreFieldFilteredLists($code){
        return CoreFieldFilterSetting::where($this->coreFieldFilterModuleNameCol, $code)->get();
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
