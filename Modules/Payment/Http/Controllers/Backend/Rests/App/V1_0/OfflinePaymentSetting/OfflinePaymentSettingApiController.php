<?php

namespace Modules\Payment\Http\Controllers\Backend\Rests\App\V1_0\OfflinePaymentSetting;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Payment\Http\Services\OfflinePaymentSettingService;

class OfflinePaymentSettingApiController extends Controller
{
    protected $offlinePaymentSettingService;

    public function __construct(OfflinePaymentSettingService $offlinePaymentSettingService)
    {
        $this->offlinePaymentSettingService = $offlinePaymentSettingService;
    }

    public function index(Request $request)
    {
        $offlinePayments = $this->offlinePaymentSettingService->indexFromApi($request);
        return $offlinePayments;
    }
}
