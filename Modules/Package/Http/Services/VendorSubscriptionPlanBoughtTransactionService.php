<?php

namespace Modules\Package\Http\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Config\ps_constant;
use App\Http\Services\PsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Constants\Constants;
use Modules\Payment\Entities\PaymentInfo;
use Modules\Payment\Entities\PaymentAttribute;
use Modules\Core\Entities\SubscriptionBoughtTransaction;
use Modules\Payment\Http\Services\PaymentSettingService;
use Modules\Core\Http\Services\UserAccessApiTokenService;
use Modules\Core\Http\Services\VendorService;
use Modules\Package\Transformers\Api\App\V1_0\Package\PackageBoughtTransactionApiResource;
use Modules\Core\Transformers\Backend\NoModel\VendorReport\VendorSubscriptionTransactionWithKeyResource;
use stdClass;

class VendorSubscriptionPlanBoughtTransactionService extends PsService
{
    protected $userAccessApiTokenService, $paymentSettingService, $packageBoughtTransactionApiRelations, $vendorService;

    public function __construct(UserAccessApiTokenService $userAccessApiTokenService, PaymentSettingService $paymentSettingService, VendorService $vendorService)
    {
        $this->userAccessApiTokenService = $userAccessApiTokenService;
        $this->paymentSettingService = $paymentSettingService;
        $this->vendorService = $vendorService;

        $this->packageBoughtTransactionApiRelations = ['user', 'package'];
    }

    public function getPaymentInfoByPackageId($id){
        $package = PaymentInfo::find($id);
        return $package;
    }

    public function storeFromApi($request)
    {
        /// check permission start
        $deviceToken = $request->header(ps_constant::deviceTokenKeyFromApi);
        $userId = $request->login_user_id;

        // user token layer permission start
        $userAccessApiToken = $this->userAccessApiTokenService->getUserAccessApiToken($userId, $deviceToken);
        // user token layer permission end

        /// check permission end

        if (empty($userAccessApiToken)){
            $msg = __('package__api_create_no_permissin',[],$request->language_symbol);
            return responseMsgApi($msg, Constants::forbiddenStatusCode);
        }else{
            $paypal_result = 0;
            $stripe_result = 0;
            $razor_result = 0;
            $paystack_result = 0;
            $in_app_purchase_result = 0;
            $offline_result = 0;

            $data = new \stdClass();
            $data->user_id = $request->user_id;
            $data->vendor_id = $request->vendor_id;
            $data->subscription_plan_id = $request->subscription_plan_id;
            $data->razor_id = $request->razor_id;
            $data->price = $request->price;
            $data->is_paystack = $request->is_paystack;
            $data->status = 0;
            $data->transaction_id = Carbon::now()->getTimestamp();

            if ($request->payment_method == 'paypal') {
                //User using Paypal to submit the transaction
                $payment_method = Constants::paypalPaymentMethod;
                $gateway = new \Braintree\Gateway([
                    'environment' => trim($this->paymentSettingService->getPaymentInfo(null, null, Constants::paypalEnvironment)->value),
                    'merchantId' => trim($this->paymentSettingService->getPaymentInfo(null, null, Constants::paypalMerchantId)->value),
                    'publicKey' => trim($this->paymentSettingService->getPaymentInfo(null, null, Constants::paypalPublicKey)->value),
                    'privateKey' => trim($this->paymentSettingService->getPaymentInfo(null, null, Constants::paypalPrivateKey)->value)
                ]);

                $result = $gateway->transaction()->sale([
                    'amount'                => $request->price,
                    'paymentMethodNonce' => $request->payment_method_nonce,
                    'options' => [
                        'submitForSettlement' => True
                    ]
                ]);

                if ($result->success == 1) {
                    $data->payment_method = $request->payment_method;
                    $paypal_result = $result->success;
                } else {
                    $this->vendorService->destroy($request->vendor_id);
                    return responseMsgApi(__('package__api_paypal_transaction_fail',[],$request->language_symbol), Constants::internalServerErrorStatusCode);
                }
            } else if ($request->payment_method == 'stripe') {
                $payment_method = Constants::stripePaymentMethod;
                //User using Stripe to submit the transaction
                $payment_method_nonce = explode('_', $request->payment_method_nonce);

                if ($payment_method_nonce[0] == 'tok') {

                    try {

                        # set stripe test key
                        \Stripe\Stripe::setApiKey(trim($this->paymentSettingService->getPaymentInfo(null, null, Constants::stripeSecretKey)->value));

                        $charge = \Stripe\Charge::create(array(
                            "amount"       => $request->price * 100, // amount in cents, so need to multiply with 100 .. $amount * 100
                            "currency"    => trim('USD'),
                            "source"      => $request->payment_method_nonce,
                            "description" => __('package__api_order_desc',[],$request->language_symbol)
                        ));

                        if ($charge->status == "succeeded") {
                            $data->payment_method = $request->payment_method;
                            $stripe_result = 1;
                        } else {
                            $this->vendorService->destroy($request->vendor_id);
                            return responseMsgApi(__('package__api_stripe_transaction_failed',[],$request->language_symbol), Constants::internalServerErrorStatusCode);
                        }
                    } catch (\Throwable $e) {
                        $this->vendorService->destroy($request->vendor_id);
                        return responseMsgApi($e->getMessage(), Constants::internalServerErrorStatusCode);
                    }
                } else if ($payment_method_nonce[0] == 'pm') {
                    try {
                        \Stripe\Stripe::setApiKey(trim($this->paymentSettingService->getPaymentInfo(null, null, Constants::stripeSecretKey)->value));

                        $paymentIntent = \Stripe\PaymentIntent::create([
                            'payment_method' => $request->payment_method_nonce,
                            'amount' => $request->price * 100, // amount in cents, so need to multiply with 100 .. $amount * 100
                            'currency' => trim('USD'),
                            'confirmation_method' => 'manual',
                            'confirm' => true,
                        ]);

                        if ($paymentIntent->status == "succeeded") {
                            $data->payment_method = $request->payment_method;
                            $stripe_result = 1;
                        } else {
                            $this->vendorService->destroy($request->vendor_id);
                            return responseMsgApi(__('package__api_stripe_transaction_failed',[],$request->language_symbol), Constants::internalServerErrorStatusCode);
                        }
                    } catch (\Throwable $e) {
                        $this->vendorService->destroy($request->vendor_id);
                        return responseMsgApi($e->getMessage(), Constants::internalServerErrorStatusCode);
                    }
                }
            } else if ($request->payment_method == 'razor') {

                //User Using Razor
                $payment_method = Constants::razorPaymentMethod;
                $data->payment_method = $payment_method;
                $razor_result = 1;
            } else if ($request->payment_method == 'paystack') {

                //User Using Paystack
                $payment_method = Constants::paystackPaymentMethod;
                $data->payment_method = $payment_method;
                $paystack_result = 1;
            } else if ($request->payment_method == 'in app purchase') {

                //User Using COD
                $payment_method = Constants::iapPaymentMethod;
                $data->payment_method = "IAP";
                $in_app_purchase_result = 1;
            }
            if ($offline_result == 1) {
                $data->payment_method = $payment_method;
                $data->status = 0;
            }

            // save package bought transaction

            $transaction = $this->store($data);
            // dd($transaction);

            if ($paypal_result == 1 || $stripe_result == 1  || $razor_result == 1 || $paystack_result == 1 || $in_app_purchase_result == 1) {

                $packageBoughtTransactionApiRelations = $this->packageBoughtTransactionApiRelations;
                $packageDetail = $this->getPackageBoughtTransaction($transaction->id,null,$packageBoughtTransactionApiRelations);

                $objs  = PaymentAttribute::whereIn("core_keys_id", [$packageDetail->package->core_keys_id])->get();

                $attributes = $objs->map(function ($key, $value){
                    return [
                        $key['attribute_key'] => $key['attribute_value']
                    ];
                })->collapse();
                $duration = intval($attributes['duration']);

                // update vendor expired date
                $expired_date = $packageDetail->added_date->addMonths($duration);
                $vendorInfo = new \stdClass();
                $vendorInfo->expired_date = $expired_date->toDateTimeString();
                $vendorInfo->duration = $duration;
                $vendorInfo->status = Constants::vendorPendingStatus;
                $updateVendorExpiredDate = $this->vendorService->updateVendorExpiredDate($request->vendor_id, $vendorInfo);

                if(isset($updateVendorExpiredDate['error'])){
                    return responseMsgApi(__('vendor_subscription_plan_update__api_db_error',[],$request->language_symbol), Constants::internalServerErrorStatusCode);
                }
            }

            if(isset($transaction['error'])){
                $this->vendorService->destroy($request->vendor_id);
                return responseMsgApi(__('vendor_subscription_plan__api_db_error',[],$request->language_symbol), Constants::internalServerErrorStatusCode);
            }

            return responseMsgApi(__('vendor_subscription_plan__api_success_subscription',[],$request->language_symbol), Constants::createdStatusCode, Constants::success);
            // return new VendorSubscriptionTransactionWithKeyResource($packageDetail);
        }
    }

    public function upgradeSubscription($request)
    {
        $paypal_result = 0;
        $stripe_result = 0;
        $razor_result = 0;
        $paystack_result = 0;
        $in_app_purchase_result = 0;
        $offline_result = 0;

        $data = new \stdClass();
        $data->user_id = $request->user_id;
        $data->vendor_id = $request->vendor_id;
        $data->subscription_plan_id = $request->subscription_plan_id;
        $data->razor_id = $request->razor_id;
        $data->price = $request->price;
        $data->is_paystack = $request->is_paystack;
        $data->status = 0;
        $data->transaction_id = Carbon::now()->getTimestamp();

        if ($request->payment_method == 'paypal') {
            //User using Paypal to submit the transaction
            $payment_method = Constants::paypalPaymentMethod;
            $gateway = new \Braintree\Gateway([
                'environment' => trim($this->paymentSettingService->getPaymentInfo(null, null, Constants::paypalEnvironment)->value),
                'merchantId' => trim($this->paymentSettingService->getPaymentInfo(null, null, Constants::paypalMerchantId)->value),
                'publicKey' => trim($this->paymentSettingService->getPaymentInfo(null, null, Constants::paypalPublicKey)->value),
                'privateKey' => trim($this->paymentSettingService->getPaymentInfo(null, null, Constants::paypalPrivateKey)->value)
            ]);

            $result = $gateway->transaction()->sale([
                'amount'                => $request->price,
                'paymentMethodNonce' => $request->payment_method_nonce,
                'options' => [
                    'submitForSettlement' => True
                ]
            ]);

            if ($result->success == 1) {
                $data->payment_method = $request->payment_method;
                $paypal_result = $result->success;
            } else {
                return ['error'=>'vendor_subscription__upgrade_fail'];
            }
        } else if ($request->payment_method == 'stripe') {
            $payment_method = Constants::stripePaymentMethod;
            //User using Stripe to submit the transaction
            $payment_method_nonce = explode('_', $request->payment_method_nonce);
            if ($payment_method_nonce[0] == 'tok') {

                try {
                    # set stripe test key
                    \Stripe\Stripe::setApiKey(trim($this->paymentSettingService->getPaymentInfo(null, null, Constants::stripeSecretKey)->value));

                    $charge = \Stripe\Charge::create(array(
                        "amount"       => $request->price * 100, // amount in cents, so need to multiply with 100 .. $amount * 100
                        "currency"    => trim('USD'),
                        "source"      => $request->payment_method_nonce,
                        "description" => __('package__api_order_desc',[],$request->language_symbol)
                    ));

                    if ($charge->status == "succeeded") {
                        $data->payment_method = $request->payment_method;
                        $stripe_result = 1;
                    } else {
                        return ['error'=>'vendor_subscription__upgrade_fail'];
                    }
                } catch (\Throwable $e) {
                    return ['error'=>'vendor_subscription__upgrade_fail'];
                }
            } else if ($payment_method_nonce[0] == 'pm') {
                try {
                    \Stripe\Stripe::setApiKey(trim($this->paymentSettingService->getPaymentInfo(null, null, Constants::stripeSecretKey)->value));

                    $paymentIntent = \Stripe\PaymentIntent::create([
                        'payment_method' => $request->payment_method_nonce,
                        'amount' => $request->price * 100, // amount in cents, so need to multiply with 100 .. $amount * 100
                        'currency' => trim('USD'),
                        'confirmation_method' => 'manual',
                        'confirm' => true,
                    ]);

                    if ($paymentIntent->status == "succeeded") {
                        $data->payment_method = $request->payment_method;
                        $stripe_result = 1;
                    } else {
                        return ['error'=>'vendor_subscription__upgrade_fail'];
                    }
                } catch (\Throwable $e) {
                    return ['error'=>'vendor_subscription__upgrade_fail'];
                }
            }
        } else if ($request->payment_method == 'razor') {

            //User Using Razor
            $payment_method = Constants::razorPaymentMethod;
            $data->payment_method = $payment_method;
            $razor_result = 1;
        } else if ($request->payment_method == 'paystack') {

            //User Using Paystack
            $payment_method = Constants::paystackPaymentMethod;
            $data->payment_method = $payment_method;
            $paystack_result = 1;
        } else if ($request->payment_method == 'in app purchase') {

            //User Using COD
            $payment_method = Constants::iapPaymentMethod;
            $data->payment_method = $payment_method;
            $in_app_purchase_result = 1;
        }
        if ($offline_result == 1) {
            $data->payment_method = $payment_method;
            $data->status = 0;
        }

        // save package bought transaction
        $transaction = $this->store($data);

        if ($paypal_result == 1 || $stripe_result == 1  || $razor_result == 1 || $paystack_result == 1 || $in_app_purchase_result == 1) {

            $packageBoughtTransactionApiRelations = $this->packageBoughtTransactionApiRelations;
            $packageDetail = $this->getPackageBoughtTransaction($transaction->id,null,$packageBoughtTransactionApiRelations);

            $objs  = PaymentAttribute::whereIn("core_keys_id", [$packageDetail->package->core_keys_id])->get();

            $attributes = $objs->map(function ($key, $value){
                return [
                    $key['attribute_key'] => $key['attribute_value']
                ];
            })->collapse();
            $duration = intval($attributes['duration']);

            // update vendor expired date
            $expired_date = $packageDetail->added_date->addMonths($duration);
            $vendorInfo = new \stdClass();
            $vendorInfo->expired_date = $expired_date->toDateTimeString();
            $vendorInfo->duration = $duration;
            $vendorInfo->status = Constants::vendorAcceptStatus;
            $updateVendorExpiredDate = $this->vendorService->updateVendorExpiredDate($request->vendor_id, $vendorInfo);

            if(isset($updateVendorExpiredDate['error'])){
                return ['error'=>'vendor_subscription__upgrade_fail'];
            }
        }

        if(isset($transaction['error'])){
            return ['error'=>'vendor_subscription__upgrade_fail'];
        }

        return $transaction;
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            $transaction = new SubscriptionBoughtTransaction();

            if (isset($request->user_id) && !empty($request->user_id))
                $transaction->user_id = $request->user_id;

            if (isset($request->vendor_id) && !empty($request->vendor_id))
                $transaction->vendor_id = $request->vendor_id;

            if (isset($request->subscription_plan_id) && !empty($request->subscription_plan_id))
                $transaction->subscription_plan_id = $request->subscription_plan_id;

            if (isset($request->payment_method) && !empty($request->payment_method))
                $transaction->payment_method = $request->payment_method;

            if (isset($request->price) && !empty($request->price))
                $transaction->price = $request->price;

            if (isset($request->razor_id) && !empty($request->razor_id))
                $transaction->razor_id = $request->razor_id;

            if (isset($request->is_paystack) && !empty($request->is_paystack))
                $transaction->is_paystack = $request->is_paystack;

            if (isset($request->status))
                $transaction->status = $request->status;

            if (isset($request->transaction_id))
                $transaction->transaction_id = $request->transaction_id;

            if (isset($request->added_user_id) && !empty($request->added_user_id))
                $transaction->added_user_id = $request->added_user_id;
            else
                $transaction->added_user_id = Auth::user()->id;

            $transaction->save();

            DB::commit();

        } catch (\Throwable $e){
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }

       return $transaction;
    }

    public function getPackageBoughtTransaction($id = null, $conds = null, $relation=null){
        $transaction = SubscriptionBoughtTransaction::when($id, function ($q, $id) {
                            $q->where(SubscriptionBoughtTransaction::tableName.'.'.SubscriptionBoughtTransaction::id, $id);
                        })
                        ->leftJoin(PaymentInfo::tableName, SubscriptionBoughtTransaction::tableName.'.'.SubscriptionBoughtTransaction::subscriptionPlanId, '=', PaymentInfo::tableName.'.'.PaymentInfo::id)
                        ->leftjoin(User::tableName, SubscriptionBoughtTransaction::tableName.'.'.SubscriptionBoughtTransaction::userId, '=', User::tableName.'.'.User::id)
                        ->select(SubscriptionBoughtTransaction::tableName.'.*', User::tableName.'.'.User::name.' as user_name', PaymentInfo::tableName.'.'.PaymentInfo::value, PaymentInfo::tableName.'.'.PaymentInfo::paymentId, PaymentInfo::tableName.'.'.PaymentInfo::coreKeysId)
                        ->when($conds, function ($q, $conds) {
                            $q->where($conds);
                        })
                        ->when($relation, function ($q, $relation){
                            $q->with($relation);
                        })
                        ->first();
        return $transaction;
    }

    // for package report
    public function subscriptionReportIndex($request){

        $relation = ['user', 'package'];
        $conds['searchterm'] = $request->input('search') ?? '';
        $conds['package_id'] = $request->input('package_filter') == 'all'? null  : $request->package_filter;
        $conds['selected_date'] = $request->input('date_filter') == 'all'? null  : $request->date_filter;
        $conds['selected_payment_method'] = $request->input('selected_payment_method') == 'all'? null  : $request->selected_payment_method;

        $conds['order_by']= null;
        $conds['order_type'] = null;
        $row = $request->input('row') ?? Constants::dataTableDefaultRow;

        if($request->sort_field)
        {
            $conds['order_by'] = $request->sort_field;
            $conds['order_type'] = $request->sort_order;
        }

        $packageConds['payment_id'] = Constants::vendorSubscriptionPlanPaymentId;
        $packages = $this->paymentSettingService->getPaymentInfos(null,null,null,$packageConds,1);
        // dd($packages);

        $transactions = VendorSubscriptionTransactionWithKeyResource::collection($this->getPackageBoughtTransactions($relation,null,null,null,null,$row,$conds));
        $payment_methods_filters= [];
        $payment_methods = SubscriptionBoughtTransaction::groupBy('payment_method')->get();
        $payment_methods = SubscriptionBoughtTransaction::groupBy('payment_method')->pluck('payment_method');

        if($conds['order_by'])
        {
            $dataArr = [
                "transactions" => $transactions,
                'sort_field' =>$conds['order_by'],
                'sort_order'=>$request->sort_order,
                'search'=>$conds['searchterm'] ,
                'packages'=>$packages ,
                'selected_package'=>$conds['package_id'] ,
                'selected_payment_method'=>$conds['selected_payment_method'] ,
                'payment_methods'=>$payment_methods,
                'selectedDate'=> $conds['selected_date'],


            ];
        }
        else{
            $dataArr = [
                "transactions" => $transactions,
                'search'=>$conds['searchterm'],
                'packages'=>$packages,
                'selected_package'=>$conds['package_id'] ,
                'payment_methods'=>$payment_methods ,
                'selected_payment_method'=>$conds['selected_payment_method'],
                'selectedDate'=> $conds['selected_date'],
            ];

        }


        return $dataArr;
    }

    public function getPackageBoughtTransactions($relation = null, $status = null, $limit = null, $offset = null, $conds = null,$pagPerPage = null,$searchConds = null){

        $sort = '';
        if(isset($conds['order_by'])){
            $sort = $conds['order_by'];
        }
        $transactions = SubscriptionBoughtTransaction::leftJoin(PaymentInfo::tableName, SubscriptionBoughtTransaction::tableName.'.'.SubscriptionBoughtTransaction::subscriptionPlanId, '=', PaymentInfo::tableName.'.'.PaymentInfo::id)
                                // ->select('psx_package_bought_transactions.*', 'psx_payment_infos.value')
                                ->leftjoin(User::tableName, SubscriptionBoughtTransaction::tableName.'.'.SubscriptionBoughtTransaction::userId, '=', User::tableName.'.'.User::id)
                                ->select(SubscriptionBoughtTransaction::tableName.'.*', User::tableName.'.'.User::name.' as user_name', PaymentInfo::tableName.'.'.PaymentInfo::value, PaymentInfo::tableName.'.'.PaymentInfo::paymentId, PaymentInfo::tableName.'.'.PaymentInfo::coreKeysId)
                                ->when($relation, function ($q, $relation){
                                    $q->with($relation);
                                })
                                ->when($status, function ($q, $status){
                                    $q->where(SubscriptionBoughtTransaction::tableName.'.'.SubscriptionBoughtTransaction::status, $status);
                                })
                                ->when($limit, function ($query, $limit) {
                                    $query->limit($limit);
                                })
                                ->when($offset, function ($query, $offset) {
                                    $query->offset($offset);
                                })
                                ->when($conds, function ($query, $conds) {
                                    $query->where($conds);
                                })->when($searchConds, function ($query, $conds) {
                                    $query = $this->searching($query, $conds);
                                })
                                ->when(empty($sort), function ($query, $conds){
                                    $query->orderBy(SubscriptionBoughtTransaction::tableName.'.'.SubscriptionBoughtTransaction::id, 'desc');
                                });
                                if ($pagPerPage){
                                $transactions = $transactions->paginate($pagPerPage)->onEachSide(1)->withQueryString();

                                } else{
                                    $transactions = $transactions->get();
                                }

        return $transactions;
    }

    public function searching($query, $conds){
        // search term
        if (isset($conds['searchterm']) && $conds['searchterm']) {
            $search = $conds['searchterm'];
            $query->where(function ($query) use ($search) {
                $query->where('users.name','like','%'.$search.'%');
            });
        }
        if(isset($conds['package_id']) && $conds['package_id']){
            $package_filter=$conds['package_id'];
            $query->whereHas('package', function($q) use($package_filter){
                $q->where(SubscriptionBoughtTransaction::tableName.'.'.SubscriptionBoughtTransaction::subscriptionPlanId, $package_filter);
            });
        }
        if(isset($conds['selected_date']) && $conds['selected_date']){
            $added_date_filter = $conds['selected_date'];
            if($added_date_filter[1] == ''){
                $added_date_filter[1] = Carbon::now();
            }
            $query->whereBetween(SubscriptionBoughtTransaction::tableName.'.'.SubscriptionBoughtTransaction::addedDate, $added_date_filter);

//            $date_filter=$conds['selected_date'];
//            $new_date=date('Y-m-d', strtotime($date_filter));
//            $query->whereDate($this->pkgTableName.'.'.$this->pkgAddedDateCol, '=', $new_date);
        }
        if(isset($conds['selected_payment_method']) && $conds['selected_payment_method']){
            $payment_method=$conds['selected_payment_method'];
            $query->where(SubscriptionBoughtTransaction::tableName.'.'.SubscriptionBoughtTransaction::paymentMethod, '=', $payment_method);
        }

        if(isset($conds['added_date_range']) && $conds['added_date_range']){
            $added_date_filter = $conds['added_date_range'];
            if($added_date_filter[1] == ''){
                $added_date_filter[1] = Carbon::now();
            }
            $query->whereBetween(SubscriptionBoughtTransaction::tableName.'.'.SubscriptionBoughtTransaction::addedDate, $added_date_filter);
        }

        if(isset($conds['status'])){

            $query->where(SubscriptionBoughtTransaction::tableName.'.'.SubscriptionBoughtTransaction::status, $conds['status']);
        }

        // order by
        if (isset($conds['order_by']) && isset($conds['order_type']) && $conds['order_by'] && $conds['order_type']){

            if($conds['order_by'] == 'add_user_id' || $conds['order_by'] == 'updated_user_id'){
                $query->orderBy('owner', $conds['order_type']);
            }
            else if($conds['order_by'] == 'package_id'){

                $query->orderBy('value', $conds['order_type']);
            }
            else if($conds['order_by'] == 'added_date' || $conds['order_by'] == 'expired_date'){

                $query->orderBy(SubscriptionBoughtTransaction::tableName.'.'.SubscriptionBoughtTransaction::addedDate, $conds['order_type']);
            }
            else if($conds['order_by'] == 'post_count'){


            }
            else{

                $query->orderBy($conds['order_by'], $conds['order_type']);
            }

        }
        return $query;
    }

}
