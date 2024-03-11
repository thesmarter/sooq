<?php

namespace Modules\StoreFront\VendorPanel\Http\Controllers\Backend\Controllers\PaymentStatus;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\StoreFront\VendorPanel\Http\Services\VendorPaymentStatusService;
use Modules\StoreFront\VendorPanel\Http\Requests\StoreVendorPaymentStatusRequest;
use Modules\StoreFront\VendorPanel\Http\Requests\UpdateVendorPaymentStatusRequest;

class VendorPaymentStatusController extends Controller
{
    protected $vendorPaymentStatusService;

    public function __construct(VendorPaymentStatusService $vendorPaymentStatusService)
    {
        $this->vendorPaymentStatusService = $vendorPaymentStatusService;
    }

    public function index(Request $request)
    {
        return $this->vendorPaymentStatusService->index($request);
    }

    public function create()
    {
        return $this->vendorPaymentStatusService->create();
    }

    public function store(StoreVendorPaymentStatusRequest $request)
    {
        return $this->vendorPaymentStatusService->store($request);
    }

    public function edit($vendor_id, $id)
    {
        return $this->vendorPaymentStatusService->edit($vendor_id, $id);
    }

    public function update(UpdateVendorPaymentStatusRequest $request)
    {
        return $this->vendorPaymentStatusService->update($request);
    }

    public function statusChange($vendor_id, $id){
        return $this->vendorPaymentStatusService->makePublishOrUnpublish($vendor_id, $id);
    }

    public function destroy($vendor_id, $id)
    {
        return $this->vendorPaymentStatusService->destroy($vendor_id, $id);
    }
}
