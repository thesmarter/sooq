<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\Vendor;

use App\Config\ps_constant;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Constants\Constants;
use Modules\Core\Http\Services\VendorRejectService;

class VendorRejectController extends Controller
{
    const parentPath = "vendor_reject/";
    const indexPath = self::parentPath . 'Index';
    const createPath = self::parentPath . 'Create';
    const editPath = self::parentPath . 'Edit';
    const indexRoute = "reject_vendor.index";

    protected $vendorRejectService;

    public function __construct(VendorRejectService $vendorRejectService)
    {
        $this->vendorRejectService = $vendorRejectService;
    }

    public function index(Request $request)
    {
        $dataArr = $this->vendorRejectService->index($request);

        if (!permissionControl(Constants::rejectVendorModule, ps_constant::readPermission)){
            return redirect()->route('admin.index');
        }
        return renderView(self::indexPath, $dataArr);
    }

    public function statusChange(Request $request, $id)
    {
        if (!permissionControl(Constants::rejectVendorModule, ps_constant::updatePermission)){
            return redirect()->route('admin.index');
        }
        $vendor = $this->vendorRejectService->disableOrPendingOrRejectStatusChange($id,$request);

        // if have error
        if (isset($vendor['error'])){
            $msg = $vendor['error'];
            return redirectView(self::indexRoute, $msg, Constants::danger);
        }

        return redirectView(self::indexRoute, $vendor['msg'], $vendor['flag']);
    }

    public function downloadDocument($id)
    {
        $dataArr = $this->vendorRejectService->downloadDocument($id);

        return $dataArr;
    }

    public function show($id)
    {
        if (!permissionControl(Constants::rejectVendorModule, ps_constant::readPermission)){
            return redirect()->route('admin.index');
        }
        $dataArr = $this->vendorRejectService->show($id);

        return renderView(self::editPath, $dataArr);
    }

    public function destroy($id)
    {
        if (!permissionControl(Constants::rejectVendorModule, ps_constant::deletePermission)){
            return redirect()->route('admin.index');
        }

        $dataArr = $this->vendorRejectService->disableOrPendingOrRejectDestroy($id);

        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }
}
