<?php

namespace App\Http\Middleware;

use App\Config\ps_url;
use Inertia\Middleware;
use App\Config\ps_constant;
use Illuminate\Http\Request;
use App\Http\Services\PsService;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Language;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Modules\Core\Entities\CoreImage;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Constants\Constants;
use Laravel\Sanctum\PersonalAccessToken;
use Modules\Core\Entities\CoreMenuGroup;
use Modules\Core\Entities\VendorMenuGroup;
use Modules\Core\Entities\VendorSubMenuGroup;
use Modules\Core\Entities\VendorRolePermission;
use Modules\Core\Entities\VendorUserPermission;
use Modules\Core\Entities\VendorModule;
use Modules\Core\Entities\VendorMenu;

use Modules\Core\Entities\MobileSetting;
use Modules\Core\Entities\BackendSetting;
use Modules\Core\Entities\FrontendSetting;
use Modules\Core\Entities\CoreSubMenuGroup;
use Modules\Core\Entities\ActivatedFileName;
use Modules\Core\Entities\CheckVersionUpdate;
use Modules\Core\Entities\BuilderAppInfoCache;
use Modules\Core\Entities\Vendor;
use Modules\Core\Entities\LogChange;
use Modules\Core\Entities\Setting;
use stdClass;
use Modules\Core\Entities\VendorRole;
use Illuminate\Support\Facades\DB;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function firbaseConfig($frontendSetting)
    {

        $firebaseConfig = new \stdClass();
        $firebaseConfig->apiKey = '000000000000000000000000000000000000000';
        $firebaseConfig->authDomain = 'sooq.firebaseapp.com';
        $firebaseConfig->databaseURL = 'https://sooq.firebaseio.com';
        $firebaseConfig->projectId = 'sooq';
        $firebaseConfig->storageBucket = 'sooq.appspot.com';
        $firebaseConfig->messagingSenderId = '000000000000';
        $firebaseConfig->appId = '1:000000000000:web:0000000000000000000000';
        $firebaseConfig->measurementId = 'G-0000000000';

        $firebaseConfig = json_encode($firebaseConfig);
        $webPushKey = $frontendSetting->firebase_web_push_key_pair;

        $firebaseConfigStr = $frontendSetting->firebase_config;
        if($frontendSetting->firebase_config == null || $frontendSetting->firebase_config == ''){
            $firebaseConfigStr = $firebaseConfig;
        }else{

            $firebaseConfigObj = json_decode($firebaseConfigStr);
            if (!is_object($firebaseConfigObj) || !isset($firebaseConfigObj->apiKey)) {
                $firebaseConfigStr = $firebaseConfig;
            }
        }
        return $firebaseConfigStr;
    }

    public function imagePaths()
    {
        $imagePathObj = new \stdClass();
        $imagePathObj->folder_path_uploads= 'storage/' .Constants::folderPath. '/uploads';
        $imagePathObj->folder_path_thumbnail1x= 'storage/' .Constants::folderPath. '/thumbnail';
        $imagePathObj->folder_path_thumbnail2x= 'storage/' .Constants::folderPath. '/thumbnail2x';
        $imagePathObj->folder_path_thumbnail3x='storage/' .Constants::folderPath. '/thumbnail3x';
        return $imagePathObj;
    }

    public function getDomain()
    {
        $dir = config("app.dir");
        if(!empty($dir)){
            $domain = config("app.url").'/';
        } else {
            $domain = config("app.url");
        }
        return $domain;
    }

    public function forBE($mobileSetting, $psService)
    {
        $forBE = [];
        $currentUrl = url()->current();
        $domain = $this->getDomain();

        $isBE = str_starts_with(substr($currentUrl, strlen($domain)), "admin");
        if($isBE){

            if(Schema::hasTable('psx_check_version_updates')){
                $checkVersionUpdate = CheckVersionUpdate::first();
            } else {
                $checkVersionUpdate = null;
            }

            $project = Project::first();
            $builderToken = "builderToken";
            $personalAccessTokens = PersonalAccessToken::where('abilities','like','%'.$builderToken.'%')->pluck('token')->toArray();
            if(!empty($project->ps_license_code) && !empty($project->api_key) && dateDiff() && !empty($project->first_time_sync)){
                // delete vendor sessions when admin panel login [once in 3 hr]
                deleteOldSessions();
                try{
                    $builderAppInfo = getHttpWithApiKey(ps_constant::base_url, getApiKey(), ps_url::builderAppInfo,
                        'project_id='.$project->id.
                        '&project_url='.$project->project_url.
                        '&project_code='.$project->project_code.
                        '&is_publish='.ps_constant::isPublish.
                        '&log_code='.getLogCode()
                    );
                    if(!empty($personalAccessTokens)){
                        $isConnected = 0;
                        foreach($personalAccessTokens as $personalAccessToken){
                            $tokensMatch  = hash_equals($personalAccessToken, hash('sha256',$builderAppInfo->token)) ?? 1;
                            if($tokensMatch){
                                $isConnected = 1;
                            }
                        }
                    }else{
                        $isConnected = 0;
                    }
                    $builderAppInfo->isConnected = $isConnected;
                    updateBuilderAppInfoCache($builderAppInfo);
                }catch(\Throwable $e){
                    $builderAppInfo = null;
                }

            }else if(!empty($project->ps_license_code) && !empty($project->api_key) && !dateDiff() && !empty($project->first_time_sync)){
                $builderAppInfoCache = BuilderAppInfoCache::first();
                if(isset($builderAppInfoCache)){
                    $cache = json_decode($builderAppInfoCache->cached_data);
                    $builderAppInfo = new \stdClass();
                    $versionCode = new \stdClass();
                    $versionCode->version_code = $cache->versionCode;
                    $builderAppInfo->isConnected = $cache->isConnected;
                    $builderAppInfo->isProjectChanged = $cache->isProjectChanged;
                    $builderAppInfo->latestVersion = $versionCode;
                    $builderAppInfo->isValid = $cache->isValid;
                    $builderAppInfo->syncAble = $cache->syncAble;
                }else{
                    $builderAppInfo = null;
                }
            }else{
                $builderAppInfo = null;
            }

            $setting = Setting::where('setting_env', Constants::SYSTEM_CONFIG)->first();
            $selcted_array = json_decode($setting->setting, true);

            $forBE = [
                "checkVersionUpdate" => $checkVersionUpdate ? $checkVersionUpdate : null,
                "builderAppInfo" => $builderAppInfo,
                "isSubCategoryOn" => $mobileSetting->is_show_subcategory,
                'selected_price_type' => (string) $selcted_array['selected_price_type']['id'],
                'systemCode2' => $psService->getSystemCode(),
                'activatedFileName' => ActivatedFileName::first(),
                'purchaseCodeOrUrlNotSame' => session('purchaseCodeOrUrlNotSame'),
                'builderApiOverwrite' => session('builderApiOverwrite'),
                'replace_status' => session('replace_status'),
                'replace_message' => session('replace_message'),
                'zipFileName' => session('zipFileName') ? session('zipFileName') : session('zipFileName2'),
                'selectedZipFileName' => session('selectedZipFileName'),
                'downloadStatus' => session('downloadStatus'),
                'activateStatus' => session('activateStatus'),
                'autoSyncStatus' => session('autoSyncStatus'),
                'autoSyncMessage' => session('autoSyncMessage'),
                'project' => $project,
                'purchased_code' => session('purchased_code') ? session('purchased_code') : '',
                'product_relation_errors' => session('product_relation_errors') ? session('product_relation_errors') : '',
                'user_relation_errors' => session('user_relation_errors') ? session('user_relation_errors') : '',
                'city_relation_errors' => session('city_relation_errors') ? session('city_relation_errors') : '',
                'menuGroups' => CoreMenuGroup::where('is_show_on_menu',1)->with('sub_menu_group')->has('sub_menu_group.module')->orderBy('ordering', 'asc')->get(),
                'subMenuGroups' => CoreSubMenuGroup::with('module')->whereNull('core_menu_group_id')->orderBy('ordering', 'asc')->get(),
                'importSuccessMsg' => session('importSuccessMsg'),
                'diffIds' => session('diffIds'),
                'importedFileName' => session('importedFileName'),
                'recentImportedFileName' => session('recentImportedFileName'),
                'baseProjectNotSameMsg' => session('baseProjectNotSameMsg'),
                'baseProjectSameMsg' => session('baseProjectSameMsg'),
                'defaultBuilderToken' => session('defaultBuilderToken'),
                'can' => [
                    'createItem' => Auth::check() ? auth()->user()->can('create-item') : '',
                    'createProduct' => Auth::check() ? auth()->user()->can('create-product') : '',
                   'createSubcategory' => Auth::check() ? auth()->user()->can('create-subCategory') : '',
                    'createCategory' => Auth::check() ? auth()->user()->can('create-category') : '',
                    'createUser' => Auth::check() ? auth()->user()->can('create-user') : '',
                    'createRole' => Auth::check() ? auth()->user()->can('create-role') : '',
                    'createPayment' => Auth::check() ? auth()->user()->can('create-payment') : '',

                    'createAvailableCurrency' => Auth::check() ? auth()->user()->can('create-availableCurrency') : '',
                    'createLanguage' => Auth::check() ? auth()->user()->can('create-language') : '',
                    'createLanguageString' => Auth::check() ? auth()->user()->can('create-languageString') : '',
                    'createBlog' => Auth::check() ? auth()->user()->can('create-blog') : '',
                    'createPhoneCountryCode' => Auth::check() ? auth()->user()->can('create-phoneCountryCode') : '',
                    'createMobileSetting' => Auth::check() ? auth()->user()->can('create-mobileSetting') : '',
                    'createApiToken' => Auth::check() ? auth()->user()->can('create-apiToken') : '',
                    'createCurrency' => Auth::check() ? auth()->user()->can('create-currency') : '',
                    'createCoreMenu' => Auth::check() ? auth()->user()->can('create-coreMenuGroup') : '',
                    'createSystemConfig' => Auth::check() ? auth()->user()->can('create-systemConfig') : '',
                    'createCoreSubMenu' => Auth::check() ? auth()->user()->can('create-coreSubMenuGroup') : '',
                    'createCoreModule' => Auth::check() ? auth()->user()->can('create-coreModule') : '',
                    'createLocationTownship' => Auth::check() ? auth()->user()->can('create-locationTownship') : '',
                    'createLocationCity' => Auth::check() ? auth()->user()->can('create-locationCity') : '',
                    'createContactUsMessage' => Auth::check() ? auth()->user()->can('create-contactUsMessage') : '',
                    'createPushNotificationMessage' => Auth::check() ? auth()->user()->can('create-pushNotificationMessage') : '',
                    'createMobileLanguage' => Auth::check() ? auth()->user()->can('create-mobileLanguage') : '',
                    'createMobileLanguageString' => Auth::check() ? auth()->user()->can('create-mobileLanguageString') : '',
                    'createPackageReport' => permissionControl(Constants::packageReportModule, ps_constant::createPermission) ? true : false,

                    'createModule' => Auth::check() ? auth()->user()->can('create-module') : '',
                    'updateBackendSetting' => permissionControl(Constants::backendSettingModule, ps_constant::updatePermission) ? true : false,


                    'deleteDataReset' => permissionControl(\Modules\DemoDataDeletion\Constants\Constants::dataReset, ps_constant::deletePermission) ? true : false,
                    'updateContact' => permissionControl(Constants::contactModule, ps_constant::updatePermission) ? true : false,
                    'deleteContact' => permissionControl(Constants::contactModule, ps_constant::deletePermission) ? true : false,
                    'updateGenerateDeepLink' => permissionControl(Constants::DeepLinkGenerateModule, ps_constant::updatePermission) ? true : false,
                    'updatePaymentSetting' => permissionControl(Constants::paymentSettingModule, ps_constant::updatePermission) ? true : false,
                    'createTable' => Auth::check() ? auth()->user()->can('create-table') : '',
                    'createCustomizeUiDetail' => Auth::check() ? auth()->user()->can('create-customizeUiDetail') : '',
                    'createPrivacyModule' => Auth::check() ? auth()->user()->can('create-privacyModule') : '',
                    'createDataDeletionModule' => Auth::check() ? auth()->user()->can('create-dataDeletionModule') : '',

                    // for frontend language string
                    'createFeLanguageString' => Auth::check() ? auth()->user()->can('create-feLanguageString') : '',
                    'createVendorLanguageString' => Auth::check() ? auth()->user()->can('create-vendorLanguageString') : '',
                    'createVendorMenuGroup' => Auth::check() ? auth()->user()->can('create-vendorMenuGroup') : '',
                    'createVendorMenu' => Auth::check() ? auth()->user()->can('create-vendorMenu') : '',
                    'createVendorModule' => Auth::check() ? auth()->user()->can('create-vendorModule') : '',
                    'createVendorRole' => Auth::check() ? auth()->user()->can('create-vendorRole') : '',
                    'createVendorSubMenuGroup' => Auth::check() ? auth()->user()->can('create-vendorSubMenuGroup') : '',

                    // for vendor
                    'createVendor' => Auth::check() ? auth()->user()->can('create-vendor') : '',
                ],
            ];
        }

        return $forBE;
    }

    public function forFE()
    {
        $forFE = [];
        $currentUrl = url()->current();
        $domain = $this->getDomain();
        $isFE = str_starts_with(substr($currentUrl, strlen($domain)), "");

        if($isFE){
            $forFE = [];
        }

        return $forFE;
    }

    public function forVendor($vendorIds)
    {
        $forVendor = [];
        $currentUrl = url()->current();
        $domain = $this->getDomain();
        $isVendor = str_starts_with(substr($currentUrl, strlen($domain)), "vendor-panel");

        if($isVendor){
            //for vendor sidebar
            $vendorMenuGroups = [];
            $vendorSubMenuGroups = [];

            $vendorId = getVendorIdFromSession();
            if($vendorId != null){
                $vendorRole = VendorUserPermission::where('user_id', Auth::id())->first();
                if($vendorRole){
                    $vendorRoleObj = json_decode($vendorRole->vendor_and_role);
                    if(isset($vendorRoleObj->$vendorId)){
                        $getRoleIds = explode(',', $vendorRoleObj->$vendorId);
                        $roleIds = VendorRole::whereIn('id', $getRoleIds)
                            ->where('status', 1)
                            ->pluck('id')
                            ->toArray();

                        $rowPermissions = VendorRolePermission::whereIn('vendor_role_id', $roleIds)->get();
                        $moduleIds = [];
                        foreach($rowPermissions as $rowPermission){
                            $moduleObj = json_decode($rowPermission->module_and_permission);
                            $allKeysOfmoduleObj = array_keys((array)$moduleObj);
                            foreach($allKeysOfmoduleObj as $tempKey){
                                if (!in_array($tempKey, $moduleIds)) {
                                    array_push($moduleIds, $tempKey);
                                }
                            }

                        }


                        $dropDownSubMenuIds = VendorSubMenuGroup::where("is_dropdown", 1)->get()->pluck("id");
                        $linkSubMenuIds = VendorModule::whereIn('id', $moduleIds)->where('sub_menu_id', '!=', 0)->where('sub_menu_id', '!=', null)->get()->pluck("sub_menu_id");
                        $menuIds = VendorModule::whereIn('id', $moduleIds)->where('menu_id', '!=', 0)->where('menu_id', '!=', null)->get()->pluck("menu_id");

                        $vendorMenus = VendorMenu::where('is_show_on_menu', 1)->whereIn('id', $menuIds)->with('routeName')->orderBy('ordering', 'asc')->get();

                        $subMenuIds = $dropDownSubMenuIds->merge($linkSubMenuIds);
                        $allSubMenuIds = $subMenuIds->merge($vendorMenus->pluck("core_sub_menu_group_id"));

                        $vendorSubMenuGroupArr = VendorSubMenuGroup::whereIn('id',$allSubMenuIds)->where('is_show_on_menu', 1)->with(['icon', 'routeName'])->orderBy('ordering', 'asc')->get();

                        $vendorSubMenuGroups = json_decode(json_encode($vendorSubMenuGroupArr));
                        foreach($vendorSubMenuGroups as $vendorSubMenuGroup){
                            $vendorSubMenuGroup->module = [];
                            foreach($vendorMenus as $vendorMenu){
                                if($vendorMenu->core_sub_menu_group_id == $vendorSubMenuGroup->id){
                                    array_push($vendorSubMenuGroup->module, $vendorMenu);
                                }


                            }
                        }
                        $vendorMenuGroupsArr = VendorMenuGroup::whereIn('id',$vendorSubMenuGroupArr->pluck("core_menu_group_id"))->where('is_show_on_menu',1)->orderBy('ordering', 'asc')->get();

                        $vendorMenuGroups = json_decode(json_encode($vendorMenuGroupsArr));
                        foreach($vendorMenuGroups as $vendorMenuGroup){
                            $vendorMenuGroup->sub_menu_group = [];
                            $hasData = false;
                            foreach($vendorSubMenuGroups as $vendorSubMenuGroup){
                                if(!($vendorSubMenuGroup->is_dropdown == '1' && count($vendorSubMenuGroup->module) == 0) && $vendorSubMenuGroup->core_menu_group_id == $vendorMenuGroup->id){
                                    array_push($vendorMenuGroup->sub_menu_group, $vendorSubMenuGroup);
                                }
                            }
                        }
                    }

                }
            }

            $vendorList = [];
            $currentVendor = null;
            if(count($vendorIds) > 0){
                $vendorList = Vendor::whereIn('id', $vendorIds)->where('status', Constants::vendorAcceptStatus)->with(['logo'])->get();

                $currentVendor = $vendorList->filter(function ($vendor) use ($vendorId) {
                    return $vendor->id == $vendorId;
                    })->first();
            }


            $forVendor = [
                'vendorList' => $vendorList,
                'currentVendor' => $currentVendor,
                'vendorMenuGroups' => $vendorMenuGroups,
                'storeCan' => [
                    'updateMyVendor' => vendorPermissionControl(Constants::vendorStoreModule, ps_constant::updatePermission, getVendorIdFromSession()) ? true : false,
                    'createPaymentStatus' => vendorPermissionControl(Constants::vendorPaymentStatusModule, ps_constant::createPermission, getVendorIdFromSession()) ? true : false,
                    'createOrderStatus' => vendorPermissionControl(Constants::vendorOrderStatusModule, ps_constant::createPermission, getVendorIdFromSession()) ? true : false,
                ]

            ];
        }

        return $forVendor;
    }

    public function forAll($frontendSetting, $backendSetting, $mobileSetting, $imagePathsObj, $vendorIds)
    {
        $route = Route::current();
        $name = $route->getName();
        $firebaseConfigStr = $this->firbaseConfig($frontendSetting);


        $canAccessVendor = false;
        if(count($vendorIds) > 0 && $backendSetting->vendor_setting == '1'){
            $canAccessVendor = true;
        }

        $forAll = [
            "canAccessAdminPanel" => checkForDashboardPermission(),
            'vendor_id_sidebar' => session('vendor_id_sidebar'),
            "canAccessVendor" =>  $canAccessVendor,
            "firebaseConfig" => $firebaseConfigStr,
            "webPushKey" => $frontendSetting->firebase_web_push_key_pair,
            "langStatus" => session('langStatus'),
            'dateFormat' => $backendSetting->date_format,
            'mapKey' => $backendSetting->map_key,
            'backendSetting' => $backendSetting,
            'mobileSetting' => $mobileSetting,
            'vendorSetting' => $backendSetting->vendor_setting == '1',
            'hasError' => session('hasError'),
            'message' => session('message'),
            'logMessages' => session('logMessages'),
            'currentRoute' => $name,
            'defaultProfileImg' => asset('/images/assets/default_profile.png'),
            'authUser' => Auth::user(),
            'languages' => Language::get(),
            'backendLogo' => CoreImage::where('img_type', 'backend-logo')->first(),
            'favIcon' => CoreImage::where('img_type', 'fav-icon')->first(),
            'uploadUrl' => asset($imagePathsObj->folder_path_uploads),
            'thumb1xUrl' => asset($imagePathsObj->folder_path_thumbnail1x),
            'thumb2xUrl' => asset($imagePathsObj->folder_path_thumbnail2x),
            'thumb3xUrl' => asset($imagePathsObj->folder_path_thumbnail3x),
            'sysImageUrl' => asset('images/assets'),
            'csrf' => csrf_token(),
            'domain' => config("app.base_domain"),
            'dir' => config("app.dir"),
            'appUrl' => config('app.url'),
            'currentRouteName' => Route::currentRouteName(),
            'status' => session('status') ? session('status') : '',
        ];

        return $forAll;
    }

    public function share(Request $request): array
    {

        $vendorIds = getNormalAccessVendorIds();

        $imagePathsObj = $this->imagePaths();
        $psService = new PsService();

        $backendSetting = BackendSetting::first();
        $mobileSetting = MobileSetting::first();
        $frontendSetting = FrontendSetting::select('firebase_web_push_key_pair', 'firebase_config')->first();

        $forBE = $this->forBE($mobileSetting, $psService);
        $forFE = $this->forFE();
        $forVendor = $this->forVendor($vendorIds);
        $forAll = $this->forAll($frontendSetting, $backendSetting, $mobileSetting, $imagePathsObj, $vendorIds);

        return array_merge(
            parent::share($request),
            $forBE, $forFE, $forAll, $forVendor
        );

    }
}
