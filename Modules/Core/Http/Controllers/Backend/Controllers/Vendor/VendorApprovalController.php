<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\Vendor;

use App\Config\ps_constant;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Constants\Constants;
use Modules\Core\Http\Services\VendorApprovalService;

class VendorApprovalController extends Controller
{
    const parentPath = "vendor_approval/";
    const indexPath = self::parentPath . 'Index';
    const createPath = self::parentPath . 'Create';
    const editPath = self::parentPath . 'Edit';
    const indexRoute = "pending_vendor.index";

    protected $vendorApprovalService;

    public function __construct(VendorApprovalService $vendorApprovalService)
    {
        $this->vendorApprovalService = $vendorApprovalService;
    }

    public function index(Request $request)
    {
        $dataArr = $this->vendorApprovalService->index($request);

        if (!permissionControl(Constants::pendingVendorModule, ps_constant::readPermission)){
            return redirect()->route('admin.index');
        }

        return renderView(self::indexPath, $dataArr);
    }

    public function statusChange(Request $request, $id)
    {
        if (!permissionControl(Constants::pendingVendorModule, ps_constant::updatePermission)){
            return redirect()->route('admin.index');
        }
        $vendor = $this->vendorApprovalService->disableOrPendingOrRejectStatusChange($id,$request);

        // if have error
        if (isset($vendor['error'])){
            $msg = $vendor['error'];
            return redirectView(self::indexRoute, $msg, Constants::danger);
        }

        return redirectView(self::indexRoute, $vendor['msg'], $vendor['flag']);
    }

    public function downloadDocument($id)
    {
        $dataArr = $this->vendorApprovalService->downloadDocument($id);

        return redirectView(self::indexRoute, $dataArr);
    }

    public function show($id)
    {
        if (!permissionControl(Constants::pendingVendorModule, ps_constant::readPermission)){
            return redirect()->route('admin.index');
        }
        $dataArr = $this->vendorApprovalService->show($id);

        return renderView(self::editPath, $dataArr);
    }

    public function destroy($id)
    {
        if (!permissionControl(Constants::pendingVendorModule, ps_constant::deletePermission)){
            return redirect()->route('admin.index');
        }

        $dataArr = $this->vendorApprovalService->disableOrPendingOrRejectDestroy($id);

        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }
}
