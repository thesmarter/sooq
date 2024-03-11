<?php

namespace Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\PushNotificationToken;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Modules\Core\Entities\PushNotificationToken;
use Modules\Core\Http\Requests\StorePushNotiTokenRequest;
use Modules\Core\Http\Services\PushNotificationTokenService;
use Modules\Core\Constants\Constants;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;
use Illuminate\Contracts\Translation\Translator;

class PushNotificationTokenApiController extends Controller
{

    protected $translator,$pushNotificationTokenService;

    public function __construct(Translator $translator,PushNotificationTokenService $pushNotificationTokenService)
    {
        $this->pushNotificationTokenService = $pushNotificationTokenService;
        $this->badRequestStatusCode = Constants::badRequestStatusCode;
        $this->okStatusCode = Constants::okStatusCode;
        $this->successStatus = Constants::successStatus;
        $this->translator = $translator;
    }

    public function registerNoti(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_token' => 'required',
            'platform_name' => 'required',
        ]);

        if($request->language_symbol){
            $this->translator->setLocale($request->language_symbol);
            $validator->setTranslator($this->translator);
        }

        $msg = implode("\n", Arr::flatten($validator->getMessageBag()->getMessages()));

        if ($validator->fails()) {
            return responseMsgApi($msg, $this->badRequestStatusCode);
        }

        $response = $this->pushNotificationTokenService->registerFromApi($request);
        if(isset($response['error'])) {
            if(isset($response['status'])){
                return responseMsgApi($response['error'], $response['status']);
            }else{
                return responseMsgApi($response['error'], $this->badRequestStatusCode);
            }
            
        }else{
            if(isset($response['status'])){
                return responseMsgApi($response['success'], $response['status'], $this->successStatus);
            }else{
                return responseMsgApi($response['success'], $this->okStatusCode, $this->successStatus);
            }
            
        }
    }

    public function unregisterNoti(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_token' => 'required',
        ]);

        if($request->language_symbol){
            $this->translator->setLocale($request->language_symbol);
            $validator->setTranslator($this->translator);
        }

        $msg = implode("\n", Arr::flatten($validator->getMessageBag()->getMessages()));

        if ($validator->fails()) {
            return responseMsgApi($msg, $this->badRequestStatusCode);
        }

        $response = $this->pushNotificationTokenService->unregisterFromApi($request);
        if(isset($response['error'])) {
            if(isset($response['status'])){
                return responseMsgApi($response['error'], $response['status']);
            }else{
                return responseMsgApi($response['error'], $this->badRequestStatusCode);
            }
            
        }else{
            if(isset($response['status'])){
                return responseMsgApi($response['success'], $response['status'], $this->successStatus);
            }else{
                return responseMsgApi($response['success'], $this->okStatusCode, $this->successStatus);
            }
            
        }
    }

    // test status code dummy api temporary

    public function testStatusCode(Request $request)
    {
        $tests = $this->pushNotificationTokenService->testStatusCode($request);
        return $tests;
    }
}
