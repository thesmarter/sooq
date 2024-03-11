<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\Vendor;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Constants\Constants;
use Modules\Core\Http\Services\VendorSettingService;
use Modules\Core\Http\Requests\UpdateVendorSettingRequest;

class VendorSettingController extends Controller
{
    const parentPath = "vendor_setting/";
    const editPath = self::parentPath . 'Edit';
    const indexRoute = "vendor_setting.index";

    protected $vendorSettingService;

    public function __construct(VendorSettingService $vendorSettingService)
    {
        $this->vendorSettingService = $vendorSettingService;
    }

    public function index(Request $request)
    {
        $dataArr = $this->vendorSettingService->index();

        // check permission
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }

        return renderView(self::editPath, $dataArr);
    }

    public function update(UpdateVendorSettingRequest $request, $id)
    {
        // validation end
        if(isset($request->vendor_setting)){
            $updateObj = new \stdClass();
            $updateObj->vendor_setting = $request->vendor_setting;
            $updateObj->vendor_subscription = $request->vendor_subscription;
            $updateObj->notic_days = $request->notic_days;
            $updateObj->id = $id;
        }else{
            return redirect()->back();
        }

        $backend_setting = $this->vendorSettingService->update($id,$updateObj);

        // if have error
        if (isset($backend_setting['error'])){
            $msg = $backend_setting['error'];
            dd($backend_setting['error']);
            return redirectView(self::indexRoute, $msg, Constants::danger);
        }


        return redirect()->back();
    }

    public function languageRefresh(){
        $msg = "Vendor Language is refreshed Successfully";
        generateAllVendorLanguages();
        return redirectView(self::indexRoute, $msg, "langSuccess");
    }

}
