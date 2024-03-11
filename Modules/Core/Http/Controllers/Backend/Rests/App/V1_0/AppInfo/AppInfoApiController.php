<?php

namespace Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\AppInfo;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Http\Services\AppInfoService;

class AppInfoApiController extends Controller
{
    protected $appInfoService;
    
    public function __construct(AppInfoService $appInfoService)
    {   
        $this->appInfoService = $appInfoService;
    }

    public function appInfo(Request $request)
    {
        $appInfo = $this->appInfoService->indexFromApi($request);
        return $appInfo;

    }

}
