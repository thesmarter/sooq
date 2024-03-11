<?php

namespace Modules\DeeplinkGenerator\Http\Controllers\Backend\Controllers;

use Modules\Core\Constants\Constants;
use Illuminate\Routing\Controller;
use Modules\DeeplinkGenerator\Http\Services\DeeplinkService;
class DeeplinkController extends Controller
{

    const parentPath = "deeplink_generator/";
    const indexPath = self::parentPath."Index";
    const createPath = self::parentPath."Create";
    const editPath = self::parentPath."Edit";
    const indexRoute = "deeplink_generator.index";

    protected $deeplinkService, $dangerFlag;

    public function __construct(DeeplinkService $deeplinkService)
    {
        $this->deeplinkService = $deeplinkService;
        $this->dangerFlag = Constants::danger;
    }

    public function index()
    {
        $dataArr = $this->deeplinkService->index();
        return renderView(self::editPath, $dataArr);
    }

    public function deeplink()
    {
        $dataArr = $this->deeplinkService->deeplink();
        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);

    }
}
