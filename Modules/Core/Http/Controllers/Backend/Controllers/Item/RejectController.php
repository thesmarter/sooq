<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\Item;

use App\Config\ps_constant;
use Illuminate\Http\Request;
use Modules\Core\Constants\Constants;
use Illuminate\Routing\Controller;
use Modules\Core\Http\Services\ItemService;

class RejectController extends Controller
{
    const parentPath = "reject_item/";
    const indexPath = self::parentPath."Index";
    const editPath = self::parentPath."Edit";
    const indexRoute = "reject_item.index";
    const editRoute = "reject_item.edit";

    protected $rejectItem, $itemService, $dangerFlag;

    public function __construct(ItemService $itemService)
    {
        $this->itemService = $itemService;
        $this->dangerFlag = Constants::danger;
        $this->rejectItem = Constants::rejectItem;

    }

    public function index(Request $request)
    {
        $dataArr = $this->itemService->disableOrPendingOrRejectIndex($this->rejectItem, $request);
        if (!permissionControl(Constants::rejectModule,ps_constant::readPermission)){
            return redirect()->route('admin.index');
        }
        return renderView(self::indexPath, $dataArr);
    }

    public function edit($id)
    {
        $relation = ['category', 'subcategory', 'city', 'township', 'currency', 'owner','itemRelation', 'cover', 'video', 'icon'];
        $dataArr = $this->itemService->disableOrPendingOrRejectEdit($relation, $id);
        return renderView(self::editPath, $dataArr);
    }

    public function destroy($id)
    {
        if (!permissionControl(Constants::rejectModule,ps_constant::deletePermission)){
            return redirect()->route('admin.index');
        }
        $dataArr = $this->itemService->disableOrPendingOrRejectDestroy($id);
        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }

    public function statusChange(Request $request, $id)
    {
        if (!permissionControl(Constants::rejectModule,ps_constant::updatePermission)){
            return redirect()->route('admin.index');
        }
        $item = $this->itemService->disableOrPendingOrRejectStatusChange($id,$request);

        // if have error
        if (isset($item['error'])){
            $msg = $item['error'];
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }
            
        return redirectView(self::indexRoute, $item['msg'], $item['flag']);
    }
}
