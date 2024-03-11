<?php

namespace Modules\StoreFront\VendorPanel\Http\Controllers\Backend\Controllers\Item;

use Modules\Core\Http\Services\VendorItemService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\VendorRolePermission;
use Modules\Core\Entities\VendorUserPermission;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

class VendorItemController extends Controller
{
    const parentPath = "Pages/vendor/views/item/";
    const indexPath = self::parentPath . 'ItemList';
    const createPath = self::parentPath . 'Create';
    const editPath = self::parentPath . 'ItemDetail';
    const indexRoute = "vendor_item.index";

    protected $itemService;

    public function __construct( VendorItemService $itemService)
    {
        $this->itemService = $itemService;

    }

    public function index(Request $request)
    {

        $dataArr = $this->itemService->index($request);
        // check permission
        $checkPermission = $dataArr["checkPermission"];
        if ($checkPermission == false) {
            return redirect()->back();
        }

        return renderView(self::indexPath, $dataArr);
    }

    public function destroy($id)
    {


        $dataArr = $this->itemService->destroy($id, self::indexRoute);
        // check permission
        $checkPermission = $dataArr["checkPermission"];
        if ($checkPermission == false) {
            return redirect()->back();
        }
        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }

    public function edit($id)
    {
        $relation = ['category', 'subcategory', 'city', 'township', 'currency', 'owner','itemRelation', 'cover', 'video', 'icon'];
        $dataArr = $this->itemService->itemReportShow($id, $relation);
        return renderView(self::editPath, $dataArr);
    }

}
