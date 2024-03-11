<?php

namespace Modules\StoreFront\VendorPanel\Http\Controllers\Backend\Controllers\Subscription;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Core\Http\Services\VendorService;
use Modules\Core\Http\Services\UpgradeSubscriptionService;
use Modules\StoreFront\VendorPanel\Http\Requests\UpdateVendorRequest;

class UpgradeSubscriptionController extends Controller
{
    const parentPath = "Pages/vendor/views/upgrade_subscription/";
    const indexPath = self::parentPath . 'Index';
    const createPath = self::parentPath . 'Create';
    const editPath = self::parentPath . 'Edit';
    const indexRoute = "upgrade_subscription.index";

    protected $vendorService, $upgradeSubscriptionService;

    public function __construct(VendorService $vendorService, UpgradeSubscriptionService $upgradeSubscriptionService)
    {
        $this->vendorService = $vendorService;
        $this->upgradeSubscriptionService = $upgradeSubscriptionService;
    }

    public function index(Request $request)
    {
        $dataArr = $this->upgradeSubscriptionService->index($request);

        $checkPermission = $dataArr["checkPermission"];
        if ($checkPermission == false) {
            return redirect()->back();
        }

        return renderView(self::indexPath, $dataArr);
    }

    public function show($id)
    {
        $dataArr = $this->vendorService->show($id);

        return renderView(self::editPath, $dataArr);
    }

    public function update(UpdateVendorRequest $request, $id)
    {
        return $this->vendorService->update($id, $request);
    }

    public function store(Request $request)
    {
        $dataArr = $this->upgradeSubscriptionService->upgradeSubscription($request);

        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }
}
