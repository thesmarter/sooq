<?php

namespace Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\MobileLanguageString;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Http\Services\MobileLanguageStringService;
use Modules\Core\Constants\Constants;

class MobileLanguageStringApiController extends Controller
{

    protected $mobileLanguageStringService;

    public function __construct(MobileLanguageStringService $mobileLanguageStringService)
    {
        $this->mobileLanguageStringService = $mobileLanguageStringService;
        $this->badRequestStatusCode = Constants::badRequestStatusCode;
    }

    public function index(Request $request)
    {
        $mobileLanguageStrings = $this->mobileLanguageStringService->indexFromApi($request);
        if(isset($mobileLanguageStrings['error'])) {
            if(isset($mobileLanguageStrings['status'])){
                return responseMsgApi($mobileLanguageStrings['error'], $mobileLanguageStrings['status']);
            }else{
                return responseMsgApi($mobileLanguageStrings['error'], $this->badRequestStatusCode);
            }
            
        } else  {
            return responseDataApi($mobileLanguageStrings);

        }
    }
}
