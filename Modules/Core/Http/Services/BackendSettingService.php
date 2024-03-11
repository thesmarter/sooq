<?php
namespace Modules\Core\Http\Services;

use App\Http\Services\PsService;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\BackendSetting;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\CoreFieldFilterSetting;
use Modules\Core\Entities\CoreImage;
use Modules\Core\Entities\CustomizeUi;
use Modules\Core\Entities\ScreenDisplayUiSetting;
use Modules\Core\Http\Services\ImageService;
use Intervention\Image\Facades\Image;
use Modules\Core\Transformers\Backend\Model\BackendSetting\BackendSettingWithKeyResource;
use Illuminate\Support\Facades\File;

class BackendSettingService extends PsService
{
    protected $colorService,$backendWaterMaskImgType, $backendWaterMaskBackgroundImage,$backendWaterMaskBackgroundImageOrg, $backendMetaImgType, $backendLoginImgType, $favIconImgType, $logoImgType, $imageService, $coreImageImgParentIdCol, $coreImageImgTypeCol, $imgType, $show, $hide, $delete, $unDelete, $viewAnyAbility, $createAbility, $editAbility, $deleteAbility, $code, $screenDisplayUiKeyCol, $screenDisplayUiIdCol, $screenDisplayUiIsShowCol, $coreFieldFilterModuleNameCol, $customUiIsDelCol;

    public function __construct(ImageService $imageService ,ColorService $colorService)
    {
        $this->imageService = $imageService;
        $this->coreImageImgParentIdCol = CoreImage::imgParentId;
        $this->coreImageImgTypeCol = CoreImage::imgType;
        $this->logoImgType = 'backend-logo';
        $this->favIconImgType = 'fav-icon';
        $this->backendLoginImgType = 'backend-login-image';
        $this->backendMetaImgType = 'backend-meta-image';
        $this->backendWaterMaskImgType = 'backend-water-mask-image';
        $this->backendWaterMaskBackgroundImage = 'water-mask-background';
        $this->backendWaterMaskBackgroundImageOrg = 'water-mask-background-original';


        $this->show = Constants::show;
        $this->hide = Constants::hide;
        $this->delete = Constants::delete;
        $this->unDelete = Constants::unDelete;

        $this->viewAnyAbility = Constants::viewAnyAbility;
        $this->createAbility = Constants::createAbility;
        $this->editAbility = Constants::editAbility;
        $this->deleteAbility = Constants::deleteAbility;
        $this->code = Constants::backendSetting;

        $this->screenDisplayUiKeyCol = ScreenDisplayUiSetting::key;
        $this->screenDisplayUiIdCol = ScreenDisplayUiSetting::id;
        $this->screenDisplayUiIsShowCol = ScreenDisplayUiSetting::isShow;

        $this->coreFieldFilterModuleNameCol = CoreFieldFilterSetting::moduleName;

        $this->customUiIsDelCol = CustomizeUi::isDelete;
        $this->colorService = $colorService;
    }

    public function create($request)
    {
        DB::beginTransaction();
        try {
            $backend_setting = new BackendSetting();
            $backend_setting->sender_name = $request->sender_name;
            $backend_setting->sender_email = $request->sender_email;
            $backend_setting->receive_email = $request->receive_email;
            $backend_setting->fcm_api_key = $request->fcm_api_key;
            $backend_setting->map_key = $request->map_key;
            $backend_setting->app_token = $request->app_token;
            $backend_setting->topics = $request->topics;
            $backend_setting->topics_fe = $request->topics_fe;
            $backend_setting->smtp_host = $request->smtp_host;
            $backend_setting->smtp_port = $request->smtp_port;
            $backend_setting->smtp_user = $request->smtp_user;
            $backend_setting->smtp_pass = $request->smtp_pass;
            $backend_setting->smtp_encryption = $request->smtp_encryption;
            $backend_setting->email_verification_enabled = $request->email_verification_enabled;
            $backend_setting->user_social_info_override = $request->user_social_info_override;
            $backend_setting->landscape_width = $request->landscape_width;
            $backend_setting->potrait_height = $request->potrait_height;
            $backend_setting->square_height = $request->square_height;
            $backend_setting->landscape_thumb_width = $request->landscape_thumb_width;
            $backend_setting->potrait_thumb_height = $request->potrait_thumb_height;
            $backend_setting->square_thumb_height = $request->square_thumb_height;
            $backend_setting->landscape_thumb2x_width = $request->landscape_thumb2x_width;
            $backend_setting->potrait_thumb2x_height = $request->potrait_thumb2x_height;
            $backend_setting->square_thumb2x_height = $request->square_thumb2x_height;
            $backend_setting->landscape_thumb3x_width = $request->landscape_thumb3x_width;
            $backend_setting->potrait_thumb3x_height = $request->potrait_thumb3x_height;
            $backend_setting->square_thumb3x_height = $request->square_thumb3x_height;
            $backend_setting->dyn_link_key = $request->dyn_link_key;
            $backend_setting->dyn_link_url = $request->dyn_link_url;
            $backend_setting->dyn_link_package_name = $request->dyn_link_package_name;
            $backend_setting->dyn_link_domain = $request->dyn_link_domain;
            $backend_setting->dyn_link_deep_url = $request->dyn_link_deep_url;
            $backend_setting->ios_boundle_id = $request->ios_boundle_id;
            $backend_setting->ios_appstore_id = $request->ios_appstore_id;
            $backend_setting->backend_version_no = $request->backend_version_no;
            $backend_setting->slow_moving_item_limit = $request->slow_moving_item_limit;
            $backend_setting->search_item_limit = $request->search_item_limit;
            $backend_setting->search_user_limit = $request->search_user_limit;
            $backend_setting->search_category_limit = $request->search_category_limit;
            $backend_setting->date_format = $request->date_format;
            $backend_setting->watermask_image_size = $request->watermask_image_size;
            $backend_setting->font_size = $request->font_size;
            $backend_setting->position = $request->position;
            $backend_setting->padding = $request->padding;
            $backend_setting->opacity = $request->opacity;
            $backend_setting->added_user_id = Auth::user()->id;
            $backend_setting->fe_setting = $request->fe_setting;
            $backend_setting->vendor_setting = $request->vendor_setting;
            $backend_setting->save();

            // save backend-logo
            if ($request->file('backend_logo')){
                $file = $request->file('backend_logo');
                $data[$this->coreImageImgParentIdCol] = $backend_setting->id;
                $data[$this->coreImageImgTypeCol] = $this->logoImgType;
                $this->imageService->insertImage($file, $data);
            }

            // save fav-icon
            if ($request->file('fav_icon')){
                $file = $request->file('fav_icon');
                $data[$this->coreImageImgParentIdCol] = $backend_setting->id;
                $data[$this->coreImageImgTypeCol] = $this->favIconImgType;
                $this->imageService->insertImage($file, $data);
            }

            // save backend-meta-image
            if ($request->file('backend_meta_image')){
                $file = $request->file('backend_meta_image');
                $data[$this->coreImageImgParentIdCol] = $backend_setting->id;
                $data[$this->coreImageImgTypeCol] = $this->backendMetaImgType;
                $this->imageService->insertImage($file, $data);
            }

            // save backend-meta-image
            if ($request->file('backend_water_mask_image')){
                $file = $request->file('backend_water_mask_image');
                $data[$this->coreImageImgParentIdCol] = $backend_setting->id;
                $data[$this->coreImageImgTypeCol] = $this->backendWaterMaskImgType;
                $this->imageService->insertImage($file, $data);
            }


            // save backend-login-image
            if ($request->file('backend_login_image')){
                $file = $request->file('backend_login_image');
                $data[$this->coreImageImgParentIdCol] = $backend_setting->id;
                $data[$this->coreImageImgTypeCol] = $this->backendLoginImgType;
                $this->imageService->insertImage($file, $data);
            }
            DB::commit();
        } catch (\Throwable $e){
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }

        return $backend_setting;
    }

    public function update($id,$request)
    {
        try{
        DB::beginTransaction();


            $backend_setting = BackendSetting::find($id);
            $backend_setting->sender_name = $request->sender_name;
            $backend_setting->sender_email = $request->sender_email;
            $backend_setting->receive_email = $request->receive_email;
            $backend_setting->fcm_api_key = $request->fcm_api_key;
            $backend_setting->map_key = $request->map_key;
            $backend_setting->app_token = $request->app_token;
            $backend_setting->topics = $request->topics;
            $backend_setting->topics_fe = $request->topics_fe;
            $backend_setting->smtp_host = $request->smtp_host;
            $backend_setting->smtp_port = $request->smtp_port;
            $backend_setting->smtp_user = $request->smtp_user;
            $backend_setting->smtp_pass = $request->smtp_pass;
            $backend_setting->smtp_encryption = $request->smtp_encryption;
            $backend_setting->email_verification_enabled = $request->email_verification_enabled;
            $backend_setting->user_social_info_override = $request->user_social_info_override;
            $backend_setting->landscape_width = $request->landscape_width;
            $backend_setting->potrait_height = $request->potrait_height;
            $backend_setting->square_height = $request->square_height;
            $backend_setting->landscape_thumb_width = $request->landscape_thumb_width;
            $backend_setting->potrait_thumb_height = $request->potrait_thumb_height;
            $backend_setting->square_thumb_height = $request->square_thumb_height;
            $backend_setting->landscape_thumb2x_width = $request->landscape_thumb2x_width;
            $backend_setting->potrait_thumb2x_height = $request->potrait_thumb2x_height;
            $backend_setting->square_thumb2x_height = $request->square_thumb2x_height;
            $backend_setting->landscape_thumb3x_width = $request->landscape_thumb3x_width;
            $backend_setting->potrait_thumb3x_height = $request->potrait_thumb3x_height;
            $backend_setting->square_thumb3x_height = $request->square_thumb3x_height;
            $backend_setting->dyn_link_key = $request->dyn_link_key;
            $backend_setting->dyn_link_url = $request->dyn_link_url;
            $backend_setting->dyn_link_package_name = $request->dyn_link_package_name;
            $backend_setting->dyn_link_domain = $request->dyn_link_domain;
            $backend_setting->dyn_link_deep_url = $request->dyn_link_deep_url;
            $backend_setting->ios_boundle_id = $request->ios_boundle_id;
            $backend_setting->ios_appstore_id = $request->ios_appstore_id;
            $backend_setting->backend_version_no = $request->backend_version_no;
            $backend_setting->slow_moving_item_limit = $request->slow_moving_item_limit;
            $backend_setting->search_item_limit = $request->search_item_limit;
            $backend_setting->search_user_limit = $request->search_user_limit;
            $backend_setting->search_category_limit = $request->search_category_limit;
            $backend_setting->date_format = $request->date_format;
            $backend_setting->watermask_image_size = $request->watermask_image_size;
            $backend_setting->font_size = $request->font_size;
            $backend_setting->position = $request->position;
            $backend_setting->padding = $request->padding;
            $backend_setting->opacity = $request->opacity;
            $backend_setting->watermask_title = $request->watermask_title;
            $backend_setting->watermask_angle = $request->watermask_angle;
            $backend_setting->is_watermask = $request->is_watermask;
            $backend_setting->is_google_map = $request->is_google_map == "Google Map" ? 1 : 0;
            $backend_setting->is_open_street_map = $request->is_google_map == "Google Map" ? 0 : 1;
            $backend_setting->updated_user_id = Auth::user()->id;
            $backend_setting->fe_setting = $request->fe_setting;
            $backend_setting->vendor_setting = $request->vendor_setting;
            $backend_setting->upload_setting = $request->upload_setting;
            $backend_setting->update();

            // update backend-logo
            if ($request->file('backend_logo')){

                $file = $request->file('backend_logo');

                $conds[$this->coreImageImgParentIdCol] = $backend_setting->id;
                $conds[$this->coreImageImgTypeCol] = $this->logoImgType;
                $image = CoreImage::where($conds)->first();

                // if image, delete existing file
                if(!empty($image)){
                    // delete image from storage folder
                    $this->imageService->deleteImage($image->img_path);
                }

                $data[$this->coreImageImgParentIdCol] = $backend_setting->id;
                $data[$this->coreImageImgTypeCol] = $this->logoImgType;
                $this->imageService->update(null,null,$file, $data, $image);
            }

            // update fav-icon
            if ($request->file('fav_icon')){

                $file = $request->file('fav_icon');

                $conds[$this->coreImageImgParentIdCol] = $backend_setting->id;
                $conds[$this->coreImageImgTypeCol] = $this->favIconImgType;
                $image = CoreImage::where($conds)->first();

                // if image, delete existing file
                if(!empty($image)){
                    // delete image from storage folder
                    $this->imageService->deleteImage($image->img_path);
                }

                $data[$this->coreImageImgParentIdCol] = $backend_setting->id;
                $data[$this->coreImageImgTypeCol] = $this->favIconImgType;
                $this->imageService->update(null,null,$file, $data, $image);
            }

            // Meta Image
            if ($request->file('backend_meta_image')){

                $file = $request->file('backend_meta_image');

                $conds[$this->coreImageImgParentIdCol] = $backend_setting->id;
                $conds[$this->coreImageImgTypeCol] = $this->backendMetaImgType;
                $image = CoreImage::where($conds)->first();

                // if image, delete existing file
                if(!empty($image)){
                    // delete image from storage folder
                    $this->imageService->deleteImage($image->img_path);
                }

                $data[$this->coreImageImgParentIdCol] = $backend_setting->id;
                $data[$this->coreImageImgTypeCol] = $this->backendMetaImgType;
                $this->imageService->update(null,null,$file, $data, $image);
            }

            if ($request->file('backend_water_mask_image')){

                $file = $request->file('backend_water_mask_image');

                $conds[$this->coreImageImgParentIdCol] = $backend_setting->id;
                $conds[$this->coreImageImgTypeCol] = $this->backendWaterMaskImgType;
                // dd($this->backendWaterMaskBackgroundImage);
                $image = CoreImage::where($conds)->first();

                // if image, delete existing file
                if(!empty($image)){
                    // delete image from storage folder
                    $this->imageService->deleteImage($image->img_path);
                }

                $data[$this->coreImageImgParentIdCol] = $backend_setting->id;
                $data[$this->coreImageImgTypeCol] = $this->backendWaterMaskImgType;
                $this->imageService->update(null,null,$file, $data, $image);
            }

            if ($request->file('water_mask_background')){
                $file = $request->file('water_mask_background');

                $conds[$this->coreImageImgParentIdCol] = $backend_setting->id;
                $conds[$this->coreImageImgTypeCol] = $this->backendWaterMaskBackgroundImage;
                $image = CoreImage::where($conds)->first();


                // if image, delete existing file
                if(!empty($image)){
                    // delete image from storage folder
                    $this->imageService->deleteImage($image->img_path);
                }

                $data[$this->coreImageImgParentIdCol] = $backend_setting->id;
                $data[$this->coreImageImgTypeCol] = $this->backendWaterMaskBackgroundImage;
                // dd($data);
                $this->imageService->update(null,null,$file, $data, $image);

                // preview save
                $conds[$this->coreImageImgParentIdCol] = $backend_setting->id;
                $conds[$this->coreImageImgTypeCol] = $this->backendWaterMaskBackgroundImageOrg;
                $image = CoreImage::where($conds)->first();


                // if image, delete existing file
                if(!empty($image)){
                    // delete image from storage folder
                    $this->imageService->deleteImage($image->img_path);
                }

                $data[$this->coreImageImgParentIdCol] = $backend_setting->id;
                $data[$this->coreImageImgTypeCol] = $this->backendWaterMaskBackgroundImageOrg;
                // dd($data);
                $this->imageService->update(null,null,$file, $data, $image,'background');



            }

            if($backend_setting->is_watermask == 1) {
                if(empty($request->file('water_mask_background')))
                {
                    $conds_background = [
                        $this->coreImageImgTypeCol => 'water-mask-background',
                    ];
                    $conds = [
                        $this->coreImageImgTypeCol => 'water-mask-background-original',
                    ];
                    $image_background = $this->imageService->getImage($conds_background);
                    $image = $this->imageService->getImage($conds);
                    if(!empty($image)){
                        // delete image from storage folder
                        $this->imageService->deleteImage($image->img_path);
                    }
                    if($image_background)
                    {
                        $storage_upload_path = '/storage/' . $this->imageService->getFolderImagePath() . '/uploads/';
                        $file_exist = File::exists(public_path() . $storage_upload_path . $image_background->img_path);

                       if($file_exist){
                        $data[$this->coreImageImgParentIdCol] = $backend_setting->id;
                        $data[$this->coreImageImgTypeCol] = $this->backendWaterMaskBackgroundImageOrg;

                        $file = Image::make(public_path() . $storage_upload_path . $image_background->img_path);
                        // dd($file);
                        $this->imageService->update(null,null,$file, $data, $image,'preview_image');
                       }
                    }
                }
            }

            // update backend-login-image
            if ($request->file('backend_login_image')){

                $file = $request->file('backend_login_image');

                $conds[$this->coreImageImgParentIdCol] = $backend_setting->id;
                $conds[$this->coreImageImgTypeCol] = $this->backendLoginImgType;
                $image = CoreImage::where($conds)->first();

                // if image, delete existing file
                if(!empty($image)){
                    // delete image from storage folder
                    $this->imageService->deleteImage($image->img_path);
                }

                $data[$this->coreImageImgParentIdCol] = $backend_setting->id;
                $data[$this->coreImageImgTypeCol] = $this->backendLoginImgType;
                $this->imageService->insertImage($file, $data, $image);
            }

            DB::commit();
            }catch (\Throwable $e){
                DB::rollBack();
                return ['error' => $e->getMessage()];
            }
        return $backend_setting;
    }

    public function getBackendSetting(){
        $backend_setting = BackendSetting::with(['backend_logo','fav_icon', 'backend_login_image','backend_meta_image','backend_water_mask_image','water_mask_background','water_mask_background_org'])->first();
        return $backend_setting;
    }

    // --------------
	public function index($routeName){

        $code = $this->code;
        $coreFieldFilterSettings = $this->getCoreFieldFilteredLists($code);
        // check permission
        $checkPermission = $this->checkPermission($this->viewAnyAbility, BackendSetting::class, "admin.index");

        $backend_setting = $this->getBackendSetting();

        $commonColor = $this->colorService->getColorByKey('backend_color');






        // taking for column and columnFilterOption
        // $columnAndColumnFilter = $this->takingForColumnAndFilterOption();
        // $showRoleCols = $columnAndColumnFilter['showCoreField'];
        // $columnProps = $columnAndColumnFilter['arrForColumnProps'];
        // $columnFilterOptionProps = $columnAndColumnFilter['arrForColumnFilterProps'];
        $conds = [
            'module_name' => Constants::backendSetting,
            'enable' => 1,
            'mandatory' => 1,
            'is_core_field' => 1,
        ];

        $core_headers = CoreFieldFilterSetting::where($conds)->get();

        $validation = [];

        foreach($core_headers as $core_header){
            if($core_header->field_name == 'backend_logo'){
                array_push($validation, 'backend_logo');
            }
            if($core_header->field_name == 'backend_login_image'){
                array_push($validation, 'backend_login_image');
            }
            if($core_header->field_name == 'fav_icon'){
                array_push($validation, 'fav_icon');
            }
            if($core_header->field_name == 'backend_meta_image'){
                array_push($validation, 'backend_meta_image');
            }
            if($core_header->field_name == 'backend_water_mask_image'){
                array_push($validation, 'backend_water_mask_image');
            }
            if($core_header->field_name == 'water_mask_background'){
                array_push($validation, 'water_mask_background');
            }
            if($core_header->field_name == 'water_mask_background_org'){
                array_push($validation, 'water_mask_background_org');
            }
        }

        $paddingList = [
            [
                'id' => 1,
                'label' => __('core__watermask_bottom_right'),
                'value'=>'bottom-right'
            ],
            [
                'id' => 2,
                'label' => __('core__watermask_bottom'),
                'value'=>'bottom'
            ],
            [
                'id' => 3,
                'label' => __('core__watermask_bottom_left'),
                'value'=>'bottom-left'
            ],
            [
                'id' => 4,
                'label' => __('core__watermask_top_right'),
                'value'=>'top-right'
            ],
            [
                'id' => 5,
                'label' => __('core__watermask_top'),
                'value'=>'top'
            ],
            [
                'id' => 6,
                'label' => __('core__watermask_top_left'),
                'value'=>'top-left'
            ],
            [
                'id' => 7,
                'label' => __('core__watermask_right'),
                'value'=>'right'
            ],
            [
                'id' => 8,
                'label' => __('core__watermask_center'),
                'value'=>'center'
            ],
            [
                'id' => 9,
                'label' => __('core__watermask_left'),
                'value'=>'left'
            ]
        ];

        $uploadSettingList = [
            [
                'id' => 1,
                'label' => __('core__admin'),
                'value'=>'admin'
            ],
            [
                'id' => 2,
                'label' => __('core__admin_bluemark'),
                'value'=>'admin-bluemark'
            ],
            [
                'id' => 3,
                'label' => __('core__be_all'),
                'value'=>'all'
            ],
        ];

        $dataArr = [
            'checkPermission' => $checkPermission,
            'backend_setting' => $backend_setting,
            'validation' => $validation,
            'paddingList'=>$paddingList,
            'uploadSettingList'=>$uploadSettingList,
            // 'showRoleCols' => $showRoleCols,
            // 'showCoreAndCustomFieldArr' => $columnProps,
            // 'hideShowFieldForFilterArr' => $columnFilterOptionProps,
            'coreFieldFilterSettings' => $coreFieldFilterSettings,
            'commonColor'=>$commonColor,
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
