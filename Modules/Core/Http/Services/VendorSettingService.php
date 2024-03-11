<?php
namespace Modules\Core\Http\Services;

use App\Http\Services\PsService;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Setting;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\CoreMenuGroup;
use Modules\Core\Entities\BackendSetting;
use Modules\Core\Http\Services\VendorMenuGroupService;

class VendorSettingService extends PsService
{

    protected $menuGroupService, $vendorMenuGroupService;

    public function __construct(MenuGroupService $menuGroupService, VendorMenuGroupService $vendorMenuGroupService)
    {
        $this->menuGroupService = $menuGroupService;
        $this->vendorMenuGroupService = $vendorMenuGroupService;

    }


    public function update($id,$request)
    {
        try{
        DB::beginTransaction();

            $backend_setting = BackendSetting::find($id);
            $backend_setting->vendor_setting = $request->vendor_setting;
            $backend_setting->update();

            $vendorMenuConds['group_name'] = 'Vendor';
            $vendorMenuConds['id'] = 5;
            $vendorMenu = $this->menuGroupService->getMenuGroup(null, null, $vendorMenuConds);

            $vendorMenu->is_show_on_menu = $backend_setting->vendor_setting;
            $vendorMenu->update();

            // hide package menu when subscription setting is FREE
            $vendorMenuGroup = $this->vendorMenuGroupService->getMenuGroup(3);
            if($request->vendor_subscription == 'FREE'){
                $vendorMenuGroup->is_show_on_menu = 0;
            }else{
                $vendorMenuGroup->is_show_on_menu = 1;
            }
            $vendorMenuGroup->update();

            $vendor_subscription_setting = $this->getJsonVendorSubscriptionConfig();
            // $selected_setting = $vendor_subscription_setting->setting;


            $selected_vendor_setting = array(
                ["id" =>  $request->vendor_subscription]
            );

            $vendor_setting["subscription_plan"] = $selected_vendor_setting;
            $vendor_setting["notic_days"] = $request->notic_days;

            $vendor_subscription_setting->setting = $vendor_setting;
            $vendor_subscription_setting->save();

            DB::commit();
            }catch (\Throwable $e){
                DB::rollBack();
                return ['error' => $e->getMessage()];
            }
        return $backend_setting;
    }

    public function getJsonVendorSubscriptionConfig(){
        $setting = Setting::where('setting_env',Constants::VENDOR_SUBSCRIPTION_CONFIG)->first();
        return $setting;
    }

    public function getBackendSetting(){
        $backend_setting = BackendSetting::select('id','vendor_setting')->first();
        return $backend_setting;
    }

    // --------------
	public function index(){

        $backend_setting = $this->getBackendSetting();
        $vendor_subscription = $this->getJsonVendorSubscriptionConfig();
        $checkPermission = $this->checkPermission(Constants::viewAnyAbility, BackendSetting::class, "admin.index");
        $dataArr = [
            'checkPermission' => $checkPermission,
            'backend_setting' => $backend_setting,
            'vendor_subscription' => $vendor_subscription
        ];
        return $dataArr;
    }




}
