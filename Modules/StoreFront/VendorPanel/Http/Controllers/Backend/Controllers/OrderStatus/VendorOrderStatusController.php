<?php

namespace Modules\StoreFront\VendorPanel\Http\Controllers\Backend\Controllers\OrderStatus;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\StoreFront\VendorPanel\Http\Requests\StoreOrderStatusRequest;
use Modules\StoreFront\VendorPanel\Http\Requests\UpdateOrderStatusRequest;
use Modules\StoreFront\VendorPanel\Http\Services\VendorOrderStatusService;

class VendorOrderStatusController extends Controller
{
    protected $vendorOrderStatusService;

    public function __construct(VendorOrderStatusService $vendorOrderStatusService)
    {
        $this->vendorOrderStatusService = $vendorOrderStatusService;
    }

    public function index(Request $request)
    {
        return $this->vendorOrderStatusService->index($request);
    }

    public function create()
    {
        return $this->vendorOrderStatusService->create();
    }

    public function store(StoreOrderStatusRequest $request)
    {
        return $this->vendorOrderStatusService->store($request);
    }

    public function edit($vendor_id, $id)
    {
        return $this->vendorOrderStatusService->edit($vendor_id, $id);
    }

    public function update(UpdateOrderStatusRequest $request)
    {
        return $this->vendorOrderStatusService->update($request);
    }

    public function statusChange($vendor_id, $id){
        return $this->vendorOrderStatusService->makePublishOrUnpublish($vendor_id, $id);
    }

    public function destroy($vendor_id, $id)
    {
        return $this->vendorOrderStatusService->destroy($vendor_id, $id);
    }
}
