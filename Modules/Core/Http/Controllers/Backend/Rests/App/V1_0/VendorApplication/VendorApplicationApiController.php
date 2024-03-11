<?php

namespace Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\VendorApplication;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Constants\Constants;
use Modules\Core\Http\Services\VendorApplicationService;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;
use Modules\Core\Transformers\Api\App\V1_0\Vendor\VendorApiResource;

class VendorApplicationApiController extends Controller
{
    protected $vendorApplicationService, $successStatus, $okStatusCode, $errorStatus, $internalServerErrorStatusCode, $translator, $badRequestStatusCode;

    public function __construct(VendorApplicationService $vendorApplicationService, Translator $translator)
    {
        $this->vendorApplicationService = $vendorApplicationService;
        $this->translator = $translator;

        $this->successStatus = Constants::successStatus;
        $this->errorStatus = Constants::errorStatus;
        $this->okStatusCode = Constants::okStatusCode;
        $this->badRequestStatusCode = Constants::badRequestStatusCode;
        $this->internalServerErrorStatusCode = Constants::internalServerErrorStatusCode;
    }

    public function submitApplication(Request $request)
    {
        // dd($request->all());
        $id = $request->id;
        $vendorId = '';
        if(!empty($id)){
            $vendorApplication = $this->vendorApplicationService->getVendorApplication($id);
            if(empty($vendorApplication)){
                return responseMsgApi(__('vendor_does_not_exist'), $this->badRequestStatusCode);
            }
            $vendorId = $vendorApplication->vendor_id;
            $request['vendor_id'] = $vendorId;
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'store_name' => 'required|min:3|unique:psx_vendors,name,'.$vendorId,
            'cover_letter' => 'required',
            'document' => empty($id) ? 'required|mimes:pdf,zip' : 'nullable|mimes:pdf,zip',
        ],[
            'document.mimes' => 'Business Document must be PDF or Zip.'
        ]);

        if ($request->language_symbol) {
            $this->translator->setLocale($request->language_symbol);
            $validator->setTranslator($this->translator);
        }

        $msg = implode("\n", Arr::flatten($validator->getMessageBag()->getMessages()));

        if ($validator->fails()) {
            return responseMsgApi($msg, $this->badRequestStatusCode);
        }

        $request['owner_user_id'] = $request->login_user_id;

        if(empty($request->id)){
            $dataArr = $this->vendorApplicationService->submitApplication($request);

            if(!isset($dataArr['error'])){
                $vendor = new VendorApiResource($dataArr);
                return responseDataApi($vendor);
                // return responseMsgApi(__('application_success'), $this->okStatusCode, $this->successStatus);
            }

            return responseMsgApi($dataArr['error'], $this->internalServerErrorStatusCode, $this->errorStatus);
        }else{
            $dataArr = $this->vendorApplicationService->updateApplication($request);

            if(!isset($dataArr['error'])){
                $vendor = new VendorApiResource($dataArr);
                return responseDataApi($vendor);
                // return responseMsgApi(__('application_success'), $this->okStatusCode, $this->successStatus);
            }else if(isset($dataArr['message'])){
                return responseMsgApi($dataArr['message'], $this->badRequestStatusCode);
            }

            return responseMsgApi($dataArr['error'], $this->internalServerErrorStatusCode, $this->errorStatus);
        }

    }
}
