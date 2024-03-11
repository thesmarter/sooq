<?php

namespace Modules\SlowMovingItem\Http\Controllers\Backend\Controllers;

use App\Config\ps_constant;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Constants\Constants;
use Modules\SlowMovingItem\Http\Services\SlowMovingItemService;

class SlowMovingItemReportController extends Controller
{
    const parentPath = "slow_moving_items/slow_moving_item_report/";
    const indexPath = self::parentPath."Index";
    const createPath = self::parentPath."Create";
    const editPath = self::parentPath."Edit";
    const indexRoute = "slow_moving_item_report.index";
    const createRoute = "slow_moving_item_report.create";
    const editRoute = "slow_moving_item_report.edit";

    protected $slowMovingItemService, $successFlag, $dangerFlag, $csvFile;

    public function __construct(SlowMovingItemService $slowMovingItemService)
    {
        $this->slowMovingItemService = $slowMovingItemService;
        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;
        $this->csvFile = Constants::csvFile;
    }

    public function index(Request $request)
    {
        $dataArr = $this->slowMovingItemService->slowMovingItemReportIndex($request);
        if (!permissionControl(Constants::slowMovingItemReportModule,ps_constant::readPermission)){
            return redirect()->route('admin.index');
        }
        return renderView(self::indexPath, $dataArr);
    }

    public function show($id)
    {
        $dataArr = $this->slowMovingItemService->slowMovingItemReportShow($id);
        return renderView(self::editPath, $dataArr);
    }

    public function csvExport(){
        // filename
		return $this->slowMovingItemService->csvExport();
    }

}
