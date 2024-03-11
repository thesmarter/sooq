<?php

namespace Modules\Core\Http\Services;

use Carbon\Carbon;
use App\Config\ps_config;
use App\Config\ps_constant;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\Item;

use Modules\Core\Http\Services\ItemService;


class  VendorItemService extends ItemService
{
    // protected ;

    // for Backend
    public function index($request)
    {
        $vendor_id = getVendorIdFromSession();
        $request->vendor_id = $vendor_id;
        $response = $this->getItemList($request);

        $categoriesWithCount = $this->categoryService->getCategories();
        $defaultCategoryId = $categoriesWithCount[0]->id;
        $categoryId = $request->category_filter ?? $defaultCategoryId;
        // $conds['category_id'] = $categoryId;
        $conds['vendor_id'] = $vendor_id;
        foreach($categoriesWithCount as $category){
            $category->count = $this->countCategory($category->id, $conds);
        }

        // check permission
        $checkPermission = vendorPermissionControl(Constants::vendorItemModule, ps_constant::readPermission, $vendor_id);
        $response["checkPermission"] = $checkPermission; //will return true or false
        $response['categoriesWithCount'] = $categoriesWithCount;

        return $response;
    }
}