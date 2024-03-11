<?php

namespace Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\Subcategory;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Http\Services\SubcategoryService;
use Modules\Core\Transformers\Api\App\V1_0\Subcategory\SubcategoryApiResource;
use Modules\Core\Constants\Constants;

class SubcategoryApiController extends Controller
{


    protected $subCategoryService;

    public function __construct(SubcategoryService $subCategoryService)
    {
        $this->subCategoryService = $subCategoryService;
        $this->badRequestStatusCode = Constants::badRequestStatusCode;
    }

    public function search(Request $request)
    {
        $subcategories = $this->subCategoryService->searchFromApi($request);

        if(isset($subcategories['error'])) {
            if(isset($subcategories['status'])){
                return responseMsgApi($subcategories['error'], $subcategories['status']);
            }else{
                return responseMsgApi($subcategories['error'], $this->badRequestStatusCode);
            }

        } else  {

            $data = SubcategoryApiResource::collection($subcategories);

            $hasError = $this->subCategoryService->noDataError( $request->offset, $data);

            if($hasError)
                return $hasError;
            else {
                return $data;
            }
        }
    }

}
