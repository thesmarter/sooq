<?php

namespace Modules\BlueMarkUser\Http\Controllers\Backend\Controller;

use App\Config\ps_constant;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Constants\Constants;
use Modules\Core\Http\Services\CoreKeyService;
use Modules\BlueMarkUser\Http\Requests\UpdateBlueMarkReqeust;
use Modules\BlueMarkUser\Http\Services\BlueMarkUserService;
// use Modules\Core\Entities\UserInfo;
use Modules\Core\Http\Services\PushNotificationTokenService;
use Modules\Core\Http\Services\UserService;

class BlueMarkUserController extends Controller
{
    const parentPath = "bluemarkuser/";

    const indexPath = self::parentPath . "Index";
    const editPath = self::parentPath . "Edit";
    const indexRoute = "bluemarkuser.index";
    const editRoute = "bluemarkuser.edit";

    protected $blueMarkUserService;

    public function __construct(BlueMarkUserService $blueMarkUserService, CoreKeyService $coreKeyService, UserService $userService, PushNotificationTokenService $pushNotificationTokenService)
    {
        $this->blueMarkUserService = $blueMarkUserService;
        $this->coreKeyService = $coreKeyService;
        $this->userService = $userService;
        $this->pushNotificationTokenService = $pushNotificationTokenService;

        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;

        $this->code = Constants::user;
    }

    public function index(Request $request)
    {
        $dataArr = $this->blueMarkUserService->index($request);
        if (!permissionControl(Constants::blueMarkUserModule,ps_constant::readPermission)){
            return redirect()->route('admin.index');
        }
        return renderView(self::indexPath, $dataArr);
    }

    public function edit($id)
    {
        if (!permissionControl(Constants::blueMarkUserModule,ps_constant::updatePermission)){
            return redirect()->route('admin.index');
        }
        $dataArr = $this->blueMarkUserService->edit($id, self::indexPath);
        return renderView(self::editPath, $dataArr);
    }

    public function update(UpdateBlueMarkReqeust $request,$id)
    {
        if (!permissionControl(Constants::blueMarkUserModule,ps_constant::updatePermission)){
            return redirect()->route('admin.index');
        }
        $userInfo = $this->blueMarkUserService->update( $id, $request);

        // if have error
        if (isset($userInfo['error'])){
            $msg = $userInfo['error'];
            return redirectView(self::editRoute, $msg, $this->dangerFlag, $id);
        }

        // $bluemark = UserInfo::where( ['user_id' => $userInfo->user_id, 'core_keys_id' => Constants::usrIsVerifyBlueMark])->first();

        if($userInfo->value == 1){
			$message = __( 'core__be_verify_blue_mark_noti_approve' );
            $this->sendNoti($userInfo->user_id, $message);
        }else if($userInfo->value == 3){
			$message = __( 'core__be_verify_blue_mark_noti_reject' );
            $this->sendNoti($userInfo->user_id, $message);
        }

        return redirect()->back();
    }

    public function sendNoti($user_id, $message){
        // send noti to blue mark user
        $user = $this->userService->getUser($user_id);
        $token_conds['user_id'] = $user->id;
        $notiTokens = $this->pushNotificationTokenService->getNotiTokens(null, $token_conds);
        $device_ids = [];
        $platform_names = [];
        foreach ($notiTokens as $token) {
            $device_ids[] = $token->device_token;
            $platform_names[] = $token->platform_name;
        }

        $data['message'] = $message;
        $data['flag'] = Constants::verifyBlueMarkNotiFlag;
        send_android_fcm($device_ids, $data, $platform_names);

        // send mail to blue mark user
        $to = $user->email;
        $subject = __('core__be_blue_mark');
        $to_name = $user->name;
        $body = $message;
        sendMail($to, $to_name, $subject, null, $body);
    }

    public function destroy($id)
    {
        if (!permissionControl(Constants::blueMarkUserModule,ps_constant::deletePermission)){
            return redirect()->route('admin.index');
        }
        $dataArr = $this->blueMarkUserService->destroy($id);
        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }

}
