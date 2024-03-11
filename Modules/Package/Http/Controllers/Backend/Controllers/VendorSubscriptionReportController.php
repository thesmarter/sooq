<?php

namespace Modules\Package\Http\Controllers\Backend\Controllers;

use App\Config\ps_constant;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Constants\Constants;
use Illuminate\Contracts\Support\Renderable;
use Modules\Package\Http\Services\VendorSubscriptionPlanBoughtTransactionService;
use Modules\Core\Transformers\Backend\NoModel\VendorReport\VendorSubscriptionTransactionWithKeyResource;

class VendorSubscriptionReportController extends Controller
{
    const parentPath = "vendor_subscription_report/";
    const indexPath = self::parentPath."Index";
    const editPath = self::parentPath."Edit";
    const indexRoute = "vendor_subscription_report.index";
    const editRoute = "vendor_subscription_report.edit";

    protected $vendorSubscriptionPlanBoughtTransactionService, $packageService, $successFlag, $dangerFlag, $csvFile, $warningFlag;

    public function __construct(VendorSubscriptionPlanBoughtTransactionService $vendorSubscriptionPlanBoughtTransactionService)
    {
        $this->vendorSubscriptionPlanBoughtTransactionService = $vendorSubscriptionPlanBoughtTransactionService;
        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;
        $this->warningFlag = Constants::warning;
        $this->csvFile = Constants::csvFile;

    }

    public function index(Request $request)
    {
        if (!permissionControl(Constants::vendorSubscriptionReportModule,ps_constant::readPermission)){
            return redirect()->route('admin.index');
        }
        $dataArr = $this->vendorSubscriptionPlanBoughtTransactionService->subscriptionReportIndex($request);

        return renderView(self::indexPath, $dataArr);
    }

    public function show($id)
    {
        $relations = ['package', 'user'];
        if (!permissionControl(Constants::vendorSubscriptionReportModule,ps_constant::readPermission)){
            return redirect()->route('admin.index');
        }
        $dataArr['transaction'] = new VendorSubscriptionTransactionWithKeyResource($this->vendorSubscriptionPlanBoughtTransactionService->getPackageBoughtTransaction($id, null, $relations));

        return renderView(self::editPath, $dataArr);
    }
}
