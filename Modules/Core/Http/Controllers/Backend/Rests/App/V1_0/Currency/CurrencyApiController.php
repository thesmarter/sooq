<?php

namespace Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\Currency;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Modules\Core\Entities\Currency;
use Modules\Core\Http\Services\CurrencyService;
use Modules\Core\Transformers\Api\App\V1_0\Currency\CurrencyApiResource;
use Modules\Core\Constants\Constants;

class CurrencyApiController extends Controller
{

    protected $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
        $this->badRequestStatusCode = Constants::badRequestStatusCode;
    }

    public function index(Request $request)
    {
        $currencies = $this->currencyService->indexFromApi($request);

        if(isset($currencies['error'])) {
            if(isset($currencies['status'])){
                return responseMsgApi($currencies['error'], $currencies['status']);
            }else{
                return responseMsgApi($currencies['error'], $this->badRequestStatusCode);
            }
            
        } else  {

            $data = CurrencyApiResource::collection($currencies);

            $hasError = $this->currencyService->noDataError( $request->offset, $data);

            if($hasError)
                return $hasError;
                else {
                return $data;
            }
        }

    }

}    