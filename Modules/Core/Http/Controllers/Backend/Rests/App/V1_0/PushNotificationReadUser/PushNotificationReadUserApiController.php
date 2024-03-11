<?php

namespace Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\PushNotificationReadUser;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Modules\Core\Constants\Constants;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;
use Modules\Core\Http\Services\PushNotificationReadUserService;
use Modules\PushNotificationMessage\Transformers\Api\App\V1_0\PushNotificationMessageApiResource;
use Illuminate\Contracts\Translation\Translator;

class PushNotificationReadUserApiController extends Controller
{
    protected $translator, $forbiddenStatusCode;

    public function __construct(Translator $translator,PushNotificationReadUserService $pushNotificationReadUserService)
    {
        $this->pushNotificationReadUserService = $pushNotificationReadUserService;
        $this->badRequestStatusCode = Constants::badRequestStatusCode;
        $this->okStatusCode = Constants::okStatusCode;
        $this->successStatus = Constants::successStatus;
        $this->translator = $translator;
        $this->forbiddenStatusCode = Constants::forbiddenStatusCode;
    }

    public function isReadNoti(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'noti_id' => 'required',
            'user_id' => 'required',
            'device_token' => 'required',
            'noti_type' => 'required',
        ]);

        if($request->language_symbol){
            $this->translator->setLocale($request->language_symbol);
            $validator->setTranslator($this->translator);
        }

        $msg = implode("\n", Arr::flatten($validator->getMessageBag()->getMessages()));

        if ($validator->fails()) {
            return responseMsgApi($msg, $this->badRequestStatusCode);
        }

        $checkUserByLoginUser = checkUserByLoginUser($request->user_id, $request->login_user_id);

        if(!$checkUserByLoginUser){
            return responseMsgApi(__('core__api_no_permission',[],$request->language_symbol), $this->forbiddenStatusCode);
        }

        $response = $this->pushNotificationReadUserService->isReadFromApi($request);
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

            $sendnoti = new PushNotificationMessageApiResource($response);
            return responseDataApi($sendnoti);
        }
    }

    public function isUnreadNoti(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'noti_id' => 'required',
            'user_id' => 'required',
            'device_token' => 'required',
            'noti_type' => 'required',
        ]);

        if($request->language_symbol){
            $this->translator->setLocale($request->language_symbol);
            $validator->setTranslator($this->translator);
        }

        $msg = implode("\n", Arr::flatten($validator->getMessageBag()->getMessages()));

        if ($validator->fails()) {
            return responseMsgApi($msg, $this->badRequestStatusCode);
        }

        $response = $this->pushNotificationReadUserService->isUnreadFromApi($request);
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
            $sendnoti = new PushNotificationMessageApiResource($response);
            return responseDataApi($sendnoti);
        }
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'noti_id' => 'required',
            'user_id' => 'required',
            'device_token' => 'required',
            'noti_type' => 'required',
        ]);

        if($request->language_symbol){
            $this->translator->setLocale($request->language_symbol);
            $validator->setTranslator($this->translator);
        }

        $msg = implode("\n", Arr::flatten($validator->getMessageBag()->getMessages()));

        if ($validator->fails()) {
            return responseMsgApi($msg, $this->badRequestStatusCode);
        }

        $msg = $this->pushNotificationReadUserService->destroyFromApi($request);

        if(isset($msg['error'])) {
            if(isset($msg['status'])){
                return responseMsgApi($msg['error'], $msg['status']);
            }else{
                return responseMsgApi($msg['error'], $this->notFoundStatusCode);
            }
        }
        if(isset($msg['success'])) {
            if(isset($msg['status'])){
                return responseMsgApi($msg['success'], $msg['status'], $this->successStatus);
            }else{
                return responseMsgApi($msg['success'], $this->okStatusCode, $this->successStatus);
            }
        }
    }
}
