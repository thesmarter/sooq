<?php

namespace Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\Category;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Http\Services\CategoryService;
use Modules\Core\Transformers\Api\App\V1_0\Category\CategoryApiResource;
use Modules\Core\Constants\Constants;

class CategoryApiController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
        $this->badRequestStatusCode = Constants::badRequestStatusCode;
    }

    public function search(Request $request)
    {
        $categories = $this->categoryService->searchFromApi($request);
        if(isset($categories['error'])) {
            if(isset($categories['status'])){
                return responseMsgApi($categories['error'], $categories['status']);
            }else{
                return responseMsgApi($categories['error'], $this->badRequestStatusCode);
            }
            
        } else  {
            $data = CategoryApiResource::collection($categories);

            $hasError = $this->categoryService->noDataError( $request->offset, $data);

            if($hasError)
                return $hasError;
            else {
                return $data;
            }
        }
    }

}
