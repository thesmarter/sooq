<?php

namespace Modules\Rating\Http\Controllers\Backend\Rests\App\V1_0;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Modules\Rating\Http\Services\RatingService;
use Illuminate\Support\Facades\Validator;
use Modules\Core\Constants\Constants;
use Modules\Rating\Transformers\Api\App\V1_0\RatingApiResource;
use Illuminate\Contracts\Translation\Translator;

class RatingApiController extends Controller
{
    protected $translator,$ratingService, $badRequestStatusCode;

    public function __construct(Translator $translator,RatingService $ratingService)
    {
        $this->ratingService = $ratingService;
        $this->badRequestStatusCode = Constants::badRequestStatusCode;
        $this->successStatus = Constants::successStatus;
        $this->badRequestStatusCode = Constants::badRequestStatusCode;
        $this->translator = $translator;
    }

    public function rating(Request $request)
    {
        //prepare validation
        $validator = Validator::make($request->all(), [
            'from_user_id' => 'required',
            'to_user_id' => 'required',
            'rating' => 'required',
            'type' => 'required',
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

        $response = $this->ratingService->ratingFromApi($request);
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

            $data = new RatingApiResource($response);
            return responseDataApi($data);

        }
    }

    public function search(Request $request)
    {
        $ratings = $this->ratingService->searchFromApi($request);
        $data = RatingApiResource::collection($ratings);

        $hasError = $this->ratingService->noDataError( $request->offset, $data);

        if($hasError)
            return $hasError;
        else {
            return $data;
        }
    }
}
