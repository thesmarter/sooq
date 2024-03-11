<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\VendorRole;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Modules\Core\Constants\Constants;
// use Modules\Core\Entities\Role;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
// use Modules\Core\Entities\RolePermission;
use Illuminate\Contracts\Support\Renderable;
use Modules\Core\Http\Requests\StoreVendorRoleRequest;
use Modules\Core\Http\Requests\UpdateVendorRoleRequest;
use Modules\Core\Http\Services\VendorRoleService;

class VendorRoleController extends Controller
{

    const parentPath = "vendor_role/";
    const indexPath = self::parentPath."Index";
    const createPath = self::parentPath."Create";
    const editPath = self::parentPath."Edit";
    const indexRoute = "vendor_role.index";
    const createRoute = "vendor_role.create";
    const editRoute = "vendor_role.edit";

    protected $roleService, $successFlag, $dangerFlag, $csvFile, $warningFlag;

    public function __construct(VendorRoleService $roleService)
    {
        $this->roleService = $roleService;
        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;
        $this->warningFlag = Constants::warning;
        $this->csvFile = Constants::csvFile;
    }

    public function index(Request $request)
    {
        $dataArr = $this->roleService->index($request);
        // check permission
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        return renderView(self::indexPath, $dataArr);
    }

    public function create()
    {
        $dataArr = $this->roleService->create();
        // check permission
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        return renderView(self::createPath, $dataArr);
    }

    public function store(StoreVendorRoleRequest $request)
    {
        
        $response = $this->roleService->store($request);

        if($response && isset($response['error'])){
            echo json_encode($response);exit;
            return redirect()->route(self::indexRoute)->with('status',[ 'flag' => 'danger', 'msg' => $response['error'] ]);
        }
        return redirect()->back();
    }

    public function edit($id)
    {
        $dataArr = $this->roleService->edit($id);
        // check permission
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        return renderView(self::editPath, $dataArr);
    }

    public function update(UpdateVendorRoleRequest $request, $id)
    {
        $response = $this->roleService->update($request, $id);

        if($response && isset($response['error'])){
            return redirect()->route(self::editRoute, $id)->with('status',[ 'flag' => 'danger', 'msg' => $response['error'] ]);
        }
        return redirect()->back();

    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $name = $this->roleService->destroy($id);

        $status = [
            'flag' => 'danger',
            // 'msg' => 'The '.$name.' row has been deleted successfully.',
            'msg' =>  __('core__be_delete_success',['attribute'=>$name]),
        ];

        return redirect()->route('vendor_role.index')->with('status',$status);

    }

    public function statusChange($id){

        $role = $this->roleService->getVendorRole($id);
        if($role->status == 1){
            $role->status = 0;
        }else{
            $role->status = 1;
        }
        $role->updated_user_id = Auth::user()->id;
        $role->update();

        $status = [
            'flag' => 'success',
            // 'msg' => 'The status of '.$role->name.' row has been updated successfully.',
            'msg' => __('core__be_status_attribute_updated',['attribute'=>$role->name]),

        ];

        return redirect()->back()->with('status',$status);
    }

    public function updateAdminPanelAccess($id){

        $role = $this->roleService->getVendorRole($id);
        if($role->can_access_admin_panel == 1){
            $role->can_access_admin_panel = 0;
        }else{
            $role->can_access_admin_panel = 1;
        }
        $role->updated_user_id = Auth::user()->id;
        $role->update();

        $status = [
            'flag' => 'success',
            // 'msg' => 'The status of '.$role->name.' row has been updated successfully.',
            'msg' => __('core__be_status_attribute_updated',['attribute'=>$role->name]),

        ];

        return redirect()->back()->with('status',$status);
    }

    public function screenDisplayUiStore(Request $request) {
        $parameter = ['page' => $request->current_page];
        $this->roleService->makeColumnHideShown($request);
        $msg = 'ScreenDisplay UiSetting is updated.';
        return redirect()->back();
    }
}
