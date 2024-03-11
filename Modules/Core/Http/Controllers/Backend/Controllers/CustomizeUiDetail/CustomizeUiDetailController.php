<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\CustomizeUiDetail;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Constants\Constants;
use Modules\Core\Http\Requests\StoreCustomizeUiDetailRequest;
use Modules\Core\Http\Requests\UpdateCustomizeUiDetailRequest;
use Modules\Core\Http\Services\CustomizeUiDetailService;

class CustomizeUiDetailController extends Controller
{

    const parentPath = "customize_ui_detail/";
    const indexPath = self::parentPath."Index";
    const createPath = self::parentPath."Create";
    const editPath = self::parentPath."Edit";
    const indexRoute = "attribute.index";
    const createRoute = "attribute.create";
    const editRoute = "attribute.edit";

    protected $customizeUiDetailService, $successFlag, $dangerFlag, $csvFile, $warningFlag;

    public function __construct(CustomizeUiDetailService $customizeUiDetailService)
    {
        $this->customizeUiDetailService = $customizeUiDetailService;
        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;
        $this->warningFlag = Constants::warning;
        $this->csvFile = Constants::csvFile;
    }

    public function index(Request $request)
    {
        $dataArr = $this->customizeUiDetailService->index($request);

         // go back to index
         $checkPermission = $dataArr["checkPermission"];
         if (!empty($checkPermission)){
             return $checkPermission;
         }

        return renderView(self::indexPath, $dataArr);
    }

    public function screenDisplayUiStore(Request $request) {

        $parameter = ['page' => $request->current_page];

        $this->customizeUiDetailService->makeColumnHideShown($request);

        $msg = 'ScreenDisplay UiSetting is updated.';
        // return redirectView(self::indexRoute, $msg,null,$parameter);
        return redirect()->back();
    }


    public function create(Request $request)
    {
        $dataArr = $this->customizeUiDetailService->create($request);

         // go back to index
         $checkPermission = $dataArr["checkPermission"];
         if (!empty($checkPermission)){
             return $checkPermission;
         }

        return renderView(self::createPath, $dataArr);
    }


    public function store(StoreCustomizeUiDetailRequest $request)
    {
        $fieldId = $request->field; // core_keys_id
        $customizeUiDetail = $this->customizeUiDetailService->store($request);
        $tableId = $request->tableId;
        $core_keys_id = $request->core_keys_id;
        // if have error
        if ($customizeUiDetail['error']){
            $msg = $customizeUiDetail;
            $params = [$tableId, $core_keys_id];
            return redirectView(self::createRoute, $msg, $this->dangerFlag, $params);
        }

        return redirect()->route(self::indexRoute, [$tableId, $fieldId]);
    }


    public function show($id)
    {
        return view('core::show');
    }


    public function edit(Request $request)
    {
        $dataArr = $this->customizeUiDetailService->edit($request);

         // go back to index
         $checkPermission = $dataArr["checkPermission"];
         if (!empty($checkPermission)){
             return $checkPermission;
         }

        return renderView(self::editPath, $dataArr);
    }

    public function update(UpdateCustomizeUiDetailRequest $request)
    {
        $customizeUiDetail = $this->customizeUiDetailService->update($request);
        $tableId = $request->tableId;
        $fieldId = $request->field; // core_keys_id

        // if have error
        if ($customizeUiDetail['error']){
            $coreKeysId = $request->core_keys_id;
            $attributeId = $request->attribute;
            $msg = $customizeUiDetail;
            $params = [$tableId, $coreKeysId, $attributeId];
            return redirectView(self::editRoute, $msg, $this->dangerFlag, $params);

            // $params = [$tableId, $core_keys_id];
            // return redirectView(self::indexRoute, $msg, $this->dangerFlag, $params);
        }

        return redirect()->route(self::indexRoute, [$tableId, $fieldId]);
    }


    public function destroy(Request $request)
    {
        $dataArr = $this->customizeUiDetailService->destroy($request);

         // go back to index
         $checkPermission = $dataArr["checkPermission"];
         if (!empty($checkPermission)){
             return $checkPermission;
         }

        // $msg = 'The '.$dataArr['name'].' row has been deleted successfully.';
        $msg = __('core__be_delete_success',['attribute'=>$dataArr["name"]]);
        // return redirect()->back()->with();
        $params = [$request->table, $request->field];
        return redirectView(self::indexRoute, $msg, $this->dangerFlag, $params);
    }
}
