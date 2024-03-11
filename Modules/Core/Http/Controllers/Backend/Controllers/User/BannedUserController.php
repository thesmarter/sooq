<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\User;

use App\Config\ps_constant;
use App\Models\User;
use Modules\Core\Constants\Constants;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Http\Services\ImageService;
use Modules\Core\Http\Services\CustomFieldService;
use Modules\Core\Http\Services\UserService;
use Modules\Core\Http\Requests\UpdateUserRequest;

class BannedUserController extends Controller
{

    const parentPath = "banned_user/";
    const indexPath = self::parentPath."Index";
    const createPath = self::parentPath."Create";
    const editPath = self::parentPath."Edit";
    const indexRoute = "banned_user.index";
    const createRoute = "banned_user.create";
    const editRoute = "banned_user.edit";

    protected $dangerFlag, $userService, $imageService, $customFieldService;

    public function __construct(UserService $userService, ImageService $imageService, CustomFieldService $customFieldService)
    {
        $this->userService = $userService;
        $this->imageService = $imageService;
        $this->customFieldService = $customFieldService;
        $this->dangerFlag = Constants::danger;
    }

    public function index(Request $request)
    {
        $dataArr = $this->userService->indexBannedUser($request);
        if (!permissionControl(Constants::bannedUserModule,ps_constant::readPermission)){
            return redirect()->route('admin.index');
        }
        return renderView(self::indexPath, $dataArr);
    }

    public function edit($id)
    {
        if (!permissionControl(Constants::bannedUserModule,ps_constant::updatePermission)){
            return redirect()->route('admin.index');
        }
        $dataArr = $this->userService->editBannedUser($id);
        return renderView(self::editPath, $dataArr);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        if (!permissionControl(Constants::bannedUserModule,ps_constant::updatePermission)){
            return redirect()->route('admin.index');
        }
        $user = $this->userService->update($id, $request);

        // if have error
        if (isset($user['error'])){
            $msg = $user['error'];
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }

        return redirect()->back();
    }

    public function destroy($id)
    {
        if (!permissionControl(Constants::bannedUserModule,ps_constant::deletePermission)){
            return redirect()->route('admin.index');
        }
        $dataArr = $this->userService->destroyBannedUser($id);
        return renderView(self::indexRoute, $dataArr);
    }

    public function ban($id){

        $this->userService->ban($id);
        return redirect()->back();
    }

    public function statusChange(User $user){

        if($user->status == 1){
            $user->status = 0;
        }else{
            $user->status = 1;
        }
        $user->updated_user_id = Auth::user()->id;
        $user->update();


        $status = [
            'flag' => 'success',
            // 'msg' => 'The status of '.$user->name.' row has been updated successfully.',
            'msg' => __('core__be_status_attribute_updated',['attribute'=>$user->name]),
        ];

        return redirect()->route('banned_user.index')->with('status', $status);
    }
}
