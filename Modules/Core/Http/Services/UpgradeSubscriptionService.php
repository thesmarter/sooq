<?php

namespace Modules\Core\Http\Services;

use Carbon\Carbon;
use App\Config\ps_config;
use App\Config\ps_constant;
use Modules\Core\Entities\Item;
use App\Http\Services\PsService;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Constants\Constants;
use Modules\Core\Http\Services\AppInfoService;
use Modules\Payment\Http\Services\PaymentSettingService;
use Modules\Core\Transformers\Backend\Model\Vendor\VendorWithKeyResource;
use Modules\Package\Http\Services\VendorSubscriptionPlanBoughtTransactionService;
use Modules\Payment\Transformers\Backend\NoModel\VendorSubscriptionPlan\VendorSubscriptionPlanWithKeyResource;

class  UpgradeSubscriptionService extends PsService
{
    protected $vendorService, $paymentSettingService, $appInfoService, $vendorSubscriptionPlanBoughtTransactionService;

    public function __construct(VendorService $vendorService, PaymentSettingService $paymentSettingService, AppInfoService $appInfoService, VendorSubscriptionPlanBoughtTransactionService $vendorSubscriptionPlanBoughtTransactionService)
    {
        $this->vendorService = $vendorService;
        $this->paymentSettingService = $paymentSettingService;
        $this->appInfoService = $appInfoService;
        $this->vendorSubscriptionPlanBoughtTransactionService = $vendorSubscriptionPlanBoughtTransactionService;
    }

    // for Backend
    public function index()
    {
        $vendorId = getVendorIdFromSession();
        $vendor = new VendorWithKeyResource($this->vendorService->getVendor($vendorId));

        $conds['payment_id'] = Constants::vendorSubscriptionPlanPaymentId;
        $relations = ['core_key'];
        $attributes = [
            Constants::pmtAttrVendorSpDurationCol,
            Constants::pmtAttrVendorSpSalePriceCol,
            Constants::pmtAttrVendorSpDiscountPriceCol,
            Constants::pmtAttrVendorSpCurrencyCol,
            Constants::pmtAttrVendorSpIsMostPopularPlanCol,
            Constants::pmtAttrVendorSpStatusCol,
        ];
        $vendorSubscriptionPlans = VendorSubscriptionPlanWithKeyResource::collection($this->paymentSettingService->getPaymentInfos($relations, null, null, $conds, true, null, $attributes));
        $appInfo = $this->appInfoService->forVendor();
        // check permission
        $checkPermission = vendorPermissionControl(Constants::vendorItemModule, ps_constant::readPermission, $vendorId);

        $dataArr = [
            'checkPermission' => $checkPermission,
            'vendor' => $vendor,
            'appInfo' => $appInfo,
            'vendorSubscriptionPlans' => $vendorSubscriptionPlans
        ];

        return $dataArr;
    }

    public function upgradeSubscription($request)
    {
        $transaction = $this->vendorSubscriptionPlanBoughtTransactionService->upgradeSubscription($request);

        if(isset($transaction['error'])){
            return [
                'msg' => __($transaction['error']),
                'flag' => Constants::danger
            ];
        }
        $dataArr = [
            'msg' => __('vendor_subscription__transaction_success'),
            'flag' => Constants::successStatus,
        ];

        return $dataArr;
    }
}
