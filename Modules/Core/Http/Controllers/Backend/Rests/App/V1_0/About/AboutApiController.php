<?php

namespace Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\About;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Http\Services\AboutService;

class AboutApiController extends Controller
{
    protected $categoryService;

    public function __construct(AboutService $aboutService)
    {
        $this->aboutService = $aboutService;
    }

    public function index(Request $request)
    {
        $about = $this->aboutService->indexFromApi($request);
        return $about;
    }
}
