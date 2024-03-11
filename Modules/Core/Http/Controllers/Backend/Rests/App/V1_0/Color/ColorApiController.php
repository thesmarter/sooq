<?php

namespace Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\Color;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\Color;
use Modules\Core\Constants\Constants;
use Modules\Core\Http\Services\ColorService;
use Modules\Core\Transformers\Api\App\V1_0\Color\ColorApiResource;

class ColorApiController extends Controller
{
    protected $colorService, $badRequestStatusCode;

    public function __construct(ColorService $colorService)
    {
        $this->colorService = $colorService;
        $this->badRequestStatusCode = Constants::badRequestStatusCode;
    }

    public function index(Request $request)
    {
        $data = new ColorApiResource('colors');
        return $data;
    }

}
