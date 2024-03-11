<?php

namespace Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\Vendor;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Constants\Constants;
use Modules\Core\Http\Services\VendorService;
use Modules\Core\Transformers\Api\App\V1_0\Vendor\VendorApiResource;

class VendorApiController extends Controller
{
    protected $vendorService, $successStatus, $okStatusCode, $errorStatus, $internalServerErrorStatusCode;

    public function __construct(VendorService $vendorService)
    {
        $this->vendorService = $vendorService;

        $this->successStatus = Constants::successStatus;
        $this->errorStatus = Constants::errorStatus;
        $this->okStatusCode = Constants::okStatusCode;
        $this->internalServerErrorStatusCode = Constants::internalServerErrorStatusCode;
    }

    public function getVendors(Request $request)
    {
        $vendors = $this->vendorService->getVendorsFromApi($request);

        return VendorApiResource::collection($vendors);
    }

    public function getVendorById(Request $request)
    {
        $vendor = $this->vendorService->getVendor($request->id);

        return new VendorApiResource($vendor);
    }

    public function getVendorBranches(Request $request)
    {
        return $this->vendorService->getVendorBranchesFromApi($request);
    }

    public function search(Request $request)
    {
        $items = $this->vendorService->searchFromApi($request);

        $data = VendorApiResource::collection($items);

        $hasError = $this->vendorService->noDataError($request->offset, $data);

        if ($hasError)
            return $hasError;
        else {
            return $data;
        }
    }

}
