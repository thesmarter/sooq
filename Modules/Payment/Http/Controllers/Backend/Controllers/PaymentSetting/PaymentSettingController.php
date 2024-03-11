<?php

namespace Modules\Payment\Http\Controllers\Backend\Controllers\PaymentSetting;

use App\Config\ps_constant;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Constants\Constants;
use Modules\Core\Http\Services\AvailableCurrencyService;
use Modules\Payment\Http\Services\PaymentService;
use Modules\Payment\Http\Services\PaymentSettingService;
use stdClass;

class PaymentSettingController extends Controller
{
    const parentPath = "payment/payment_setting/";
    const indexPath = self::parentPath . "Index";
    const indexRoute = "payment_setting.index";
    const createRoute = "payment_setting.edit";
    const editRoute = "payment_setting.edit";

    protected $paymentService, $paymentSettingService, $successFlag, $dangerFlag, $default, $unDefault, $publish, $availableCurrencyService;

    public function __construct(PaymentSettingService $paymentSettingService, AvailableCurrencyService $availableCurrencyService, PaymentService $paymentService)
    {
        $this->paymentSettingService = $paymentSettingService;
        $this->availableCurrencyService = $availableCurrencyService;
        $this->paymentService = $paymentService;

        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;
        $this->publish = Constants::publish;
        $this->default = Constants::default;      
        $this->unDefault = Constants::unDefault;
    }

    public function index(Request $request)
    {
        $dataArr = $this->paymentSettingService->index($request);
        if (!permissionControl(Constants::paymentSettingModule,ps_constant::readPermission)){
            return redirect()->route('admin.index');
        }
        return renderView(self::indexPath, $dataArr);
    }

    public function store(Request $request, $currency_id)
    {
        if($currency_id){
            $this->availableCurrencyService->clearDefault();
            $currency = $this->availableCurrencyService->getAvailableCurrency($currency_id);
            $currency->status = $this->publish;
            $currency->is_default = $this->default;
            $currency->updated_user_id = Auth::user()->id;
            $currency->update();
        }

        for ($i = 0; $i < count((array)$request); $i++) {
            
            // payment status update
            if(isset($request[$i]['status'])){
                $payment = new \stdClass();
                $payment->status = $request[$i]['status']?'1':'0';
                $this->paymentService->update($request[$i], $payment);
            }

            // update payment info
            if(isset($request[$i]['payment_infos'])){
                $payment_infos = $request[$i]['payment_infos'];
                for ($j = 0; $j < count($payment_infos); $j++) {
                    $paymentInfo = new \stdClass();
                    $paymentInfo->core_keys_id = $payment_infos[$j]['core_keys_id'];
                    $paymentInfo->payment_id = $payment_infos[$j]['payment_id'];
                    
                    if(isset($payment_infos[$j]['shop_id']) && empty($payment_infos[$j]['shop_id']))
                        $paymentInfo->shop_id = $payment_infos[$j]['shop_id']??$payment_infos[$j]['shop_id'];
                    
                    $paymentInfo->value = $payment_infos[$j]['value'];
                    $payment_setting = $this->paymentSettingService->update($payment_infos[$j]['id'], $paymentInfo);

                    // if have error
                    if (isset($payment_setting['error'])) {
                        $msg = $payment_setting['error'];
                        return redirectView(self::indexRoute, $msg, $this->dangerFlag);
                    }
                }
            }            
        }

        $msg = 'The payment setting has been updated successfully.';
        return redirectView(self::indexRoute, $msg);
    }
}
