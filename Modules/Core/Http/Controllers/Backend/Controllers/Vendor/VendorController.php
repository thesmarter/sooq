<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\Vendor;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Http\Services\VendorService;

class VendorController extends Controller
{
    const parentPath = "vendor/";
    const indexPath = self::parentPath . 'Index';
    const createPath = self::parentPath . 'Create';
    const editPath = self::parentPath . 'Edit';
    const indexRoute = "vendor.index";

    protected $vendorService;

    public function __construct(VendorService $vendorService)
    {
        $this->vendorService = $vendorService;
    }

    public function index(Request $request)
    {
        $dataArr = $this->vendorService->index($request);

        // check permission
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }

        return renderView(self::indexPath, $dataArr);
    }

    public function show($id)
    {
        $dataArr = $this->vendorService->show($id);

        return renderView(self::editPath, $dataArr);
    }

    public function setSession()
    {
        $this->vendorService->setSession();

        return redirect()->route("vendor_info.index");
    }
    
    public function destroy($id)
    {
        $dataArr = $this->vendorService->destroy($id);

        // check permission
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }

        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }

    public function changeVendor($id){
        $this->vendorService->setSession($id);

        return redirect()->route("vendor_info.index");
    }
}
