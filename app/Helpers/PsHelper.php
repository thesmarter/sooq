<?php

use Carbon\Carbon;
use App\Models\User;
use Inertia\Inertia;
use App\Config\ps_url;
use Carbon\CarbonPeriod;
use App\Config\ps_constant;
use App\Config\ps_config;
use App\Http\Services\PsService;
use Modules\Core\Entities\Item;

use Modules\Core\Entities\Touch;
use Modules\Core\Entities\UiType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\CoreKey;
use Modules\Core\Entities\Project;
use Illuminate\Support\Facades\URL;
use Modules\Chat\Entities\ChatNoti;
use Modules\Core\Entities\Currency;
use Modules\Core\Entities\ItemInfo;
use Modules\Core\Entities\Language;
use Modules\Rating\Entities\Rating;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Modules\Core\Entities\CoreImage;
use Modules\Core\Entities\Favourite;
use Illuminate\Support\Facades\Route;
use Intervention\Image\Facades\Image;
use Modules\Core\Constants\Constants;
use Illuminate\Support\Facades\Schema;
use Modules\Chat\Entities\ChatHistory;
use Modules\Core\Entities\CoreKeyType;
use Modules\Core\Entities\CustomizeUi;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Entities\SearchHistory;
use Modules\BlockUser\Entities\BlockUser;
use Modules\Core\Entities\ApiCallSetting;
use Modules\Core\Entities\BackendSetting;
use Modules\Core\Entities\CoreKeyCounter;
use Modules\Core\Entities\LanguageString;
use Modules\Core\Entities\RolePermission;
use Modules\Core\Entities\UserPermission;
use Modules\Core\Entities\FeLanguageString;
use Modules\Core\Entities\VendorLanguageString;
use Modules\FollowUser\Entities\FollowUser;
use Modules\Core\Entities\CustomizeUiDetail;
use Modules\ContactUsMessage\Entities\Contact;
use Modules\Core\Entities\BuilderAppInfoCache;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\BlueMarkUser\Entities\BlueMarkUser;
use Modules\Core\Entities\PushNotificationUser;
use Modules\Core\Entities\PushNotificationToken;
use Modules\ComplaintItem\Entities\ComplaintItem;
use Modules\Core\Entities\CoreFieldFilterSetting;
use Modules\Core\Entities\MobileSetting;
use Modules\Core\Entities\LogChange;
use Modules\Core\Entities\ScreenDisplayUiSetting;
use Modules\ItemPromotion\Entities\PaidItemHistory;
use Modules\Package\Entities\PackageBoughtTransaction;
use Modules\Core\Entities\VendorRolePermission;
use Modules\Core\Entities\VendorRole;
use Modules\Core\Entities\VendorUserPermission;
use Modules\Core\Entities\Vendor;

if (!function_exists('deleteUserRelatedData')) {
    /**
     * Set the active class to the current opened menu.
     *
     * @param  string|array $route
     * @param  string       $className
     * @return string
     */
    function deleteUserRelatedData($id)
    {
        DB::beginTransaction();
        try {
            //delete rating
            $fromUserCond['from_user_id'] = $id;
            $toUserCond['to_user_id'] = $id;
            Rating::where($fromUserCond)->delete();
            Rating::where($toUserCond)->delete();

            //delete follow
            $followUserCond['user_id'] = $id;
            $followedUserCond['followed_user_id'] = $id;
            FollowUser::where($followUserCond)->delete();
            FollowUser::where($followedUserCond)->delete();

            // delete block
            $fromBlockUserCond['from_block_user_id'] = $id;
            $toBlockUserCond['to_block_user_id'] = $id;
            BlockUser::where($fromBlockUserCond)->delete();
            BlockUser::where($toBlockUserCond)->delete();

            //delete noti
            $notiCond['user_id'] = $id;
            PushNotificationToken::where($notiCond)->delete();
            PushNotificationUser::where($notiCond)->delete();

            //chat history
            $buyerConds['buyer_user_id'] = $id;
            $sellerConds['seller_user_id'] = $id;
            ChatHistory::where($buyerConds)->delete();
            ChatHistory::where($sellerConds)->delete();

            //chat noti
            ChatNoti::where($buyerConds)->delete();
            ChatNoti::where($sellerConds)->delete();

            //contact us messages
            $contactConds['added_user_id'] = $id;
            Contact::where($contactConds)->delete();

            //delete favourite, touch
            $usrDeleteCond['user_id'] = $id;
            Favourite::where($usrDeleteCond)->delete();
            Touch::where($usrDeleteCond)->delete();
            //search history
            SearchHistory::where($usrDeleteCond)->delete();
            //package bought soft delete
            PackageBoughtTransaction::where($usrDeleteCond)->delete();

            //user report
            $reportConds['reported_user_id'] = $id;
            ComplaintItem::where($reportConds)->delete();

            //delete Item
            $itemdeleteCond['added_user_id'] = $id;
            $items = Item::where($itemdeleteCond)->get();

            //define image paths
            $upload_path = 'storage/uploads/';
            $thumb1x_path = 'storage/thumbnail/';
            $thumb2x_path = 'storage/thumbnail2x/';
            $thumb3x_path = 'storage/thumbnail3x/';
            $storage_upload_path = '/storage/' . Constants::folderPath . '/uploads/';
            $storage_thumb1x_path = '/storage/' . Constants::folderPath . '/thumbnail/';
            $storage_thumb2x_path = '/storage/' . Constants::folderPath . '/thumbnail2x/';
            $storage_thumb3x_path = '/storage/' . Constants::folderPath . '/thumbnail3x/';

            foreach ($items as $item) {
                $productRelations = ItemInfo::where('item_id', $item->id)->get();
                foreach ($productRelations as $productRelation) {
                    //delete custom field images
                    if (str_contains($productRelation->value, '.png') || str_contains($productRelation->value, '.jpg')) {
                        Storage::delete($upload_path . $productRelation->value);
                        Storage::delete($storage_upload_path . $productRelation->value);
                        Storage::delete($storage_thumb1x_path . $productRelation->value);
                        Storage::delete($storage_thumb2x_path . $productRelation->value);
                        Storage::delete($storage_thumb3x_path . $productRelation->value);
                        Storage::delete($thumb1x_path . $productRelation->value);
                        Storage::delete($thumb2x_path . $productRelation->value);
                        Storage::delete($thumb3x_path . $productRelation->value);
                    }
                    //delete custom field
                    $productRelation->delete();
                }

                //item image and video delete start
                $imageConds['img_parent_id'] = $item->id;
                $imageConds['img_type'] = 'item';
                $videoConds['img_parent_id'] = $item->id;
                $videoConds['img_type'] = 'item-video';
                $videoIconConds['img_parent_id'] = $item->id;
                $videoIconConds['img_type'] = 'item-video-icon';
                $images = CoreImage::where($imageConds)->get();
                $videos = CoreImage::where($videoConds)->get();
                $videoIcons = CoreImage::where($videoIconConds)->get();

                if (count($images) > 0) {
                    $imageIds = $images->pluck('id');
                    CoreImage::destroy($imageIds);
                    foreach ($images as $image) {
                        // delete image from storage folder
                        Storage::delete($upload_path . $image->img_path);
                        Storage::delete($storage_upload_path . $image->img_path);
                        Storage::delete($storage_thumb1x_path . $image->img_path);
                        Storage::delete($storage_thumb2x_path . $image->img_path);
                        Storage::delete($storage_thumb3x_path . $image->img_path);
                        Storage::delete($thumb1x_path . $image->img_path);
                        Storage::delete($thumb2x_path . $image->img_path);
                        Storage::delete($thumb3x_path . $image->img_path);
                    }
                }

                if (count($videos) > 0) {
                    $videoIds = $videos->pluck('id');
                    CoreImage::destroy($videoIds);
                    foreach ($videos as $image) {
                        // delete image from storage folder
                        Storage::delete($upload_path . $image->img_path);
                        Storage::delete($storage_upload_path . $image->img_path);
                        Storage::delete($storage_thumb1x_path . $image->img_path);
                        Storage::delete($storage_thumb2x_path . $image->img_path);
                        Storage::delete($storage_thumb3x_path . $image->img_path);
                        Storage::delete($thumb1x_path . $image->img_path);
                        Storage::delete($thumb2x_path . $image->img_path);
                        Storage::delete($thumb3x_path . $image->img_path);
                    }
                }

                if (count($videoIcons) > 0) {
                    $videoIconIds = $videoIcons->pluck('id');
                    CoreImage::destroy($videoIconIds);
                    foreach ($videoIcons as $image) {
                        // delete image from storage folder
                        Storage::delete($upload_path . $image->img_path);
                        Storage::delete($storage_upload_path . $image->img_path);
                        Storage::delete($storage_thumb1x_path . $image->img_path);
                        Storage::delete($storage_thumb2x_path . $image->img_path);
                        Storage::delete($storage_thumb3x_path . $image->img_path);
                        Storage::delete($thumb1x_path . $image->img_path);
                        Storage::delete($thumb2x_path . $image->img_path);
                        Storage::delete($thumb3x_path . $image->img_path);
                    }
                }
                //item image and video delete end

                //delete item
                $item->delete();
                $itemConds['item_id'] = $item->id;
                Touch::where('type_name', 'item')->where('type_id', $item->id)->delete();
                ComplaintItem::where($itemConds)->delete();
                ChatHistory::where($itemConds)->delete();
                ChatNoti::where($itemConds)->delete();
                Favourite::where($itemConds)->delete();
                PaidItemHistory::where($itemConds)->delete();
            }

            // delete blue mark user
            $blueMarkConds['user_id'] = $id;
            BlueMarkUser::where($blueMarkConds)->delete();

            //delete vendor
            Vendor::where('owner_user_id', $id)->delete();

            //permission
            UserPermission::where('user_id', $id)->delete();

            DB::commit();
            return true;
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }
}

if (!function_exists('isActive')) {
    /**
     * Set the active class to the current opened menu.
     *
     * @param  string|array $route
     * @param  string       $className
     * @return string
     */
    function isActive($route, $className = 'active')
    {
        if (is_array($route)) {
            return in_array(Route::currentRouteName(), $route) ? $className : '';
        }
        if (Route::currentRouteName() == $route) {
            return $className;
        }
        if (strpos(URL::current(), $route)) {
            return $className;
        }
    }
}

if (!function_exists('columnOrdering')) {
    function columnOrdering($field, $arrObj, $sortType = SORT_ASC)
    {
        $col  = $field;
        $sort = array();
        foreach ($arrObj as $i => $obj) {
            $sort[$i] = $obj->{$col};
        }

        $sorted_db = array_multisort($sort, $sortType, $arrObj);
        return $arrObj;
    }
}


if (!function_exists('deeplinkGenerate')) {
    /**
     * @param String,Integer  $id - item id
     * @param String $title - item title
     * @param String $description - item description
     * @param String $img - item image path
     *
     * @return String generated deeplink short url
     */
    function deeplinkGenerate($id, $title, $description, $img)
    {
        $folder_path_thumbnail1x = '/storage/' . Constants::folderPath . '/thumbnail/';
        $backend_setting = BackendSetting::first();

        // check description length
        if (strlen($description) > 6605) {
            $description = substr($description, 0, 6605);
        }

        // $title = strtolower($title);
        // $item_name = str_replace(' ', '-', $title);
        $longUrl = $backend_setting->dyn_link_deep_url . '/fe_item?item_id=' . $id;

        $landingPage = $backend_setting->dyn_link_deep_url;

        //Web API Key From Firebase
        $key = $backend_setting->dyn_link_key;

        //Firebase Rest API URL
        $url = $backend_setting->dyn_link_url . $key;

        //To link with Android App, so need to provide with android package name
        $androidInfo = array(
            "androidPackageName" => $backend_setting->dyn_link_package_name,
            // "androidFallbackLink" => $landingPage,
        );

        //For iOS
        $iOSInfo = array(
            "iosBundleId" => $backend_setting->ios_boundle_id,
            "iosAppStoreId" => $backend_setting->ios_appstore_id,
            // "iosFallbackLink" => $landingPage,
        );

        //For meta data when share the URL
        $socialMetaTagInfo = array(
            "socialDescription" => $description,
            "socialImageLink"   => $backend_setting->dyn_link_deep_url . $folder_path_thumbnail1x . $img,
            "socialTitle"       => $title
        );

        //For only 4 character at url
        $suffix = array(
            "option" => "SHORT"
        );

        $data = array(
            "dynamicLinkInfo" => array(
                "dynamicLinkDomain" => $backend_setting->dyn_link_domain,
                "link" => $longUrl,
                //    "link" => $landingPage,
                "androidInfo" => $androidInfo,
                "iosInfo" => $iOSInfo,
                "socialMetaTagInfo" => $socialMetaTagInfo
            ),
            "suffix" => $suffix
        );

        $headers = array('Content-Type: application/json');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $data = curl_exec($ch);
        curl_close($ch);

        if ($data != false) {
            $short_url = json_decode($data);
            if ($short_url == null || isset($short_url->error)) {
                $status = [
                    'msg' => $short_url->error->message,
                    'flag' => 'error',
                ];
                return $status;
            } else {
                $status = [
                    'msg' => $short_url->shortLink,
                    'flag' => 'success',
                ];
                return $status;
            }
        } else {
            $status = [
                'msg' => 'Wrong Configuration',
                'flag' => 'error',
            ];
            return $status;
        }
    }
}

if (!function_exists('duplicate')) {
    /**
     * @param Model_instatnce $data - original data - array object from table Model
     * @param Array $copies - data to be updated during duplication - array
     * @param Boolean $img_copy (optional) - need or not image file to copy - false is noe copy image file, otherwise is copy img
     *
     * @return Model_instatnce duplicated model
     */
    function duplicate($data, $copies, $img_copy = false)
    {
        // replicate model with customize data from $copies
        $duplicate = $data->replicate()->fill($copies);
        $duplicate->save();

        // 1) update and copy a image record to and from core_images table
        // 2) copy image file from storage
        if ($img_copy == true) {

            $storage_upload_path = '/storage/PSX_MPC/uploads/';
            $storage_thumb1x_path = '/storage/PSX_MPC/thumbnail/';
            $storage_thumb2x_path = '/storage/PSX_MPC/thumbnail2x/';
            $storage_thumb3x_path = '/storage/PSX_MPC/thumbnail3x/';

            $images = CoreImage::where('img_parent_id', $data->id)->get();
            if (count($images) > 0) {
                foreach ($images as $image) {
                    // duplicate data image to table
                    $image_copies['img_path'] = $duplicate->id . '_' . $image->img_path;
                    $image_copies['img_parent_id'] = $duplicate->id;
                    $duplicate_image = $image->replicate()->fill($image_copies);
                    $duplicate_image->save();

                    // duplicate data image to storage file
                    // Storage::copy($storage_upload_path . $image->img_path, $storage_upload_path . $duplicate_image->img_path);
                    // Storage::copy($storage_thumb1x_path . $image->img_path, $storage_thumb1x_path . $duplicate_image->img_path);
                    // Storage::copy($storage_thumb2x_path . $image->img_path, $storage_thumb2x_path . $duplicate_image->img_path);
                    // Storage::copy($storage_thumb3x_path . $image->img_path, $storage_thumb3x_path . $duplicate_image->img_path);
                    try {
                        File::copy(public_path($storage_upload_path . $image->img_path), public_path($storage_upload_path . $duplicate_image->img_path));
                        File::copy(public_path($storage_thumb1x_path . $image->img_path), public_path($storage_thumb1x_path . $duplicate_image->img_path));
                        File::copy(public_path($storage_thumb2x_path . $image->img_path), public_path($storage_thumb2x_path . $duplicate_image->img_path));
                        File::copy(public_path($storage_thumb3x_path . $image->img_path), public_path($storage_thumb3x_path . $duplicate_image->img_path));
                    } catch (Exception $e) {
                        continue;
                    }
                }
            }
        }

        return $duplicate;
    }
}

if (!function_exists('validateForCustomField')) {
    function validateForCustomField($moduleName, $request = null)
    {

        $haveValueId = [];
        $customizeHeaderIds = [];
        $errors = [];

        $customizeHeaders = CustomizeUi::where('module_name', $moduleName)->get();
        foreach ($customizeHeaders as $key => $value) {
            array_push($customizeHeaderIds, $value->core_keys_id);
        }

        if (!empty($request)) {
            foreach ($request as $key => $postRel) {

                if (!is_object($postRel)) {
                    $coreKeysIdFromReq = $key;
                    $valueFromReq = $postRel;
                } else {
                    $coreKeysIdFromReq = $postRel->core_keys_id;
                    $valueFromReq = $postRel->value;
                }

                if ($valueFromReq !== null) {
                    array_push($haveValueId, $coreKeysIdFromReq);
                }
            }
        }
        $result = array_diff($customizeHeaderIds, $haveValueId);
        foreach ($result as $value) {
            foreach ($customizeHeaders as $key => $value2) {
                if ($value === $value2->core_keys_id && $value2->mandatory === 1 && $value2->enable === 1 && $value2->is_delete === 0) {
                    $errMessage = __($value2->name) . " is required.";
                    $errors[$value2->core_keys_id] = $errMessage;
                }
            }
        }

        return $errors;
    }
}

if (!function_exists('validateForCustomFieldFromApi')) {
    function validateForCustomFieldFromApi($moduleName, $request)
    {

        $haveValueId = [];
        $customizeHeaderIds = [];
        $errors = [];


        $customizeHeaders = CustomizeUi::where('module_name', $moduleName)->get();
        foreach ($customizeHeaders as $key => $value) {
            array_push($customizeHeaderIds, $value->core_keys_id);
        }


        foreach ($request as $key => $postRel) {
            if ($postRel['value'] !== null) {
                array_push($haveValueId, $postRel['core_keys_id']);
            }
        }

        $result = array_diff($customizeHeaderIds, $haveValueId);

        foreach ($result as $value) {
            foreach ($customizeHeaders as $key => $value2) {

                if ($value === $value2->core_keys_id && $value2->mandatory === 1 && $value2->enable === 1 && $value2->is_delete === 0) {
                    $errMessage = $value2->name . " is required";
                    $errors[$value2->core_keys_id] = $errMessage;
                }
            }
        }

        return $errors;
    }
}

if (!function_exists('responseMsgApi')) {
    function responseMsgApi($message = "Record not Found", $code = Constants::notFoundStatusCode, $status = Constants::errorStatus)
    {
        // dd("here");
        return response([
            "status" => $status,
            "message" =>  $message,
        ], $code);
    }
}

if (!function_exists('responseDataApi')) {
    function responseDataApi($message, $code = Constants::okStatusCode)
    {
        return response($message, $code);
    }
}



if (!function_exists('customizeDetailsApi')) {
    function custiomizeDetailsApi($coreKeysId, $limit, $offset, $msg = "Record Not Found")
    {

        $customizeDetails = CustomizeUiDetail::where('core_keys_id', $coreKeysId)
            ->latest('id')
            ->when($limit, function ($query, $limit) {
                $query->limit($limit);
            })
            ->when($offset, function ($query, $offset) {
                $query->offset($offset);
            })
            ->get();

        // if not have value
        if ($customizeDetails->isEmpty()) {
            return ['error' =>   $msg];
        }

        return $customizeDetails;
    }
}

if (!function_exists('uiTypesForCustomizeDetails')) {
    function uiTypesForCustomizeDetailsApi($coreKeyIdsForUiType, $limit, $offset, $msg = "Record Not Found")
    {
        $uiTypesForCustomizeDetails = UiType::whereIn("core_keys_id", $coreKeyIdsForUiType)
            ->when($limit, function ($query, $limit) {
                $query->limit($limit);
            })
            ->when($offset, function ($query, $offset) {
                $query->offset($offset);
            })
            ->get();

        // if not have value
        if ($uiTypesForCustomizeDetails->isEmpty()) {
            return ['error' =>   $msg];
        }

        return $uiTypesForCustomizeDetails;
    }
}

if (!function_exists('permissionControl')) {
    /**
     * @param $module_id
     * @param $permission_id
     */
    function permissionControl($module_id = '', $permission_id = '')
    {

        $loginUserId = Auth::id();
        // check request from api or backend
        if (!empty($_GET['login_user_id'])) {
            $loginUserIdFromGet = $_GET['login_user_id'];
            $loginUserRoles = UserPermission::where('user_id', $loginUserIdFromGet)->first();
        } else {
            $loginUserRoles = UserPermission::where('user_id', $loginUserId)->first();
        }

        if (!empty($loginUserRoles)) {
            $roleIds = explode(',', $loginUserRoles->role_id);
            $userAccesses = RolePermission::whereIn('role_id', $roleIds)->where('module_id', $module_id)->get();

            //        $userPermissions = UserPermission::where('user_id',$loginUserId)->where('module_id',$module_id)->first();
            foreach ($userAccesses as $userAccess) {
                if ($userAccess) {
                    $permission = $userAccess->permission_id;
                    $permissionIds = explode(',', $permission);
                    if ($permission_id == ps_constant::readPermission) {
                        foreach ($permissionIds as $id) {
                            if ($id == $permission_id || $id == ps_constant::createPermission || $id == ps_constant::updatePermission || $id == ps_constant::deletePermission) {
                                return true;
                            }
                        }
                    }
                    foreach ($permissionIds as $id) {
                        if ($id == $permission_id) {
                            return true;
                        }
                    }
                }
            }
        }
    }
}

if (!function_exists('vendorPermissionControl')) {
    /**
     * @param $module_id
     * @param $permission_id
     */
    function vendorPermissionControl($module_id = '', $permission_id = '', $vendorId = '')
    {
        $permission_id = strval($permission_id);
        $loginUserId = Auth::id();

        if (!empty($_GET['login_user_id'])) {
            $loginUserId = $_GET['login_user_id'];
        }


        $vendorRole = VendorUserPermission::where('user_id',$loginUserId)->first();

        if(!$vendorRole){
            return false;
        }
        $vendorRoleObj = json_decode($vendorRole->vendor_and_role);
        if(!isset($vendorRoleObj->$vendorId)){
            return false;
        }
        $getRoleIds = explode(',', $vendorRoleObj->$vendorId);
        //check if role is publish
        $roleIds = VendorRole::whereIn('id', $getRoleIds)
            ->where('status', 1)
            ->pluck('id')
            ->toArray();

        if ($permission_id == strval(ps_constant::readPermission)) {
            $rowPermission = VendorRolePermission::whereIn('vendor_role_id', $roleIds)
            ->whereJsonContains('module_and_permission->' . $module_id , $permission_id)
            ->orWhereJsonContains('module_and_permission->' . $module_id , strval(ps_constant::createPermission))
            ->orWhereJsonContains('module_and_permission->' . $module_id , strval(ps_constant::updatePermission))
            ->orWhereJsonContains('module_and_permission->' . $module_id , strval(ps_constant::deletePermission))
            ->first();
        }else{
            $rowPermission = VendorRolePermission::whereIn('vendor_role_id', $roleIds)->whereJsonContains('module_and_permission->' . $module_id , $permission_id)->first();
        }
        if($rowPermission){

            return true;
        }
        return false;

    }
}

if (!function_exists('haveVendorAndCreateAccess')) {
    /**
     * @param $module_id
     * @param $permission_id
     */
    function haveVendorAndCreateAccess($user_id = '')
    {

        $vendorRole = VendorUserPermission::where('user_id',$user_id)->first();

        if(!$vendorRole){
            return [];
        }

        $vendorRoles =  json_decode($vendorRole->vendor_and_role);
        $vendorRoleKeys = array_keys((array)$vendorRoles);
        $vendorIds = [];

        foreach($vendorRoleKeys as $vendorRoleKey){
            $getRoleIds = explode(',', $vendorRoles->$vendorRoleKey);
            //check if role is publish
            $roleIds = VendorRole::whereIn('id', $getRoleIds)
                ->where('status', 1)
                ->pluck('id')
                ->toArray();

            $rowPermission = VendorRolePermission::whereIn('vendor_role_id', $roleIds)
                ->whereJsonContains('module_and_permission->' . Constants::vendorItemModule , strval(ps_constant::createPermission))->first();
            if($rowPermission && isVendorEnable($vendorRoleKey)){
                array_push($vendorIds, strval($vendorRoleKey));
            }
        }

        return $vendorIds;

    }
}

if (!function_exists('getVendorIdFromSession')) {
    /**
     * @param $module_id
     * @param $permission_id
     */
    function getVendorIdFromSession()
    {
        $currentSessionId = session()->getId();
        $currentSessionData = DB::table('psx_custom_sessions')->where('id', $currentSessionId)->first();

        //check if have same ip address and user agent for same user id
        if($currentSessionData == null && Auth::user()){
            $sameIpAndAgent = DB::table('psx_custom_sessions')
            ->where('user_id', Auth::id())
            ->where('user_agent', request()->userAgent())
            ->where('ip_address', request()->ip())
            ->first();

            if ($sameIpAndAgent) {
                // Update the row
                DB::table('psx_custom_sessions')
                    ->where('user_id', Auth::id())
                    ->where('user_agent', request()->userAgent())
                    ->where('ip_address', request()->ip())
                    ->update(['id' => $currentSessionId]);

                // Retrieve the updated row
                $currentSessionData = DB::table('psx_custom_sessions')
                    ->where('id', $currentSessionId)
                    ->first();
            }
        }

        //if current session still null, add new
        if($currentSessionData == null && Auth::user()){

            $sessionId = $currentSessionId;
            $sessionData = new \stdClass();
            $sessionData->vendor_id = null;

            // Insert data into the psx_custom_sessions table
            DB::table('psx_custom_sessions')->insert([
                'id' => $sessionId,
                'user_id' => Auth::id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'data_obj' => json_encode($sessionData),
                'last_activity' => Carbon::now()->getTimestamp(),
            ]);

            $currentSessionData = DB::table('psx_custom_sessions')->where('id', $currentSessionId)->first();

            $vendor_id = null;
        }else if($currentSessionData == null){
            $vendor_id = null;
        }else{
            $vendor_id = json_decode($currentSessionData->data_obj)->vendor_id ?? null;
        }


        return $vendor_id;

    }
}

if (!function_exists('isVendorEnable')) {
    /**
     * @param $module_id
     * @param $permission_id
     */
    function isVendorEnable($id)
    {
        $vendor = Vendor::find($id);

        if($vendor && $vendor->status == Constants::vendorAcceptStatus){
            return true;
        }else{
            return false;
        }

    }
}

if (!function_exists('getNormalAccessVendorIds')) {
    /**
     * @param $module_id
     * @param $permission_id
     */
    function getNormalAccessVendorIds()
    {
        $user_id = Auth::id();
        $vendorRole = VendorUserPermission::where('user_id',$user_id)->first();

        if(!$vendorRole){
            return [];
        }

        $vendorRoles =  json_decode($vendorRole->vendor_and_role);
        $vendorRoleKeys = array_keys((array)$vendorRoles);
        $vendorIds = [];

        foreach($vendorRoleKeys as $vendorRoleKey){
            $getRoleIds = explode(',', $vendorRoles->$vendorRoleKey);
            //check if role is publish
            $roleIds = VendorRole::whereIn('id', $getRoleIds)
                ->where('status', 1)
                ->pluck('id')
                ->toArray();

            if(isVendorEnable($vendorRoleKey) && count($roleIds) > 0){
                array_push($vendorIds, strval($vendorRoleKey));
            }
        }

        return $vendorIds;

    }
}

if (!function_exists('authorization')) {
    /**
     * @param $module_id
     */
    function authorization($module_id)
    {

        $loginUserRoles = Auth::user()->role_id;
        if ($loginUserRoles) {
            $roleIds = explode(',', $loginUserRoles);
            $userAccesses = RolePermission::whereIn('role_id', $roleIds)->where('module_id', $module_id)->get();
            if ($userAccesses->isNotEmpty()) {
                return true;
            } else {
                return false;
            }
        }
    }
}

if (!function_exists('keyGenerate')) {
    function keyGenerate($typeCode)
    {
        $coreKeyType = CoreKeyType::where('code', $typeCode)->first();

        $coreKeyLastestRow = CoreKey::where('core_keys_id', 'like', '%' . $typeCode . '%')->latest()->first();
        if (!empty($coreKeyLastestRow)) {
            $coreKeysIdLastest = substr($coreKeyLastestRow->core_keys_id, -5);
        } else {
            $coreKeysIdLastest = null;
        }
        $countRow = str_pad($coreKeysIdLastest + 1, 5, '0', STR_PAD_LEFT);
        $core_keys_id = $coreKeyType->code . $countRow;
        return $core_keys_id;
    }
}

if (!function_exists('getCoreKey')) {
    function getCoreKey($coreKeysId)
    {
        $coreKey = CoreKey::where("core_keys_id", $coreKeysId)->first();
        return $coreKey;
    }
}


if (!function_exists('relationForCoreFieldFilter')) {
    function relationForCoreFieldFilter($coreFieldForFilter, $ownerField, $relationTable, $relationField, $coreFieldFilterForRelation)
    {
        if ($coreFieldForFilter === $ownerField) {
            //        return $coreFieldForFilter.$ownerField;
            foreach ($relationTable as $category) {
                if ($category == $relationField) {
                    return $coreFieldForFilter . $coreFieldFilterForRelation . $category;
                }
            }
        }
    }
}


if (!function_exists('read_more')) {
    function read_more($string, $limit)
    {
        $string = strip_tags($string);

        if (strlen($string) > $limit) {

            // truncate string
            $stringCut = substr($string, 0, $limit);

            // make sure it ends in a word so assassinate doesn't become ass...
            $string = substr($stringCut, 0, strrpos($stringCut, ' ')) . '...';
        }
        return $string;
    }
}

if (!function_exists('generateLangStrJson')) {
    function generateLangStrJson($fileName, $lang_str)
    {

        $languageString = [];
        foreach ($lang_str as $str) {
            $languageString[$str['key']] = $str['value'];
        }

        $file['data'] = json_encode($languageString);

        File::put(base_path('lang/' . $fileName), $file);
    }
}
if (!function_exists('generateFEangStrJson')) {
    function generateFEangStrJson($fileName, $lang_str)
    {

        $languageString = [];
        foreach ($lang_str as $str) {
            $languageString[$str['key']] = $str['value'];
        }

        $file['data'] = json_encode($languageString);

        File::put(base_path('Modules/Template/PSXFETemplate/Resources/frontend_languages/' . $fileName), $file);
    }
}

if (!function_exists('generateFeLangStrJson')) {
    function generateFeLangStrJson($fileName, $lang_str)
    {
        $languageString = [];
        foreach ($lang_str as $str) {
            $languageString[$str['key']] = $str['value'];
        }

        $file['data'] = json_encode($languageString);

        File::put(base_path('Modules/Template/PSXFETemplate/Resources/frontend_languages/' . $fileName), $file);
    }
}

if (!function_exists('generateVendorLangStrJson')) {
    function generateVendorLangStrJson($fileName, $lang_str)
    {
        $languageString = [];
        foreach ($lang_str as $str) {
            $languageString[$str['key']] = $str['value'];
        }

        $file['data'] = json_encode($languageString);

        File::put(base_path('Modules/StoreFront/VendorPanel/Resources/vendor_languages/' . $fileName), $file);
    }
}

if (!function_exists('generateAllLanguage')) {

    function generateAllLanguage()
    {

        $languages = Language::all();
        foreach ($languages as $language) {
            $languageString = [];
            $lang_str = LanguageString::select('key', 'value')->where('language_id', $language->id)->get();

            foreach ($lang_str as $str) {
                $languageString[$str['key']] = $str['value'];
            }

            $file['data'] = json_encode($languageString);

            $fileName = $language->symbol . '.json';
            File::put(base_path('lang/' . $fileName), $file);
        }
    }
}

if (!function_exists('generateAllFeLanguages')) {

    function generateAllFeLanguages()
    {

        $languages = Language::all();
        foreach ($languages as $language) {
            $languageString = [];
            $lang_str = FeLanguageString::select('key', 'value')->where('language_id', $language->id)->get();

            foreach ($lang_str as $str) {
                $languageString[$str['key']] = $str['value'];
            }

            $file['data'] = json_encode($languageString);

            $fileName = $language->symbol . '.json';
            File::put(base_path('Modules/Template/PSXFETemplate/Resources/frontend_languages/' . $fileName), $file);
        }
    }
}

if (!function_exists('generateAllVendorLanguages')) {

    function generateAllVendorLanguages()
    {

        $languages = Language::all();
        foreach ($languages as $language) {
            $languageString = [];
            $lang_str = VendorLanguageString::select('key', 'value')->where('language_id', $language->id)->get();

            foreach ($lang_str as $str) {
                $languageString[$str['key']] = $str['value'];
            }

            $file['data'] = json_encode($languageString);

            $fileName = $language->symbol . '.json';
            File::put(base_path('Modules/StoreFront/VendorPanel/Resources/vendor_languages/' . $fileName), $file);
        }
    }
}

if (!function_exists('deleteBuilderLanguageStrings')) {
    function deleteBuilderLanguageStrings()
    {
        LanguageString::where("is_from_builder", 1)->delete();
    }
}

if (!function_exists('deleteBuilderFeLanguageStrings')) {
    function deleteBuilderFeLanguageStrings()
    {
        FeLanguageString::where('is_from_builder', 1)->delete();
    }
}

if (!function_exists('redirectView')) {
    function redirectView($routeName, $msg, $flag = "success", $parameter = null)
    {

        if (empty($parameter) && !empty($routeName)) {
            return redirect()->route($routeName)->with('status', ['flag' => $flag, 'msg' => $msg]);
        } elseif (empty($routeName) && empty($parameter)) {
            return redirect()->back()->with('status', ['flag' => $flag, 'msg' => $msg]);
        } else {
            return redirect()->route($routeName, $parameter)->with('status', ['flag' => $flag, 'msg' => $msg]);
        }
    }
}

if (!function_exists('getBetweenTwoDateRangeArr')) {
    function getBetweenTwoDateRangeArr($startDate, $endDate, $format = 'Y-m-d')
    {

        $dateRange = CarbonPeriod::create($startDate, $endDate);
        $formatedDateRangeArr = [];
        foreach ($dateRange as $date) {
            array_push($formatedDateRangeArr, $date->format($format));
        }
        return $formatedDateRangeArr;
    }
}

if (!function_exists('subtractDay')) {
    function subtractDay($dayCount, $date, $format = 'Y-m-d H:i:s')
    {
        return date($format, strtotime("-$dayCount day", strtotime($date)));
    }
}



if (!function_exists('checkSave')) {
    function checkSave($returnValue, $route, $flag)
    {
        if (is_object($returnValue)) {
            $savedValue = $returnValue;
        } else {
            return redirectView($route, $returnValue, $flag);
        }
    }
}

if (!function_exists('deleteImages')) {
    function deleteImages($images)
    {
        if (count($images) > 0) {
            foreach ($images as $image) {
                // delete image from storage folder
                Storage::delete('public/uploads/' . $image->img_path);
                Storage::delete('public/thumbnail/' . $image->img_path);
                Storage::delete('public/thumbnail2x/' . $image->img_path);
                Storage::delete('public/thumbnail3x/' . $image->img_path);

                $image->delete();
            }
        }
    }
}

if (!function_exists('deleteImage')) {
    function deleteImage($image)
    {
        if (!empty($image)) {
            // delete image from storage folder
            Storage::delete('public/uploads/' . $image->img_path);
            Storage::delete('public/thumbnail/' . $image->img_path);
            Storage::delete('public/thumbnail2x/' . $image->img_path);
            Storage::delete('public/thumbnail3x/' . $image->img_path);
        }
    }
}


if (!function_exists('renderView')) {
    function renderView($componentPath, $dataForView = null)
    {
        if (empty($dataForView)) {
            return Inertia::render($componentPath);
        } else {
            return Inertia::render($componentPath, $dataForView);
        }
    }
}


if (!function_exists('getAllCoreFields')) {
    function getAllCoreFields($tableName)
    {
        return Schema::getColumnListing($tableName);
    }
}


if (!function_exists('checkPermissionApi')) {
    function checkPermissionApi($ability, $model, $msg = null)
    {
        if ($msg == null) {
            $msg = __("no_permission");
        }
        if (Gate::denies($ability, $model)) {
            return response()->json(["message" => $msg, "status" => "error"], 403);
        }
    }
}

if (!function_exists('checkOwnerShip')) {
    function checkOwnerShip($singleObj, $loginUserId)
    {
        if ($singleObj->added_user_id == $loginUserId) {
            return true;
        }
        return false;
    }
}

if (!function_exists('checkUserByLoginUser')) {
    function checkUserByLoginUser($userId, $loginUserId)
    {
        if ($userId == $loginUserId) {
            return true;
        }
        return false;
    }
}


if (!function_exists('getLoginUserId')) {
    function getLoginUserId($userIdParaFromApi = null, $userIdFromBE = null)
    {
        if (!empty($_GET['login_user_id'])) {
            $userId = $_GET['login_user_id'];
        } else {
            $userId = $userIdFromBE;
        }
        return $userId;
    }
}

if (!function_exists('createCustomizeAttr')) {
    function createCustomizeAttr($request)
    {
        $customizeDetail = new CustomizeUiDetail();
        $customizeDetail->name = $request->name;
        $customizeDetail->core_keys_id = $request->core_keys_id;
        $customizeDetail->save();

        return $customizeDetail;
    }
}

if (!function_exists('updateCustomizeAttr')) {
    function updateCustomizeAttr($customizationDetail, $request)
    {
        $customizationDetail->name = $request->name;
        $customizationDetail->core_keys_id = $request->core_keys_id;
        $customizationDetail->update();

        return $customizationDetail;
    }
}

if (!function_exists("getSupportedUi")) {
    function getSupportedUi()
    {
        $ui = UiType::all();
        return $ui;
    }
}

if (!function_exists("createCustomField")) {
    function createCustomField($request, $code)
    {
        $customizeHeader = new CustomizeUi();
        $customizeHeader->name = $request->name;
        $customizeHeader->placeholder = $request->placeholder;
        $customizeHeader->ui_type_id = $request->ui_type_id;

        $customizeHeader->core_keys_id = keyGenerate($code);
        if ($request->mandatory === false) {
            $customizeHeader->mandatory = 0;
        } else {
            $customizeHeader->mandatory = 1;
        }
        //        $key = CoreKeyType::where("name","product")->get();
        $customizeHeader->module_name = $code;
        $customizeHeader->enable = 1;
        $customizeHeader->save();
        return $customizeHeader;
    }
}

if (!function_exists("createCustomField")) {
    function createCustomField($request, $code)
    {
        $customizeHeader = new CustomizeUi();
        $customizeHeader->name = $request->name;
        $customizeHeader->placeholder = $request->placeholder;
        $customizeHeader->ui_type_id = $request->ui_type_id;

        $customizeHeader->core_keys_id = keyGenerate($code);
        if ($request->mandatory === false) {
            $customizeHeader->mandatory = 0;
        } else {
            $customizeHeader->mandatory = 1;
        }
        //        $key = CoreKeyType::where("name","product")->get();
        $customizeHeader->module_name = $code;

        $stringUiTypes = ['uit00001', 'uit00002', 'uit00003', 'uit00004', 'uit00006'];
        $imageUiTypes = ['uit00009'];
        $multiSelectUiTypes = ['uit00008'];
        $dateUiTypes = ['uit00005', 'uit00010', 'uit00011'];
        $integerUiTypes = ['uit00007'];

        if (in_array($request->ui_type_id, $stringUiTypes)) {
            $customizeHeader->data_type = 'String';
        } elseif (in_array($request->ui_type_id, $dateUiTypes)) {
            $customizeHeader->data_type = 'Date';
        } elseif (in_array($request->ui_type_id, $integerUiTypes)) {
            $customizeHeader->data_type = 'Integer';
        } elseif (in_array($request->ui_type_id, $imageUiTypes)) {
            $customizeHeader->data_type = 'Image';
        } elseif (in_array($request->ui_type_id, $multiSelectUiTypes)) {
            $customizeHeader->data_type = 'MultiSelect';
        }

        $customizeHeader->enable = 1;
        $customizeHeader->save();
        return $customizeHeader;
    }
}

if (!function_exists("updateCustomField")) {
    function updateCustomField($customizeHeader, $request, $code)
    {
        $customizeHeader->name = $request->name;
        $customizeHeader->placeholder = $request->placeholder;
        $customizeHeader->ui_type_id = $request->ui_type_id;

        $customizeHeader->core_keys_id = $customizeHeader->core_keys_id;
        if ($request->mandatory === false) {
            $customizeHeader->mandatory = 0;
        } else {
            $customizeHeader->mandatory = 1;
        }
        //        $key = CoreKeyType::where("name","product")->get();
        $customizeHeader->module_name = $code;
        $customizeHeader->enable = 1;
        $customizeHeader->update();

        return $customizeHeader;
    }
}

if (!function_exists("createCoreKey")) {
    function createCoreKey($customizeHeader, $code)
    {
        $coreKey = new CoreKey();
        $coreKey->core_keys_id = keyGenerate($code);
        $coreKey->name = $customizeHeader->name;
        $coreKey->description = $customizeHeader->name . ' desc';
        $coreKey->save();
        return $coreKey;
    }
}

if (!function_exists("updateCoreKey")) {
    function updateCoreKey($coreKey, $customizeHeader, $code)
    {
        $coreKey->core_keys_id = $customizeHeader->core_keys_id;
        $coreKey->name = $customizeHeader->name;
        $coreKey->description = $customizeHeader->name . ' desc';
        $coreKey->update();
        return $coreKey;
    }
}

if (!function_exists("createForHideShow")) {
    function createForHideShow($coreKey, $code)
    {
        $screenDisplayUiSetting = new ScreenDisplayUiSetting();
        $screenDisplayUiSetting->module_name = $code;
        $screenDisplayUiSetting->key = $coreKey->core_keys_id;
        $screenDisplayUiSetting->is_show = 1;
        $screenDisplayUiSetting->save();
        return $screenDisplayUiSetting;
    }
}


if (!function_exists("customFieldStatusUpdate")) {
    function customFieldStatusUpdate($customizeHeader, $columnName)
    {
        if ($customizeHeader->$columnName === 1) {
            $customizeHeader->$columnName = 0;
        } else {
            $customizeHeader->$columnName = 1;
        }
        $customizeHeader->update();
        return $customizeHeader;
    }
}

if (!function_exists("newFileName")) {
    function newFileName($value, $componentName = null)
    {
        $newName = uniqid() . "_" . $componentName . "." . $value->getClientOriginalExtension();
        return $newName;
    }
}

if (!function_exists("newFileNameForExport")) {
    function newFileNameForExport($fileName, $format = "csv")
    {
        $newName = $fileName . '_' . date('Y_m_d') . '.' . $format;
        return $newName;
    }
}



if (!function_exists("saveImgAsOrigin")) {
    function saveImgAsOrigin($file, $originPath, $fileName)
    {
        $img = Image::make($file);
        $origin = $originPath;
        $img->save($origin . $fileName, 30);
    }
}

if (!function_exists("saveImgAsThumbnail1x")) {
    function saveImgAsThumbnail1x($file, $thumbnail1xPath, $fileName)
    {
        $thumbnail1x = Image::make($file);
        $thumbnail1xDir = $thumbnail1xPath;
        $thumbnail1x->resize(200, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $thumbnail1x->save($thumbnail1xDir . $fileName);
    }
}


if (!function_exists("saveImgAsThumbnail2x")) {
    function saveImgAsThumbnail2x($file, $thumbnail2xPath, $fileName)
    {
        $thumbnail2x = Image::make($file);
        $thumbnail2xDir = $thumbnail2xPath;
        $thumbnail2x->resize(400, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $thumbnail2x->save($thumbnail2xDir . $fileName);
    }
}

if (!function_exists("saveImgAsThumbnail3x")) {
    function saveImgAsThumbnail3x($file, $thumbnail3xPath, $fileName)
    {
        $thumbnail3x = Image::make($file);
        $thumbnail3xDir = $thumbnail3xPath;
        $thumbnail3x->resize(600, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $thumbnail3x->save($thumbnail3xDir . $fileName);
    }
}

if (!function_exists("saveImgAsOriginalThumbNail1x2x3x")) {
    function saveImgAsOriginalThumbNail1x2x3x($file, $fileName, $originPath, $thumbnail1xPath, $thumbnail2xPath, $thumbnail3xPath)
    {

        //save origin
        saveImgAsOrigin($file, $originPath, $fileName);

        //save 1x
        saveImgAsThumbnail1x($file, $thumbnail1xPath, $fileName);

        //save 2x
        saveImgAsThumbnail2x($file, $thumbnail2xPath, $fileName);

        //save 3x
        saveImgAsThumbnail3x($file, $thumbnail3xPath, $fileName);
    }
}

if (!function_exists("delImageFromCustomFieldValue")) {
    function delImageFromCustomFieldValue($productRelation, $uploadPathForDel, $thumb1xPathForDel, $thumb2xPathForDel, $thumb3xPathForDel)
    {

        // delete all photos
        if (str_contains($productRelation->value, '.png') || str_contains($productRelation->value, '.jpg')) {
            Storage::delete($uploadPathForDel . $productRelation->value);
            Storage::delete($thumb1xPathForDel . $productRelation->value);
            Storage::delete($thumb2xPathForDel . $productRelation->value);
            Storage::delete($thumb3xPathForDel . $productRelation->value);
        }
    }
}

/**
 * Gets the generate_random_string
 *
 * @param      <type>  $id     The identifier
 * @param      <type>  $type   The type
 */
if (!function_exists('generate_random_string')) {
    function generate_random_string($length = 5)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

if (!function_exists('generateCoreKey')) {
    function generateCoreKey($code)
    {
        $conds['code'] = $code;
        $coreKeyCounter = CoreKeyCounter::where($conds)->first();
        $counter = $coreKeyCounter->counter + 1;

        $middleCoreKeyCode = Constants::middleCoreKeyCode;
        $middleCoreKeyCount = strlen($middleCoreKeyCode);
        $counterCount = strlen((string)$counter);

        $count = 0;
        if ($middleCoreKeyCount <= $counterCount) {
            $count = $counter;
        } else if ($middleCoreKeyCount > $counterCount) {
            $count = substr($middleCoreKeyCode, 0, ($middleCoreKeyCount - $counterCount) + 1) . $counter;
        }

        // update core key counter
        $data['counter'] = $counter;
        CoreKeyCounter::where('id', $coreKeyCounter->id)->update($data);

        return $code . $count;
    }
}

/**
 * Get Paid Item Status
 */
if (!function_exists('getPaidStatus')) {
    function getPaidStatus($start_timestamp, $end_timestamp)
    {
        $today_date = Carbon::now();

        $start_date = date('Y-m-d H:i:s', $start_timestamp);
        $end_date = date('Y-m-d H:i:s', $end_timestamp);

        if ($today_date >= $start_date && $today_date <= $end_date) {
            // dd("here");
            return Constants::paidItemProgressStatus;
        } else if ($today_date > $start_date && $today_date > $end_date) {
            return Constants::paidItemCompletedStatus;
        } else if ($today_date < $start_date && $today_date < $end_date) {
            return Constants::paidItemNotYetStartStatus;
        }
    }
}
/**
 * Sending Message From FCM For Android
 */
if (!function_exists('send_android_fcm')) {
    function send_android_fcm($registatoin_ids, $data, $platform_names)
    {
        // dd("gheere");
        $backend_setting = BackendSetting::first();

        $message = $data['message'];
        $flag = $data['flag'];

        if (isset($data['item_id'])) {
            $id = $data['item_id'];

            $item = Item::find($id);

            $title = $item->title;
            $item_name = str_replace(' ', '%20', $title);
            $item_approval_name = str_replace(' ', '-', $title);

            $price = $item->price;

            $currency_id = $item->currency_id;
            $cur = Currency::find($currency_id);
            $currency = $cur ? $cur->currency_symbol : '';

            $conds_img['img_parent_id'] = $id;
            $conds_img['img_type'] = "item";
            $conds_img['ordering'] = '1';
            $images = CoreImage::where($conds_img)->get();
            $img_path = count($images) > 0 ? $images[0]->img_path : '';

            if (count($images) == 0) {
                $conds_img1['img_parent_id'] = $id;
                $conds_img1['img_type'] = "item";

                $images1 = CoreImage::where($conds_img1)->get();

                if (count($images1) == 1) {
                    $img_path = $images1[0]->img_path;
                } else {
                    $img_path = count($images1) > 0 ? $images1[0]->img_path : '';
                }
            }

            // **** custom field *****
            // $condition_of_item_id = $item->condition_of_item_id;
            // $condition = Condition::find($condition_of_item_id)->name;
        }

        //to get prj name
        // $dyn_link_deep_url = $backend_setting->dyn_link_deep_url;
        // $prj_url = explode('/', $dyn_link_deep_url);
        // $i = count($prj_url) - 2;
        $prj_name = env('APP_URL');
        if (!str_ends_with($prj_name, '/')) {
            $prj_name = $prj_name . '/';
        }

        $click_action = "";

        foreach ($platform_names as $platform_name) {
            $currency_tmp =  '&currency=';
            $currency_tmp = htmlentities($currency_tmp);

            if (strtolower($platform_name) == "frontend" && $flag == Constants::chatNotiFlag) {
                //for chat chat?buyer_user_id=133&seller_user_id=1&item_id=192&chat_flag=CHAT_FROM_BUYER
                $click_action = $prj_name .  'chat?buyer_user_id=' . $data['buyer_user_id'] . '&seller_user_id=' . $data['seller_user_id'] . '&item_id=' . $data['item_id'] . '&chat_flag=' . $data['chat_flag'];
            } elseif (strtolower($platform_name) == "frontend" && $flag == Constants::reviewNotiFlag) {
                $click_action = $prj_name . 'review-list?user_id=' . $data['review_user_id'];
            } elseif (strtolower($platform_name) == "frontend" && $flag == Constants::approvalNotiFlag) {
                $click_action = $prj_name . 'fe_item?item_id=' . $data['item_id'];
            } elseif (strtolower($platform_name) == "frontend" && $flag == Constants::followNotiFlag) {
                $click_action = $prj_name . "profile";
            } elseif (strtolower($platform_name) == "frontend" && $flag == Constants::verifyBlueMarkNotiFlag) {
                $click_action = $prj_name . "profile";
            } elseif (strtolower($platform_name) == "android" || strtolower($platform_name) == "ios") {
                $click_action = ps_constant::flutterNotificationClick;
            } else if (strtolower($platform_name) == "frontend")
                $click_action = $prj_name . "";
            else {
                $click_action = ps_constant::flutterNotificationClick;
            }
        }

        if ($flag == Constants::approvalNotiFlag || $flag == Constants::verifyBlueMarkNotiFlag || $flag == Constants::followNotiFlag) {
            $noti_arr = array(
                'title' => __('site_name'),
                'body' => $message,
                'sound' => 'default',
                'message' => $message,
                'flag' => $flag,
                'click_action' => $click_action
            );

            $fields = array(
                'sound' => 'default',
                'notification' => $noti_arr,
                'registration_ids' => $registatoin_ids,
                'data' => array(
                    'message' => $message,
                    'flag' => $flag,
                    'click_action' => $click_action
                )

            );
        } elseif ($flag == Constants::reviewNotiFlag) {

            $rating = $data['rating'];

            $noti_arr = array(
                'title' => __('site_name'),
                'body' => $message,
                'sound' => 'default',
                'message' => $message,
                'flag' => 'review',
                'rating' => $rating,
                'click_action' => $click_action
            );

            $fields = array(
                'sound' => 'default',
                'notification' => $noti_arr,
                'registration_ids' => $registatoin_ids,
                'data' => array(
                    'message' => $message,
                    'rating' => $rating,
                    'flag' => 'review',
                    'click_action' => $click_action
                )

            );
        } else if ($flag == Constants::chatNotiFlag) {

            $message = $data['message'];
            $buyer_id = $data['buyer_user_id'];
            $seller_id = $data['seller_user_id'];
            $sender_name = $data['sender_name'];
            $item_id = $data['item_id'];
            $sender_profile_photo = $data['sender_profile_photo'];

            $noti_arr = array(
                'title' => __('site_name'),
                'body' => $message,
                'sound' => 'default',
                'message' => $message,
                'flag' => $flag,
                'buyer_id' => $buyer_id,
                'seller_id' => $seller_id,
                'item_id' => $item_id,
                'sender_name' => $sender_name,
                'sender_profile_photo' => $sender_profile_photo,
                'action' => "abc",
                'click_action' => $click_action
            );

            $fields = array(
                'sound' => 'default',
                'notification' => $noti_arr,
                'registration_ids' => $registatoin_ids,
                'data' => array(
                    'message' => $message,
                    'flag' => $flag,
                    'buyer_id' => $buyer_id,
                    'seller_id' => $seller_id,
                    'item_id' => $item_id,
                    'sender_name' => $sender_name,
                    'sender_profile_photo' => $sender_profile_photo,
                    'action' => "abc",
                    'click_action' => $click_action
                )
            );
        }

        // Update your Google Cloud Messaging API Key
        $fcm_api_key = !empty($backend_setting) ? $backend_setting->fcm_api_key : '';
        $google_api_key = $fcm_api_key;

        // //Google cloud messaging GCM-API url
        $url = 'https://fcm.googleapis.com/fcm/send';

        $headers = array(
            'Authorization: key=' . $google_api_key,
            'Content-Type: application/json'
        );
        $ch = curl_init();
        // dd($fields);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);

        return $result;
    }
}

if (!function_exists('send_subscribe_noti_fcm')) {
    function send_subscribe_noti_fcm($registatoin_ids, $data, $platform_names)
    {
        $backend_setting = BackendSetting::first();

        $message = $data['message'];
        $flag = $data['flag'];

        $click_action = "";

        $noti_arr = array(
            'title' => __('site_name'),
            'body' => $message,
            'sound' => 'default',
            'message' => $message,
            'flag' => $flag,
            'click_action' => $click_action
        );

        $fields = array(
            'sound' => 'default',
            'notification' => $noti_arr,
            'registration_ids' => $registatoin_ids,
            'data' => array(
                'message' => $message,
                'flag' => $flag,
                'click_action' => $click_action
            )

        );

        // Update your Google Cloud Messaging API Key
        $fcm_api_key = !empty($backend_setting) ? $backend_setting->fcm_api_key : '';
        // define("GOOGLE_API_KEY", $fcm_api_key);

        // //Google cloud messaging GCM-API url
        $url = 'https://fcm.googleapis.com/fcm/send';

        $headers = array(
            'Authorization: key=' . $fcm_api_key,
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);
        // dd($result);

        return $result;
    }
}

if (!function_exists('customPagination')) {

    function customPagination($items, $perPage = 10, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, [
            'path'  => request()->url(),
        ]);
    }
}


/**
 * Sending Message From FCM For Android & iOS By using topics subscribe
 */
if (!function_exists('send_android_fcm_topics_subscribe')) {
    function send_android_fcm_topics_subscribe($data)
    {
        // dd();
        $backend_setting = BackendSetting::first();

        $prj_name = env('APP_URL');
        if (!str_ends_with($prj_name, '/')) {
            $prj_name = $prj_name . '/';
        }

        $click_action = "";

        //Google cloud messaging GCM-API url
        $url = 'https://fcm.googleapis.com/fcm/send';

        if ($data['subscribe'] == '0' && $data['push'] == 1) {
            // push noti
            $click_action = $prj_name . "notification-list";

            $noti_arr = array(
                'title' => $data['message'],
                'body' => $data['desc'],
                'sound' => 'default',
                'flag' => ps_constant::broadcast
            );

            $noti_data = array(
                'sound' => 'default',
                'message' => $data['message'],
                'flag' => ps_constant::broadcast,
                'click_action' => $click_action
            );

            $fields = array(
                'sound' => 'default',
                'flag' => ps_constant::broadcast,
                'notification' => $noti_arr,
                'data' => $noti_data,
                'to' => '/topics/' . $backend_setting->topics
            );
        } else {
            // subscribe noti
            // $noti_arr = array(
            //     'title' => __('site_name'),
            //     'body' => $message,
            //     'sound' => 'default',
            //     'message' => $message,
            //     'flag' => $flag,
            //     'click_action' => $click_action
            // );

            // $fields = array(
            //     'sound' => 'default',
            //     'notification' => $noti_arr,
            //     'registration_ids' => $registatoin_ids,
            //     'data' => array(
            //         'message' => $message,
            //         'flag' => $flag,
            //         'click_action' => $click_action
            //     )

            // );
            $click_action = $prj_name . 'fe_item?item_id=' . $data['item_id'];
            $noti_arr = array(
                'title' => __('site_name'),
                'body' => $data['message'],
                'item_id' => $data['item_id'],
                'sound' => 'default',
                'flag' => Constants::subscribeNotiFlag
            );

            $noti_data = array(
                'sound' => 'default',
                'message' => $data['message'],
                'item_id' => $data['item_id'],
                'flag' => Constants::subscribeNotiFlag,
                'click_action' => $click_action
            );

            $fields = array(
                'sound' => 'default',
                'flag' => Constants::subscribeNotiFlag,
                // 'registration_ids' => $data['device_token'],
                'notification' => $noti_arr,
                'data' => $noti_data,
                'to' => '/topics/' . $data['subcategory_id'] . '_MB'
            );
        }

        $fcm_api_key = !empty($backend_setting) ? $backend_setting->fcm_api_key : '';
        // define("GOOGLE_API_KEY", $fcm_api_key);

        $headers = array(
            'Authorization: key=' . $fcm_api_key,
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);

        return $result;
    }
}

/**
 * Sending Message From FCM For Frontend By using topics subscribe
 */
if (!function_exists('send_android_fcm_topics_subscribe_fe')) {
    function send_android_fcm_topics_subscribe_fe($data, $prj_name)
    {
        $backend_setting = BackendSetting::first();

        $url = 'https://fcm.googleapis.com/fcm/send';

        if ($data['subscribe'] == '0' && $data['push'] == 1) {
            // push noti
            $noti_arr = array(
                'title' => $data['message'],
                'body' => $data['desc'],
                'sound' => 'default',
                'flag' => ps_constant::feBroadcast
            );

            $noti_data = array(
                'sound' => 'default',
                'message' => $data['message'],
                'flag' => ps_constant::feBroadcast,
                'click_action' => $prj_name . '/' . 'notification'
            );

            $fields = array(
                'sound' => 'default',
                'flag' => ps_constant::feBroadcast,
                'notification' => $noti_arr,
                'data' => $noti_data,
                'to' => '/topics/' . $backend_setting->topics_fe
            );
        } else {
            // subscribe noti

            // to get item name for FE click action
            $subscribeFlag = $data['subcategory_id'] . Constants::feSubscribeNotiFlag;
            $id = $data['item_id'];
            $title = Item::find($id)->title;
            $item_name = str_replace(' ', '%20', $title);
            $itm_name = str_replace(' ', '-', $title);
            $click_action = $prj_name . '/' . 'item/' . $itm_name . '?item_id=' . $data['item_id'] . '&item_name=' . $itm_name;

            $noti_arr = array(
                'title' => __('site_name'),
                'body' => $data['message'],
                'sound' => 'default',
                'item_id' => $id,
                'flag' => Constants::subscribeNotiFlag
            );

            $noti_data = array(
                'sound' => 'default',
                'message' => $data['message'],
                'item_id' => $id,
                'flag' => Constants::subscribeNotiFlag,
                'click_action' => $click_action
            );

            $fields = array(
                'sound' => 'default',
                'flag' => Constants::subscribeNotiFlag,
                'notification' => $noti_arr,
                'data' => $noti_data,
                'to' => '/topics/' . $subscribeFlag
            );
        }


        $fcm_api_key = !empty($backend_setting) ? $backend_setting->fcm_api_key : '';
        // define("GOOGLE_API_KEY", $fcm_api_key);


        $headers = array(
            'Authorization: key=' . $fcm_api_key,
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }
}

if (!function_exists('authorizationWithoutPolicy')) {

    function authorizationWithoutPolicy($moduleId)
    {
        return [
            "create" => permissionControl($moduleId, 1) ? true : false,
            "update" => permissionControl($moduleId, 3) ? true : false,
            "delete" => permissionControl($moduleId, 4) ? true : false
        ];
    }
}

if (!function_exists('vendorAuthorization')) {

    function vendorAuthorization($moduleId, $vendorId)
    {
        return [
            "create" => vendorPermissionControl($moduleId, 1, $vendorId) ? true : false,
            "update" => vendorPermissionControl($moduleId, 3, $vendorId) ? true : false,
            "delete" => vendorPermissionControl($moduleId, 4, $vendorId) ? true : false
        ];
    }
}

if (!function_exists("checkPurchasedCode")) {
    function checkPurchasedCode($response, $routeName = null)
    {

        if (empty($response->item)) {
            return redirect()->back()->with("purchased_code", "Envato Purchase Code is invalid")->withInput();
        }
    }
}

if (!function_exists("checkForDashboardPermission")) {
    function checkForDashboardPermission()
    {
        $havePermission = true;
        $haveNoPermission = false;

        if (Auth::check()) {
            $authUserId = Auth::id();
            $user = User::select("id", "role_id")->with(['user_permissions', 'role', 'role_permissions'])->where('id', $authUserId)->first();
            if($user->role->can_access_admin_panel){

                $userPermission = $user->user_permissions;
                $rolePermission = $user->role_permissions;

                if ($rolePermission->isNotEmpty()) {
                    return $havePermission;
                } else {
                    return $haveNoPermission;
                }
            } else {
                return $haveNoPermission;
            }
        }
    }
}


if (!function_exists('getHttpWithApiKey')) {
    function getHttpWithApiKey($baseUrl, $apiKey, $url, $para = null)
    {
        // if(!empty($para)){
        //     $responseData = json_decode(Http::get($baseUrl.$url.'?api_key='.$apiKey.$para));
        //     return $responseData;
        // } else {
        //     $responseData = json_decode(Http::get($baseUrl.$url.'?api_key='.$apiKey));
        //     return $responseData;
        // }
        try {
            $responseData = json_decode(Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
            ])->get($baseUrl . $url . '?' . $para));

            return $responseData;
        } catch (\Throwable $e) {
            $dataArr = json_decode(
                json_encode([
                    "status" => "error",
                    "message" => $e->getMessage()
                ])
            );
            return $dataArr;
        }
    }
}

if (!function_exists('postHttpWithApiKey')) {
    function postHttpWithApiKey($baseUrl, $apiKey, $url, $para = null, $data = null)
    {
        // try{
        //     $responseData = json_decode(Http::post($baseUrl.$url.'?api_key='.$apiKey,  $data));
        //     return $responseData;
        // } catch(\Throwable $e) {
        //     return $e->getMessage();
        // }
        try {
            $responseData = json_decode(Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
            ])->post($baseUrl . $url . '?' . $para, $data));

            return $responseData;
        } catch (\Throwable $e) {
            $dataArr = [
                "status" => "error",
                "message" => $e->getMessage()
            ];
            return $dataArr;
        }
    }
}

if (!function_exists('getApiKey')){
    function getApiKey(){
        $project = Project::select("api_key")->first();
        if(!empty($project)){
            $apiKey = $project->api_key;
            return $apiKey;
        }
    }
}

if (!function_exists('getProjectId')){
    function getProjectId(){
        $project = Project::select("id")->first();
        if(!empty($project)){
            $id = $project->id;
            return $id;
        }
    }
}

if (!function_exists('dateDiff')) {
    function dateDiff()
    {
        $apiCallSetting = ApiCallSetting::first();
        $duration = date_diff(Carbon::now(), $apiCallSetting->added_date);
        if ($duration->d == 0) {
            if ($duration->h >= $apiCallSetting->delay) {
                $apiCallSetting->added_date = Carbon::now();
                $apiCallSetting->update();
                return $status = true;
            } else {
                return $status = false;
            }
        } else {
            $apiCallSetting->added_date = Carbon::now();
            $apiCallSetting->update();
            return $status = true;
        }
    }
}

if (!function_exists('updateBuilderAppInfoCache')) {
    function updateBuilderAppInfoCache($request)
    {
        $builderAppInfoCache = BuilderAppInfoCache::first();
        if (isset($builderAppInfoCache)  && !empty($builderAppInfoCache)) {
            $cachedData = json_decode($builderAppInfoCache->cached_data);
            if (isset($request->isConnected)) {
                $cachedData->isConnected = $request->isConnected;
            }
            if (isset($request->isProjectChanged)) {
                $cachedData->isProjectChanged = $request->isProjectChanged;
            }
            if (isset($request->isValid)) {
                $cachedData->isValid = $request->isValid;
            }
            if (isset($request->syncAble)) {
                $cachedData->syncAble = $request->syncAble;
            }
            if (isset($request->versionCode)) {
                $cachedData->versionCode = $request->versionCode;
            }
            if (isset($request->latestVersion->version_code)) {
                $cachedData->versionCode = $request->latestVersion->version_code;
            }
            $cache = [
                'cached_data' => json_encode($cachedData)
            ];
            $builderAppInfoCache->update($cache);
        } else {
            $data = [
                "isConnected" => $request->isConnected,
                "isProjectChanged" => $request->isProjectChanged,
                "versionCode" => $request->latestVersion->version_code,
                "isValid" => $request->isValid,
                "syncAble" => $request->syncAble,
            ];
            $cache = [
                'cached_data' => json_encode($data)
            ];
            BuilderAppInfoCache::create($cache);
        }
    }
}

if (!function_exists('redirectBackWithError')) {
    function redirectBackWithError($dataArr)
    {
        return redirect()->back()->withErrors($dataArr);
    }
}


if (!function_exists('resultMessage')) {
    function resultMessage($msg, $status = "success")
    {
        $dataArr = [
            "status" => $status,
            "message" => $msg
        ];
        return $dataArr;
    }
}

if (!function_exists('syncTableFields')) {
    function syncTableFields($data)
    {
        try {
            $checkBuilderConnection = getHttpWithApiKey(ps_constant::base_url, getApiKey(), ps_url::checkBuilderConnection);
            // dd($checkBuilderConnection);
            if ($checkBuilderConnection?->status !== "success" || empty($checkBuilderConnection)) {

                $msg = $checkBuilderConnection?->message ? $checkBuilderConnection?->message : __('connection__failed');
                $dataArr = [
                    "status" => 'error',
                    "message" => $msg
                ];
            } else {
                $postdata = [
                    "id" => $data->id,
                    "project_id" => $data->project_id,
                    "table_id" => $data->table_id,
                    "core_keys_id" => $data->core_keys_id,
                    "field_name" => $data->field_name,
                    "ui_type_id" => $data->ui_type_id ? $data->ui_type_id : null,
                    "mandatory" => $data->mandatory,
                    "enable" => $data->enable,
                    "is_delete" => $data->is_delete,
                    // "base_module_name" => $data->base_module_name,
                    // "data_type" => $data->data_type,
                    "is_core_field" => $data->is_core_field,
                    "is_show_sorting" => $data->is_show_sorting,
                    "ordering" => $data->ordering,
                    "is_show_in_filter" => $data->is_show_in_filter,
                    "is_include_in_hideshow" => $data->is_include_in_hideshow,
                    "is_show" => $data->is_show,
                    "permission_for_enable_disable" => $data->permission_for_enable_disable,
                    "permission_for_delete" => $data->permission_for_delete,
                    "permission_for_mandatory" => $data->permission_for_mandatory,
                ];

                $syncResponse = postHttpWithApiKey(ps_constant::base_url, getApiKey(), ps_url::syncTaleField, "&project_id=" . getProjectId(), $postdata);

                // dd($syncResponse);

                if (empty($syncResponse)) {
                    $dataArr = [
                        "status" => 'error',
                        "message" => __('error__builder_synced')
                    ];
                }
                if ($syncResponse?->status !== "success") {

                    $dataArr = [
                        "status" => 'error',
                        "message" => $syncResponse->message
                    ];
                } else {
                    $dataArr = [
                        "status" => 'success',
                        "message" => __('api__builder_sync_success')
                    ];
                }
            }
        } catch (\Throwable $e) {
            $dataArr = [
                "status" => 'error',
                "message" => $e->getMessage()
            ];
        }

        // dd($dataArr);
        return $dataArr;
    }
}

if (!function_exists('findAndReplaceForBuildFolder')) {
    function findAndReplaceForBuildFolder($filePath, $searchContent, $replaceContent)
    {
        $file_contents = file_get_contents($filePath);
        $search = $searchContent;
        $replace = $replaceContent;
        $file_contents = str_replace($search, $replace, $file_contents);
        file_put_contents($filePath, $file_contents);

        return "success";
    }
}

if ( !function_exists('CheckPhpVersion')){
    function CheckPhpVersion(){
        $phpPathFromEnv = config("app.php_path");

        if(!empty($phpPathFromEnv)){
            $phpVersion = shell_exec($phpPathFromEnv.' -r "echo PHP_VERSION;"');

            if(empty($phpVersion)){
                $dataArr = [
                    "errMsg" => "This php path ($phpPathFromEnv) is wrong. You can find detailed instructions in our guide at",
                ];
                dd($dataArr);
            }

            return $phpPathFromEnv;
        } else {
            return "php";
        }
    }
}

if (!function_exists('isJsonDuplicate')) {
    function isJsonDuplicate($arrayName1, $arrayName2, $idToFind)
    {
        if (isset($data[$arrayName1][$arrayName2])) {
            // $idToFind = "1";
            $result = false;

            foreach ($data[$arrayName1][$arrayName2] as $item) {
                if ($item['id'] === $idToFind) {
                    $result = true;
                    break;
                }
            }

            return $result ? 'true' : 'false';
        } else {
            return 'false'; // item_price_type_list not found in JSON.
        }
    }
}

if (!function_exists('findNextId')) {
    function findNextId($array, $subArrayName1, $subArrayName2)
    {
        $existingIds = array_map(function ($item) {
            return intval($item["id"]);
        }, $array[$subArrayName1][$subArrayName2]);
        return strval(max($existingIds) + 1);
    }
}
if (!function_exists('addValueWithNewId')) {
    function addValueWithNewId($data, $subArrayName1, $subArrayName2,$new_values)
    {

        foreach ($new_values as $new_value) {
            // Add the new item to the list
            $newId = findNextId($data, $subArrayName1, $subArrayName2);
            $newItem = ["id" => $newId, "value" => $new_value['value']];
            $data[$subArrayName1][$subArrayName2][] = $newItem;
        }

        return $data;

        // Print the updated data
    }
}

if ( !function_exists('CheckPhpVersion')){
    function CheckPhpVersion(){
        $phpPathFromEnv = config("app.php_path");

        if(!empty($phpPathFromEnv)){
            $phpVersion = shell_exec($phpPathFromEnv.' -r "echo PHP_VERSION;"');

            if(empty($phpVersion)){
                $dataArr = [
                    "errMsg" => "This php path ($phpPathFromEnv) is wrong. You can find detailed instructions in our guide at",
                ];
                dd($dataArr);
            }

            return $phpPathFromEnv;
        } else {
            return "php";
        }
    }
}

if ( !function_exists('findFindWithHashKey')){
    function findFindWithHashKey($path){
        return File::glob($path);
    }
}

function getKeys($data, $keys) {
    $result = [];
    foreach ($keys as $key) {
        if (array_key_exists($key, $data)) {
            $result[$key] = $data[$key];
        }
    }
    return $result;
}

if ( !function_exists('takingForColumnProps')){
    function takingForColumnProps($label, $field, $type, $isShowSorting, $ordering){
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
}

if ( !function_exists('hiddenShownForCoreAndCustomField')){
    function hiddenShownForCoreAndCustomField($code){
        $hiddenShownForFields = ScreenDisplayUiSetting::with(['customizeField' => function ($q){
        $q->where(CustomizeUi::isDelete, ps_constant::unDelete);
        }, 'coreField'=> function($q) use ($code){
            $q->where(CoreFieldFilterSetting::moduleName, $code);
        }])
        ->where(CoreFieldFilterSetting::moduleName, $code)
        ->get();
        return $hiddenShownForFields;
    }
}

if ( !function_exists('takingForColumnFilterProps')){
    function takingForColumnFilterProps($id, $label, $field, $hidden, $moduleName, $keyId){
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

if ( !function_exists('takingForColumnAndFilterOption')){
    function takingForColumnAndFilterOption($code, $controlFieldArr = null, $keys = [])
    {

        // for table
        $hideShowCoreFieldForColumnArr = [];
        $hideShowCustomFieldForColumnArr = [];

        $showProductCols = [];

        // for eye
        $hideShowFieldForColumnFilterArr = [];

        $code = $code;
        $hideShowForCoreAndCustomFields = hiddenShownForCoreAndCustomField($code);
        // $shownForCoreAndCustomField = hiddenShownForCoreAndCustomField(ps_constant::show, $code);
        // $hideShowForCoreAndCustomFields = $shownForCoreAndCustomField->merge($hiddenForCoreAndCustomField);


        foreach ($hideShowForCoreAndCustomFields as $showFields) {
            if ($showFields->coreField !== null) {

                $label = $showFields->coreField->label_name;

                if (str_contains($showFields->coreField->field_name, "@@")) {
                    $afterNeedleField = strstr($showFields->coreField->field_name, "@@");
                    $afterNeedleField = str_replace("@@", "", $afterNeedleField);
                    $beforeNeedleField = strstr($showFields->coreField->field_name, "@@", 'true');
                    $field = $beforeNeedleField . "@@" . $afterNeedleField;
                } else {
                    $field = $showFields->coreField->field_name;
                }
                // $field = $showFields->coreField->field_name;
                $colName = $showFields->coreField->field_name;
                $type = $showFields->coreField->data_type;
                $isShowSorting = $showFields->coreField->is_show_sorting;
                $ordering = $showFields->coreField->ordering;

                //if subcategory is disabled
                if ($showFields->coreField->field_name == 'subcategory_id@@name' && MobileSetting::first()->is_show_subcategory != '1') {
                    continue;
                }

                $cols = $colName;
                $id = $showFields->id;
                $hidden = $showFields->is_show ? false : true;
                $moduleName = $showFields->coreField->module_name;
                $keyId = $showFields->coreField->id;

                $coreFieldObjForColumnsProps = takingForColumnProps($label, $field, $type, $isShowSorting, $ordering);
                $coreFieldObjForColumnFilter = takingForColumnFilterProps($id, $label, $field, $hidden, $moduleName, $keyId);

                array_push($hideShowFieldForColumnFilterArr, $coreFieldObjForColumnFilter);
                array_push($hideShowCoreFieldForColumnArr, $coreFieldObjForColumnsProps);
                array_push($showProductCols, $cols);
            }
            if ($showFields->customizeField !== null) {

                $label = $showFields->customizeField->name;
                $uiHaveAttribute = [ps_constant::dropDownUi, ps_constant::radioUi];
                if (in_array($showFields->customizeField->ui_type_id, $uiHaveAttribute)) {
                    $field = $showFields->customizeField->core_keys_id . "@@name";
                } else {
                    $field = $showFields->customizeField->core_keys_id;
                }
                if ($showFields->customizeField->ui_type_id == ps_constant::multiSelectUi) {
                    $type = "MultiSelect";
                } elseif ($showFields->customizeField->ui_type_id == ps_constant::imageUi) {
                    $type = "Image";
                } else {
                    $type = $showFields->customizeField->data_type;
                }
                $isShowSorting = $showFields->customizeField->is_show_sorting;
                $ordering = $showFields->customizeField->ordering;

                $id = $showFields->id;
                $hidden = $showFields->is_show ? false : true;
                $moduleName = $showFields->customizeField->module_name;
                $keyId = $showFields->customizeField->core_keys_id;

                $customFieldObjForColumnsProps = takingForColumnProps($label, $field, $type, $isShowSorting, $ordering);
                $customFieldObjForColumnFilter = takingForColumnFilterProps($id, $label, $field, $hidden, $moduleName, $keyId);

                array_push($hideShowFieldForColumnFilterArr, $customFieldObjForColumnFilter);
                array_push($hideShowCustomFieldForColumnArr, $customFieldObjForColumnsProps);
            }
        }

        // for columns props
        $showCoreAndCustomFieldArr = array_merge($hideShowCoreFieldForColumnArr, $controlFieldArr, $hideShowCustomFieldForColumnArr);
        // dd($showCoreAndCustomFieldArr);
        $sortedColumnArr = columnOrdering("ordering", $showCoreAndCustomFieldArr);


        // for eye
        $hideShowCoreAndCustomFieldArr = array_merge($hideShowFieldForColumnFilterArr);

        $dataArr = [
            ps_constant::handlingColumn => $sortedColumnArr,
            ps_constant::handlingFilter => $hideShowCoreAndCustomFieldArr,
            ps_constant::showCoreField => $showProductCols,
        ];
        if(count($keys) !== 0){
            $dataArr = getKeys($dataArr, $keys);
        }
        return $dataArr;
    }
}
if(!function_exists('repairVue')){
    function repairVue(){
        $pathArr = findFindWithHashKey(public_path(ps_constant::appJSFilePath));
        if(count($pathArr) !== 0){
            $path_to_file = $pathArr[0];
        }

        $pathArr2 = findFindWithHashKey(public_path(ps_constant::PsApiServiceJSFilePath));
        if(count($pathArr2) !== 0){
            $path_to_file2 = $pathArr2[0];
        }

        $pathArr3 = findFindWithHashKey(public_path(ps_constant::psApiServiceJSFilePath));
        if(count($pathArr3) !== 0){
            $path_to_file3 = $pathArr3[0];
        }

        if(empty(config("app.dir"))){
            $domainSubFolderBuild = ps_constant::searchDomain . ps_constant::searchSubFolder . '/' . 'build' . '/';
            $domainSubFolder = ps_constant::searchDomain . ps_constant::searchSubFolder;
            $envDomainBuild = config("app.base_domain") . 'build' . '/';

            findAndReplaceForBuildFolder($path_to_file, $domainSubFolderBuild, $envDomainBuild);
            findAndReplaceForBuildFolder($path_to_file, $domainSubFolder, config("app.base_domain"));
            findAndReplaceForBuildFolder($path_to_file, ps_constant::searchDomain, config("app.base_domain"));
            findAndReplaceForBuildFolder($path_to_file, ps_constant::searchSubFolderWithSlash1, "");
            findAndReplaceForBuildFolder($path_to_file, ps_constant::searchSubFolderWithSlash2, "");
            findAndReplaceForBuildFolder($path_to_file, ps_constant::searchSubFolder, "");

            findAndReplaceForBuildFolder($path_to_file2, ps_constant::searchApiToken, config("app.api_token"));
            findAndReplaceForBuildFolder($path_to_file2, $domainSubFolderBuild, $envDomainBuild);
            findAndReplaceForBuildFolder($path_to_file2, $domainSubFolder, config("app.base_domain"));
            findAndReplaceForBuildFolder($path_to_file2, ps_constant::searchDomain, config("app.base_domain"));
            findAndReplaceForBuildFolder($path_to_file2, ps_constant::searchSubFolderWithSlash1, "");
            findAndReplaceForBuildFolder($path_to_file2, ps_constant::searchSubFolderWithSlash2, "");
            findAndReplaceForBuildFolder($path_to_file2, ps_constant::searchSubFolder, "");

            findAndReplaceForBuildFolder($path_to_file3, $domainSubFolderBuild, $envDomainBuild);
            findAndReplaceForBuildFolder($path_to_file3, $domainSubFolder, config("app.base_domain"));
            findAndReplaceForBuildFolder($path_to_file3, ps_constant::searchDomain, config("app.base_domain"));
            findAndReplaceForBuildFolder($path_to_file3, ps_constant::searchSubFolderWithSlash1, "");
            findAndReplaceForBuildFolder($path_to_file3, ps_constant::searchSubFolderWithSlash2, "");
            findAndReplaceForBuildFolder($path_to_file3, ps_constant::searchSubFolder, "");
        } else {
            findAndReplaceForBuildFolder($path_to_file, ps_constant::searchDomain, config("app.base_domain"));
            findAndReplaceForBuildFolder($path_to_file, ps_constant::searchSubFolder, config("app.dir"));

            findAndReplaceForBuildFolder($path_to_file2, ps_constant::searchApiToken, config("app.api_token"));
            findAndReplaceForBuildFolder($path_to_file2, ps_constant::searchDomain, config("app.base_domain"));
            findAndReplaceForBuildFolder($path_to_file2, ps_constant::searchSubFolder, config("app.dir"));

            findAndReplaceForBuildFolder($path_to_file3, ps_constant::searchDomain, config("app.base_domain"));
            findAndReplaceForBuildFolder($path_to_file3, ps_constant::searchSubFolder, config("app.dir"));
        }
    }
}

if ( !function_exists('getLogCode')){
    function getLogCode(){
        $logChange = LogChange::first();
        $logCode = !empty($logChange) ? $logChange->code : null;
        return $logCode;
    }
}

if ( !function_exists('handleValidation')){
    function handleValidation($errors, $coreFields, $validations){

        $customFieldValidationArr = [];
        $customFieldAttributeArr = [];
        foreach($errors as $key => $error){
            $rules = 'required';
            $customFieldValidationArr[$key] = $rules;
            $customField = CustomizeUi::where("core_keys_id", $key)->first();
            $customFieldAttributeArr[$key] = __($customField->name);
        }
        $coreFieldsIds = [];

        $cond['module_name'] = Constants::item;
        $cond['mandatory'] = 1;
        $cond['is_core_field'] = 1;
        $cond['enable'] = 1;



        foreach ($coreFields as $key => $value){
            if (str_contains($value->field_name,"@@")) {
                $originFieldName = strstr($value->field_name,"@@",true);
            } else {
                $originFieldName = $value->field_name;
            }
            array_push($coreFieldsIds,$originFieldName);

        }

        $coreFieldValidationArr = [];

        foreach($validations as $validation){
                if(in_array($validation['fieldName'],$coreFieldsIds)){
                    $coreFieldValidationArr[$validation['fieldName']] = $validation['rules'];
                } else {
                    $coreFieldValidationArr[$validation['fieldName']] = 'nullable';
                }
        }
        $validationArr = array_merge($coreFieldValidationArr, $customFieldValidationArr);
        return $validationArr;
    }
}

if ( !function_exists('handleCFAttrForValidation')){
    function handleCFAttrForValidation($moduleName, $reqValForCF){
        $errors = validateForCustomField($moduleName, $reqValForCF);
        $customFieldAttributeArr = [];
        foreach($errors as $key => $error){
            $customField = CustomizeUi::where("core_keys_id", $key)->first();
            $customFieldAttributeArr[$key] = __($customField->name);

        }
        return $customFieldAttributeArr;
    }
}

if ( !function_exists('makeColumnHideShown')){
    function makeColumnHideShown($request){
        $hideShowFields = $request->value;
        foreach ($hideShowFields as $hideShowField){
            $hideShowFieldData[] = [
                'id' => $hideShowField['id'],
                'is_show' => $hideShowField['hidden'] ? Constants::hide : Constants::show,
            ];
        }
        ScreenDisplayUiSetting::upsert(
            $hideShowFieldData,
            ['id'], ['is_show']
        );
        return "success";
    }
}

if ( !function_exists('noDataError')){
    function noDataError($offset, $data){
        if ($offset > 0 && $data->isEmpty()) {
            // no paginate data
            $data = [];
            return responseDataApi($data);

        } else if($data->isEmpty()) {
            // no data db
            return responseMsgApi(__('core__no_data'), Constants::noContentStatusCode, Constants::successStatus);
        }
    }
}

if ( !function_exists('condsForCoreFieldValidation')){
    function condsForCoreFieldValidation($module){
        $cond['module_name'] = $module;
        $cond['mandatory'] = 1;
        $cond['is_core_field'] = 1;
        $cond['enable'] = 1;
        return $cond;
    }
}

if ( !function_exists('setMeta')){
    function setMeta($title, $description, $image){
        $psService = new PsService();
        $imagePath = '';
        if(is_array($image)){
            $img = $image = CoreImage::where($image)->orderBy('id','desc')->first();;
            if($img){
                $imagePath = $img->img_path;
            }

        }else{
            $imagePath = $image;
        }
        if($title != null ){
            $psService::addMeta('title', $title);
            $psService::addMeta('description', $description);
            $psService::addMeta('image', $imagePath);
        }
    }
}

if (!function_exists('isVendorSettingOn')) {
    function isVendorSettingOn()
    {
        $backend_setting = BackendSetting::select('vendor_setting')->first();

        if($backend_setting && $backend_setting->vendor_setting == '1'){
            return true;
        }
        return false;
    }
}

if (!function_exists('deleteOldSessions')) {
    function deleteOldSessions()
    {
        $expireTime = ps_config::sessionExpiredTime;

        // Calculate the timestamp for the expiration time
        $expirationTimestamp = Carbon::now()->subHours($expireTime)->getTimestamp();

        // Manually execute SQL query to delete rows
        DB::table('psx_custom_sessions')->where('last_activity', '<', $expirationTimestamp)->delete();
    }
}

if (!function_exists('updateSessionLastActivity')) {
    function updateSessionLastActivity()
    {
        $currentSessionId = session()->getId();

        DB::table('psx_custom_sessions')->where('id','=',$currentSessionId)->update(
            [

                'last_activity' => Carbon::now()->getTimestamp(),

            ]
        );
    }
}

if (!function_exists('productCountByVendorId')) {
    function productCountByVendorId($vendorId)
    {
        return Item::where('vendor_id', $vendorId)->where('status', Constants::publishItem)->count();
    }
}

if (!function_exists('convertMonthFromStringToNumber')) {
    function convertMonthFromStringToNumber($monthByNumber)
    {
       switch ($monthByNumber) {
            case 1:
                return "One Month";
                break;
            case 2:
                return "Two Months";
                break;
            case 3:
                return "Three Months";
                break;
            case 4:
                return "Four Months";
                break;
            case 5:
                return "Five Months";
                break;
            case 6:
                return "Six Months";
                break;
            case 12:
                return "One Year";
                break;
            default:
                return "NA";
        }
    }
}


if(!function_exists('availableQuantityFromItem')){
    function availableQuantityFromItem($itemId){
        $getItemInfo = ItemInfo::where(ItemInfo::itemId, $itemId)
        ->where(ItemInfo::coreKeysId, 'ps-itm00010')
        ->first();
        $availableQuantity = (int)$getItemInfo->value;
        return $availableQuantity;
    }

}
