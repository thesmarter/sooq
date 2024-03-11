<?php

namespace Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\LocationTownship;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Http\Services\LocationTownshipService;
use Modules\Core\Constants\Constants;
use Modules\Core\Transformers\Api\App\V1_0\LocationTownship\LocationTownshipApiResource;

class LocationTownshipApiController extends Controller
{
    protected $locationTownshipService;

    public function __construct(LocationTownshipService $locationTownshipService)
    {
        $this->locationTownshipService = $locationTownshipService;
        $this->badRequestStatusCode = Constants::badRequestStatusCode;
    }

    public function search(Request $request)
    {
        $townships = $this->locationTownshipService->searchFromApi($request);
        if(isset($townships['error'])) {
            if(isset($townships['status'])){
                return responseMsgApi($townships['error'], $townships['status']);
            }else{
                return responseMsgApi($townships['error'], $this->badRequestStatusCode);
            }
            
        } else  {
            $data = LocationTownshipApiResource::collection($townships);

            $hasError = $this->locationTownshipService->noDataError( $request->offset, $data);

            if($hasError)
                return $hasError;
            else {
                return $data;
            }
        }
    }
}
