<?php

namespace Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\PhoneCountryCode;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Modules\Core\Entities\PhoneCountryCode;
use Modules\Core\Http\Services\PhoneCountryCodeService;
use Modules\Core\Transformers\Api\App\V1_0\PhoneCountryCode\PhoneCountryCodeApiResource;
use Modules\Core\Constants\Constants;

class PhoneCountryCodeApiController extends Controller
{

    protected $phoneCountryCodeService;

    public function __construct(PhoneCountryCodeService $phoneCountryCodeService)
    {
        $this->phoneCountryCodeService = $phoneCountryCodeService;
        $this->badRequestStatusCode = Constants::badRequestStatusCode;
    }

    public function search(Request $request)
    {
        $phoneCountryCodes = $this->phoneCountryCodeService->indexFromApi($request);

        if(isset($phoneCountryCodes['error'])) {
            if(isset($phoneCountryCodes['status'])){
                return responseMsgApi($phoneCountryCodes['error'], $phoneCountryCodes['status']);
            }else{
                return responseMsgApi($phoneCountryCodes['error'], $this->badRequestStatusCode);
            }
            
        } else  {

            $data = PhoneCountryCodeApiResource::collection($phoneCountryCodes);

            $hasError = $this->phoneCountryCodeService->noDataError( $request->offset, $data);

            if($hasError)
                return $hasError;
                else {
                return $data;
            }
        }

    }

}    