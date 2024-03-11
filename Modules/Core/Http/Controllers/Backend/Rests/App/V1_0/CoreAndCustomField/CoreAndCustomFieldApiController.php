<?php

namespace Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\CoreAndCustomField;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Constants\Constants;
use Modules\Core\Http\Services\CoreAndCustomFieldService;

class CoreAndCustomFieldApiController extends Controller
{
    protected $coreAndCustomFieldService, $okStatusCode, $sucessStatus, $errorStatus, $internalServerErrorStatusCode;
    public function __construct(CoreAndCustomFieldService $coreAndCustomFieldService){
        $this->coreAndCustomFieldService = $coreAndCustomFieldService;
        $this->okStatusCode = Constants::okStatusCode;
        $this->sucessStatus = Constants::successStatus;
        $this->errorStatus = Constants::errorStatus;
        $this->internalServerErrorStatusCode = Constants::internalServerErrorStatusCode;

    }

    public function syncFields(Request $request){
        $response = $this->coreAndCustomFieldService->syncFieldFromApi($request);
        return $response;
    }
}
