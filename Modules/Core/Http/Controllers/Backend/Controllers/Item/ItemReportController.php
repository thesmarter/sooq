<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\Item;

use App\Config\ps_constant;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Constants\Constants;
use Modules\Core\Http\Services\ItemService;

class ItemReportController extends Controller
{
    const parentPath = "item_report/";
    const indexPath = self::parentPath."Index";
    const createPath = self::parentPath."Create";
    const editPath = self::parentPath."Edit";

    const parentSuccessfulDealCountReportPath = "successful_deal_count_report/";
    const indexSuccessfulDealCountReportPath = self::parentSuccessfulDealCountReportPath."Index";
    const createSuccessfulDealCountReportPath = self::parentSuccessfulDealCountReportPath."Create";
    const editSuccessfulDealCountReportPath = self::parentSuccessfulDealCountReportPath."Edit";

    const parentSoldOutItemReportPath = "sold_out_item_report/";
    const indexSoldOutItemReportPath = self::parentSoldOutItemReportPath."Index";
    const createSoldOutItemReportPath = self::parentSoldOutItemReportPath."Create";
    const editSoldOutItemReportPath = self::parentSoldOutItemReportPath."Edit";

    protected $itemService;

    public function __construct(ItemService $itemService)
    {
        $this->itemService = $itemService;
    }

    // Item Report
    public function itemReportIndex(Request $request)
    {
        $dataArr = $this->itemService->itemReportIndex($request);
        if (!permissionControl(Constants::itemReportModule,ps_constant::readPermission)){
            return redirect()->route('admin.index');
        }
        return renderView(self::indexPath, $dataArr);
    }

    public function itemReportShow($id)
    {
        $relation = ['category', 'subcategory', 'city', 'township', 'currency', 'owner','itemRelation', 'cover', 'video', 'icon'];
        $dataArr = $this->itemService->itemReportShow($id, $relation);
        return renderView(self::editPath, $dataArr);
    }

    public function itemReportCsvExport(){
        // filename
		return $this->itemService->itemReportCsvExport();
    }

    // Successful Deal Count Report
    public function successfulDealCountReportIndex(Request $request)
    {
        $dataArr = $this->itemService->successfulDealCountReportIndex($request);
        if (!permissionControl(Constants::successfulDealCountReportModule,ps_constant::readPermission)){
            return redirect()->route('admin.index');
        }
        return renderView(self::indexSuccessfulDealCountReportPath, $dataArr);
    }

    public function successfulDealCountReportShow($id)
    {
        $dataArr = $this->itemService->successfulDealCountReportShow($id);
        return renderView(self::editSuccessfulDealCountReportPath, $dataArr);
    }

    public function successfulDealCountReportCsvExport(){
        // filename
		return $this->itemService->successfulDealCountReportCsvExport();
    }

    // Sold Out Item Report
    public function soldOutItemReportIndex(Request $request)
    {
        $dataArr = $this->itemService->soldOutItemReportIndex($request);
        if (!permissionControl(Constants::soldOutItemReportModule,ps_constant::readPermission)){
            return redirect()->route('admin.index');
        }
        return renderView(self::indexSoldOutItemReportPath, $dataArr);
    }

    public function soldOutItemReportShow($id)
    {
        $relation = ['category', 'subcategory', 'user_boughts', 'city', 'township', 'currency', 'owner',
        'itemRelation', 'cover', 'video', 'icon'];
        $dataArr = $this->itemService->soldOutItemReportShow($id, $relation);

        return renderView(self::editSoldOutItemReportPath, $dataArr);
    }

    public function soldOutItemReportCsvExport(){
        // filename
        return $this->itemService->soldOutItemReportCsvExport();
    }
}
