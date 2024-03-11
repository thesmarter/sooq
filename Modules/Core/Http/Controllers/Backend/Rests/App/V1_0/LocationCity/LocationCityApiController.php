<?php

namespace Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\LocationCity;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Http\Services\LocationCityService;
use Modules\Core\Constants\Constants;
use Modules\Core\Transformers\Api\App\V1_0\LocationCity\LocationCityApiResource;

class LocationCityApiController extends Controller
{
    protected $locationCityService;

    public function __construct(LocationCityService $locationCityService)
    {
        $this->locationCityService = $locationCityService;
        $this->badRequestStatusCode = Constants::badRequestStatusCode;
    }

    public function search(Request $request)
    {
        $cities = $this->locationCityService->searchFromApi($request);
        if(isset($cities['error'])) {
            if(isset($cities['status'])){
                return responseMsgApi($cities['error'], $cities['status']);
            }else{
                return responseMsgApi($cities['error'], $this->badRequestStatusCode);
            }
            
        } else  {
            $data = LocationCityApiResource::collection($cities);

            $hasError = $this->locationCityService->noDataError( $request->offset, $data);

            if($hasError)
                return $hasError;
            else {
                return $data;
            }
        }
    }
}
