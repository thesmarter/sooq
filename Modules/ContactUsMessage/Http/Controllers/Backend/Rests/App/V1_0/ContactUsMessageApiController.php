<?php

namespace Modules\ContactUsMessage\Http\Controllers\Backend\Rests\App\V1_0;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Modules\ContactUsMessage\Http\Services\ContactService;
use Modules\Core\Constants\Constants;
use Illuminate\Support\Facades\Validator;
use Modules\ContactUsMessage\Transformers\Api\App\V1_0\ContactUsMessageApiResource;
use Illuminate\Contracts\Translation\Translator;

class ContactUsMessageApiController extends Controller
{
    protected $translator,$contactService, $badRequestStatusCode;

    public function __construct(Translator $translator,ContactService $contactService)
    {
        $this->contactService = $contactService;
        $this->badRequestStatusCode = Constants::badRequestStatusCode;
        $this->okStatusCode = Constants::okStatusCode;
        $this->successStatus = Constants::successStatus;
        $this->translator = $translator;
    }

    public function contact(Request $request)
    {
        //prepare validation
        $validator = Validator::make($request->all(), [
            'contact_name' => 'required',
            'contact_email' => 'required',
            'contact_message' => 'required',
        ]);

        if($request->language_symbol){
            $this->translator->setLocale($request->language_symbol);
            $validator->setTranslator($this->translator);
        }

        $msg = implode("\n", Arr::flatten($validator->getMessageBag()->getMessages()));

        if ($validator->fails()) {
            return responseMsgApi($msg, $this->badRequestStatusCode);
        }
        /// validation end

        $response = $this->contactService->contactFromApi($request);

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

            $contactUs = new ContactUsMessageApiResource($response);
            return responseDataApi($contactUs);
        }
    }

    public function getInTouchForContact(Request $request){
        
        $data = $this->contactService->getInTouchForContactFromApi($request);
        return responseDataApi($data);
    }
}
