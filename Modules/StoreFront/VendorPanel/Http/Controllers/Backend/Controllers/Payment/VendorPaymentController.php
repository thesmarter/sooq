<?php

namespace Modules\StoreFront\VendorPanel\Http\Controllers\Backend\Controllers\Payment;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Constants\Constants;
use Modules\Core\Http\Services\CoreKeyService;
use Modules\StoreFront\VendorPanel\Http\Services\VendorPaymentService;

class VendorPaymentController extends Controller
{
    const parentPath = "Pages/vendor/views/payment_lists/";

    const indexPath = self::parentPath . "Index";
    const createPath = self::parentPath . "Create";
    const editPath = self::parentPath . "Edit";
    const indexRoute = "vendor_payment.index";
    const createRoute = "vendor_payment.create";
    const editRoute = "vendor_payment.edit";

    protected $vendorPaymentService, $coreKeyService, $successFlag, $dangerFlag, $code;

    public function __construct(VendorPaymentService $vendorPaymentService, CoreKeyService $coreKeyService)
    {
        $this->vendorPaymentService = $vendorPaymentService;
        $this->coreKeyService = $coreKeyService;

        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;

        $this->code = Constants::payment;
    }

    public function index(Request $request)
    {
        return $this->vendorPaymentService->index($request);
    }

    public function statusChange($id)
    {
        return $this->vendorPaymentService->makePublishOrUnpublish($id);
    }

    public function edit($id)
    {
        return $this->vendorPaymentService->edit($id);
    }

    public function update(Request $request, $id)
    {
        return $this->vendorPaymentService->update($id, $request);
    }

}
