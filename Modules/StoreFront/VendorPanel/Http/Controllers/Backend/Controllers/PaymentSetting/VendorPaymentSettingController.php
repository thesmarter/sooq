<?php

namespace Modules\StoreFront\VendorPanel\Http\Controllers\Backend\Controllers\PaymentSetting;

use App\Config\ps_constant;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Constants\Constants;
use Modules\Core\Http\Services\AvailableCurrencyService;
use Modules\Payment\Http\Services\PaymentService;
use Modules\Payment\Http\Services\PaymentSettingService;
use Modules\StoreFront\VendorPanel\Http\Services\VendorPaymentSettingService;
use stdClass;

class VendorPaymentSettingController extends Controller
{
    const parentPath = "payment/payment_setting/";
    const indexPath = self::parentPath . "Index";
    const indexRoute = "payment_setting.index";
    const createRoute = "payment_setting.edit";
    const editRoute = "payment_setting.edit";

    protected $paymentService, $paymentSettingService, $vendorPaymentSettingService, $successFlag, $dangerFlag, $default, $unDefault, $publish, $availableCurrencyService;

    public function __construct(PaymentSettingService $paymentSettingService, VendorPaymentSettingService $vendorPaymentSettingService, AvailableCurrencyService $availableCurrencyService, PaymentService $paymentService)
    {
        $this->paymentSettingService = $paymentSettingService;
        $this->availableCurrencyService = $availableCurrencyService;
        $this->paymentService = $paymentService;
        $this->vendorPaymentSettingService = $vendorPaymentSettingService;

        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;
        $this->publish = Constants::publish;
        $this->default = Constants::default;
        $this->unDefault = Constants::unDefault;
    }

    public function index(Request $request)
    {
        return $this->vendorPaymentSettingService->index($request);
    }

    public function store(Request $request, $vendorId)
    {
        return $this->vendorPaymentSettingService->store($request, $vendorId);
    }
}
