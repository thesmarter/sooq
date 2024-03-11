<?php

namespace Modules\PushNotificationMessage\Http\Controllers\Backend\Rests\App\V1_0;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Chat\Http\Services\ChatService;
use Modules\PushNotificationMessage\Http\Services\PushNotificationMessageService;
use Modules\PushNotificationMessage\Transformers\Api\App\V1_0\PushNotificationMessageApiResource;
use Modules\Core\Constants\Constants;
use stdClass;

class PushNotificationMessageApiController extends Controller
{
    protected $pushNotificationMessageService, $chatService;

    public function __construct(PushNotificationMessageService $pushNotificationMessageService, ChatService $chatService)
    {
        $this->pushNotificationMessageService = $pushNotificationMessageService;
        $this->chatService = $chatService;

        $this->okStatusCode = Constants::okStatusCode;
        $this->successStatus = Constants::successStatus;
        $this->badRequestStatusCode = Constants::badRequestStatusCode;
    }

    public function allNotis(Request $request)
    {
        $response = collect();
        if(isset($request->login_user_id) && !empty($request->login_user_id)){
            $loginUserID = $request->login_user_id;
            $conds['login_user_id'] = $loginUserID;
            $chatMessages = $this->chatService->getChatNotis($conds);
        }
        $pushNotis = $this->pushNotificationMessageService->allNotisFromApi($request);

        foreach ($pushNotis as $pushNoti)
            $response->push($pushNoti);
        foreach ($chatMessages as $chatMessage)
            $response->push($chatMessage);

        $response = ($response->sortByDesc('added_date'));
        
        if(isset($response['error'])) {
            if(isset($response['status'])){
                return responseMsgApi($response['error'], $response['status']);
            }else{
                return responseMsgApi($response['error'], $this->badRequestStatusCode);
            }
            
        }else if(isset($response['success'])) {
            if(isset($response['status'])){
                return responseMsgApi($response['success'], $response['status'], $this->successStatus);
            }else{
                return responseMsgApi($response['success'], $this->okStatusCode, $this->successStatus);
            }
            
        } else  {
            $data = PushNotificationMessageApiResource::collection($response);

            $hasError = $this->pushNotificationMessageService->noDataError( $request->offset, $data);

            if($hasError)
                return $hasError;
            else {
                return $data;
            }
        }
    }

    public function getNotiDetail(Request $request){
        // echo json_encode($request->all());exit;
        $response = $this->pushNotificationMessageService->getByIdFromApi($request);

        if(isset($response['error'])) {
            if(isset($response['status'])){
                return responseMsgApi($response['error'], $response['status']);
            }else{
                return responseMsgApi($response['error'], $this->badRequestStatusCode);
            }

        }else if(isset($response['success'])) {
            if(isset($response['status'])){
                return responseMsgApi($response['success'], $response['status'], $this->successStatus);
            }else{
            return responseMsgApi($response['success'], $this->okStatusCode, $this->successStatus);
            }

        } else  {

            $pushNotificationMessage = new PushNotificationMessageApiResource($response);
            return responseDataApi($pushNotificationMessage);
        }
    }
}
