<?php

namespace Modules\StoreFront\VendorPanel\Http\Controllers\Backend\Rests\App\V1_0\VendorInfo;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Support\Arr;
use Modules\Core\Constants\Constants;
use Modules\StoreFront\VendorPanel\Http\Services\VendorInfoService;

class VendorInfoApiController extends Controller
{
    protected $vendorInfoService, $translator;

    public function __construct(Translator $translator, VendorInfoService $vendorInfoService)
    {
        $this->vendorInfoService = $vendorInfoService;
        $this->translator = $translator;
    }

    public function getVendorInfo(Request $request)
    {
         //prepare validation
         $validator = Validator::make($request->all(), [
            'vendor_id' => 'required|exists:psx_vendors,id',
        ]);

        if($request->language_symbol){
            $this->translator->setLocale($request->language_symbol);
            $validator->setTranslator($this->translator);
        }

        if ($validator->fails()) {
            return responseMsgApi(implode("\n", Arr::flatten($validator->getMessageBag()->getMessages())), Constants::badRequestStatusCode);
        }
        /// validation end

        return $this->vendorInfoService->getVendorInfoFromApi($request);
    }

}
