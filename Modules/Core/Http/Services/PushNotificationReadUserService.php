<?php

namespace Modules\Core\Http\Services;

use App\Http\Services\PsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\PushNotificationUser;
use Modules\PushNotificationMessage\Http\Services\PushNotificationMessageService;
use Modules\Core\Http\Services\UserAccessApiTokenService;
use App\Config\ps_constant;
use Carbon\Carbon;
use Modules\Chat\Entities\ChatNoti;
use Modules\Chat\Http\Services\ChatService;
use stdClass;

class PushNotificationReadUserService extends PsService
{
    protected $userAccessApiTokenService, $chatService;

    public function __construct(ChatService $chatService, PushNotificationMessageService $pushNotificationMessageService,UserAccessApiTokenService $userAccessApiTokenService)
    {
        $this->successStatus = Constants::successStatus;
        $this->createdStatusCode = Constants::createdStatusCode;
        $this->okStatusCode = Constants::okStatusCode;
        $this->internalServerErrorStatusCode = Constants::internalServerErrorStatusCode;
        $this->noContentStatusCode = Constants::noContentStatusCode;
        $this->notFoundStatusCode = Constants::notFoundStatusCode;
        $this->badRequestStatusCode = Constants::badRequestStatusCode;

        $this->pushNotiMessageApiRelation = ['defaultPhoto'];
        $this->pushNotificationMessageService = $pushNotificationMessageService;
        $this->forbiddenStatusCode = Constants::forbiddenStatusCode;

        $this->loginUserIdParaApi = ps_constant::loginUserIdParaFromApi;
        $this->deviceTokenParaApi = ps_constant::deviceTokenKeyFromApi;

        $this->userAccessApiTokenService = $userAccessApiTokenService;
        $this->chatService = $chatService;

        $this->chatNotiIdCol = ChatNoti::id;
    }

    public function store($request)
    {
        DB::beginTransaction();

        try {
            $noti = new PushNotificationUser();

            if (isset($request->device_token) && !empty($request->device_token))
                $noti->device_token = $request->device_token;

            if (isset($request->user_id) && !empty($request->user_id))
                $noti->user_id = $request->user_id;

            if (isset($request->noti_id) && !empty($request->noti_id))
                $noti->noti_id = $request->noti_id;

            $noti->added_user_id = Auth::user()->id;
            $noti->save();

            DB::commit();
            return $noti;
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];

        }
    }

    public function isReadFromApi($request){

        // check permission start
        $deviceToken = $request->header($this->deviceTokenParaApi);
        $userAccessApiToken = $this->userAccessApiTokenService->getUserAccessApiToken( $request->login_user_id, $deviceToken);

        if (empty($userAccessApiToken)){
            $msg = __('core__api_update_no_permission',[],$request->language_symbol);
            return ['error' =>  $msg,'status' =>  $this->forbiddenStatusCode ];
        }
        // check permission end

        $noti_type = $request->noti_type;

        if($noti_type == 'PUSH_NOTI'){
            $push_noti = $this->pushNotificationMessageService->getPushNotificationMessage($request->noti_id);
            if (!$push_noti) {
                return  ['error' => __('noti_read__api_invalid_noti_id',[],$request->language_symbol) ,'status' =>  $this->badRequestStatusCode ];
            } else {

                $conds['device_token'] = $request->device_token;
                $conds['user_id'] = $request->user_id;
                $conds['noti_id'] = $request->noti_id;
                $noti = PushNotificationUser::where($conds)->count();

                if ($noti == 0) {
                    $push_noti_token = $this->store($request);
                }

                if(isset($push_noti_token['error'])){
                    return  ['error' => __('noti_read__api_db_error',[],$request->language_symbol) ,'status' =>  $this->internalServerErrorStatusCode ];
                } else {
                    $id = $request->noti_id;
                    $pushNotiMessageApiRelation = $this->pushNotiMessageApiRelation;
                    $push_noti_token = $this->pushNotificationMessageService->getPushNotificationMessage($id, $pushNotiMessageApiRelation);
                }
                return  $push_noti_token;
            }
        }else{
            $chatNoti = new stdClass;
            $chatNoti->is_read = 1;
            $chatNoti->id = $request->noti_id;
            $chatNoti->updated_user_id= $request->login_user_id;
            $chatNoti->updated_date = Carbon::now();
            $data = $this->chatService->updateChatNoti($chatNoti);
            return $data;
        }
    }

    public function isUnreadFromApi($request){

        // check permission start
        $deviceToken = $request->header($this->deviceTokenParaApi);
        $userAccessApiToken = $this->userAccessApiTokenService->getUserAccessApiToken( $request->login_user_id, $deviceToken);

        if (empty($userAccessApiToken)){
            $msg = __('core__api_update_no_permission',[],$request->language_symbol);
            return ['error' =>  $msg,'status' =>  $this->forbiddenStatusCode ];
        }
        // check permission end

        $noti_type = $request->noti_type;

        if($noti_type == 'PUSH_NOTI'){
            $push_noti = $this->pushNotificationMessageService->getPushNotificationMessage($request->noti_id);
            if (!$push_noti) {
                return  ['error' => __('noti_read__api_invalid_noti_id',[],$request->language_symbol) ,'status' =>  $this->badRequestStatusCode ];
            } else {

                $conds['device_token'] = $request->device_token;
                $conds['user_id'] = $request->user_id;
                $conds['noti_id'] = $request->noti_id;
                $noti = PushNotificationUser::where($conds)->forceDelete();

                $id = $request->noti_id;
                $pushNotiMessageApiRelation = $this->pushNotiMessageApiRelation;
                $push_noti_token = $this->pushNotificationMessageService->getPushNotificationMessage($id, $pushNotiMessageApiRelation);

                return  $push_noti_token;
            }
        }else{
            $chatNoti = new stdClass;
            $chatNoti->is_read = 0;
            $chatNoti->id = $request->noti_id;
            $chatNoti->updated_user_id= $request->login_user_id;
            $chatNoti->updated_date = Carbon::now();
            $data = $this->chatService->updateChatNoti($chatNoti);
            return $data;
        }
    }

    public function destroyFromApi($request){
        /// check permission start
        $deviceToken = $request->header($this->deviceTokenParaApi);
        $userId = $request->login_user_id;

        // user token layer permission start
        $userAccessApiToken = $this->userAccessApiTokenService->getUserAccessApiToken($userId, $deviceToken);
        // user token layer permission end

        /// check permission end

        if (empty($userAccessApiToken)){
            $msg = __('core__api_no_permission',[],$request->language_symbol);
            return responseMsgApi($msg, $this->forbiddenStatusCode);
        }else{
            //delete
            $noti_type = $request->noti_type;
            if($noti_type == 'PUSH_NOTI'){

                $conds['device_token'] = $request->device_token;
                $conds['user_id'] = $request->user_id;
                $conds['noti_id'] = $request->noti_id;
                $push_noti_token = PushNotificationUser::where($conds)->onlyTrashed()->count();

                if($push_noti_token == 0){
                    $push_noti_token = $this->store($request);
                    $push_noti_token->delete();
                }
            }else{
                ChatNoti::where($this->chatNotiIdCol, $request->noti_id)->delete();
            }
            return  ['success' =>  __('core__api_noti_delete_success',[],$request->language_symbol),'status' =>  $this->okStatusCode ];
        }
    }
}
