<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\Color;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Constants\Constants;
use Modules\Core\Http\Services\CategoryService;
use Modules\Core\Http\Requests\StoreCategoryRequest;
use Modules\Core\Http\Requests\UpdateCategoryRequest;
use Modules\Core\Http\Services\ColorService;

class ColorController extends Controller
{
    const parentPath = "color/";
    const indexPath = self::parentPath."Index";
    const createPath = self::parentPath."Create";
    const editPath = self::parentPath."Edit";
    const indexRoute = "color.index";
    const createRoute = "color.create";
    const editRoute = "color.edit";


    protected $colorService, $successFlag, $dangerFlag, $csvFile;

    public function __construct(ColorService $colorService)
    {
        $this->colorService = $colorService;
        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;
    }



    public function index(Request $request)
    {
        $dataArr = $this->colorService->index($request);

        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        return renderView(self::indexPath, $dataArr);
    }

    public function screenDisplayUiStore(Request $request) {

        $parameter = ['page' => $request->current_page];

        $this->colorService->makeColumnHideShown($request);

        $msg = 'ScreenDisplay UiSetting is updated.';
        return redirect()->back();
    }

    public function create()
    {
        $dataArr = $this->colorService->create();
        // check permission
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        return renderView(self::createPath);
    }

    public function store(StoreCategoryRequest $request)
    {
        $color = $this->colorService->store($request);

        // if have error
        if ($color){
            $msg = $color;
            return redirectView(self::createRoute, $msg, $this->dangerFlag);
        }

        return redirect()->back();
    }

    public function edit($id)
    {
        $dataArr = $this->colorService->edit($id);
        // check permission
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }

        return renderView(self::editPath, $dataArr);
    }

    public function update(Request $request, $id)
    {

        $color = $this->colorService->update($request, $id);

        // if have error
        if ($color){
            echo json_encode($color);exit;
            $msg = $color;
            return redirect()->back()->with('status',[ 'flag' =>$this->dangerFlag, 'msg' => $msg ]);
        }

        return redirect()->back();
    }

    public function destroy($id)
    {
        $dataArr = $this->colorService->destroy($id);

        // check permission
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }

        $msg = __('core__be_delete_success', ['attribute' => $dataArr['name']]);
        return redirectView(self::indexRoute, $msg);
    }
}
