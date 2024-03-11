<?php

namespace Modules\Payment\Http\Services;

use App\Config\ps_constant;
use App\Http\Services\PsService;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\CoreKey;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\CustomizeUi;
use Modules\Payment\Entities\PaymentInfo;
use Modules\Core\Http\Services\CoreKeyService;
use Modules\Payment\Entities\PaymentAttribute;
use Modules\Core\Entities\CoreFieldFilterSetting;
use Modules\Core\Entities\ScreenDisplayUiSetting;
use Modules\Payment\Entities\CoreKeyPaymentRelation;
use Modules\Core\Http\Services\AvailableCurrencyService;
use Modules\Core\Http\Services\UserAccessApiTokenService;
use Modules\Payment\Transformers\Api\App\V1_0\Payment\PackageWithPurchasedCountApiResource;
use Modules\Payment\Transformers\Api\App\V1_0\Payment\PackageInAppPurchaseSettingApiResource;
use Modules\Payment\Transformers\Backend\NoModel\PackageInAppPurchase\PackageInAppPurchaseWithKeyResource;

class PackageInAppPurchaseSettingService extends PsService
{
    protected $customUiIsDelCol, $customUiEnableCol, $pmtAttrPackageIapPriceCol, $iapTypeAndroid, $iapTypeIOS, $dangerFlag, $successFlag, $noContentStatusCode, $successStatus, $packageInAppPurchasePaymentId, $paymentInfoIdCol, $paymentInfoCoreKeysIdCol, $paymentInfoPaymentIdCol, $paymentInfoValueCol, $publish, $unPublish, $coreKeyPaymentRelationService, $paymentService, $inAppPurchaseSetting, $coreKeyService, $code, $paymentSettingService, $inAppPurchaseApiRelations, $pmtAttrPackageIapTypeCol, $pmtAttrPackageIapCountCol, $paymentAttributeService, $packageInAppPurchaseApiRelations, $availableCurrencyService, $pmtAttrPackageIapStatusCol, $pmtAttrPackageIapCurrencyCol,  $deviceTokenParaApi, $loginUserIdParaApi, $userAccessApiTokenService, $forbiddenStatusCode, $hide, $show, $coreFieldFilterModuleNameCol, $coreFieldFilterIdCol, $screenDisplayUiKeyCol, $screenDisplayUiIdCol, $screenDisplayUiIsShowCol;

    public function __construct(UserAccessApiTokenService $userAccessApiTokenService, CoreKeyPaymentRelationService $coreKeyPaymentRelationService, PaymentService $paymentService, CoreKeyService $coreKeyService, PaymentSettingService $paymentSettingService, PaymentAttributeService $paymentAttributeService, AvailableCurrencyService $availableCurrencyService)
    {
        $this->paymentInfoIdCol = PaymentInfo::id;
        $this->paymentInfoCoreKeysIdCol = PaymentInfo::coreKeysId;
        $this->paymentInfoPaymentIdCol = PaymentInfo::paymentId;
        $this->paymentInfoValueCol = PaymentInfo::value;

        $this->customUiEnableCol = CustomizeUi::enable;
        $this->customUiIsDelCol = CustomizeUi::isDelete;

        $this->coreKeyCoreKeysIdCol = CoreKey::coreKeysId;

        $this->coreKeyPaymentRelationCoreKeysIdCol = CoreKeyPaymentRelation::coreKeysId;

        $this->paymentAttributeCoreKeysIdCol = PaymentAttribute::coreKeysId;

        $this->publish = Constants::publish;
        $this->unPublish = Constants::unPublish;
        $this->packageInAppPurchasePaymentId = Constants::packageInAppPurchasePaymentId;
        $this->packageInAppPurchasePaymentId = Constants::packageInAppPurchasePaymentId;
        $this->code = Constants::payment;
        $this->pmtAttrPackageIapTypeCol = Constants::pmtAttrPackageIapTypeCol;
        $this->pmtAttrPackageIapCountCol = Constants::pmtAttrPackageIapCountCol;
        $this->pmtAttrPackageIapPriceCol = Constants::pmtAttrPackageIapPriceCol;
        $this->pmtAttrPackageIapStatusCol = Constants::pmtAttrPackageIapStatusCol;
        $this->pmtAttrPackageIapCurrencyCol = Constants::pmtAttrPackageIapCurrencyCol;

        $this->coreKeyPaymentRelationService = $coreKeyPaymentRelationService;
        $this->paymentService = $paymentService;
        $this->coreKeyService = $coreKeyService;
        $this->paymentSettingService = $paymentSettingService;
        $this->paymentAttributeService = $paymentAttributeService;
        $this->availableCurrencyService = $availableCurrencyService;

        $this->inAppPurchaseApiRelations = ['payment', 'core_key', 'payment_attributes'];

        $this->packageInAppPurchaseApiRelations = ['payment', 'core_key', 'payment_info'];

        $this->noContentStatusCode = Constants::noContentStatusCode;
        $this->successStatus = Constants::successStatus;
        $this->forbiddenStatusCode = Constants::forbiddenStatusCode;

        $this->userAccessApiTokenService = $userAccessApiTokenService;
        $this->loginUserIdParaApi = ps_constant::loginUserIdParaFromApi;
        $this->deviceTokenParaApi = ps_constant::deviceTokenKeyFromApi;

        $this->show = Constants::show;
        $this->hide = Constants::hide;
        $this->enable = Constants::enable;
        $this->disable = Constants::disable;
        $this->delete = Constants::delete;
        $this->unDelete = Constants::unDelete;
        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;

        $this->dropDownUi = Constants::dropDownUi;
        $this->textUi = Constants::textUi;
        $this->radioUi = Constants::radioUi;
        $this->checkBoxUi = Constants::checkBoxUi;
        $this->dateTimeUi = Constants::dateTimeUi;
        $this->textAreaUi = Constants::textAreaUi;
        $this->numberUi = Constants::numberUi;
        $this->multiSelectUi = Constants::multiSelectUi;
        $this->imageUi = Constants::imageUi;
        $this->timeOnlyUi = Constants::timeOnlyUi;
        $this->dateOnlyUi = Constants::dateOnlyUi;

        $this->coreFieldFilterModuleNameCol = CoreFieldFilterSetting::moduleName;
        $this->coreFieldFilterIdCol = CoreFieldFilterSetting::id;

        $this->screenDisplayUiKeyCol = ScreenDisplayUiSetting::key;
        $this->screenDisplayUiIdCol = ScreenDisplayUiSetting::id;
        $this->screenDisplayUiIsShowCol = ScreenDisplayUiSetting::isShow;

        $this->iapTypeAndroid = Constants::iapTypeAndroid;
        $this->iapTypeIOS = Constants::iapTypeIOS;
    }

    public function index($request)
    {

        $code = $this->code;

        // Search and filter
        $conds['searchterm'] = $request->input('search') ?? '';
        $conds['type'] = $request->input('type_filter') == 'all'? null  : $request->type_filter;
        $conds['currency_id'] = $request->input('currency_filter') == 'all'? null  : $request->currency_filter;

        $conds['order_by']= null;
        $conds['order_type'] = null;
        $row = $request->input('row') ?? Constants::dataTableDefaultRow;

        if($request->sort_field)
        {
            $conds['order_by'] = $request->sort_field;
            $conds['order_type'] = $request->sort_order;
        }

        $conds['payment_id'] = $this->packageInAppPurchasePaymentId;
        $relations = ['core_key'];
        $attributes = [
            $this->pmtAttrPackageIapTypeCol,
            $this->pmtAttrPackageIapCountCol,
            $this->pmtAttrPackageIapPriceCol,
            $this->pmtAttrPackageIapStatusCol,
            $this->pmtAttrPackageIapCurrencyCol
        ];
        $inAppPurchases = PackageInAppPurchaseWithKeyResource::collection($this->paymentSettingService->getPaymentInfos($relations, null, null, $conds, false, $row, $attributes));
        $currencies = $this->availableCurrencyService->getAvailableCurrencies($this->publish);

        $types = [
            [
                'id' => $this->iapTypeAndroid,
                'name' => $this->iapTypeAndroid,
            ],
            [
                'id' => $this->iapTypeIOS,
                'name' => $this->iapTypeIOS,
            ],
        ];

        // taking for column and columnFilterOption
        $columnAndColumnFilter = $this->takingForColumnAndFilterOption();
        $columnProps = $columnAndColumnFilter['arrForColumnProps'];
        $columnFilterOptionProps = $columnAndColumnFilter['arrForColumnFilterProps'];

        if($conds['order_by'])
        {
            $dataArr = [
                'showCoreAndCustomFieldArr' => $columnProps,
                'hideShowFieldForFilterArr' => $columnFilterOptionProps,
                "inAppPurchases" => $inAppPurchases,
                "countKey" => $this->pmtAttrPackageIapCountCol,
                "typeKey" => $this->pmtAttrPackageIapTypeCol,
                "priceKey" => $this->pmtAttrPackageIapPriceCol,
                "statusKey" => $this->pmtAttrPackageIapStatusCol,
                "currencyKey" => $this->pmtAttrPackageIapCurrencyCol,
                "currencies" => $currencies,
                'sort_field' =>$conds['order_by'],
                'sort_order'=>$request->sort_order,
                'search'=>$conds['searchterm'] ,
                'types' => $types,
                'selected_type'=>$conds['type'] ,
                'selected_currency'=>$conds['currency_id'] ,
            ];
        }
        else
        {
            $dataArr = [
                'showCoreAndCustomFieldArr' => $columnProps,
                'hideShowFieldForFilterArr' => $columnFilterOptionProps,
                "inAppPurchases" => $inAppPurchases,
                "countKey" => $this->pmtAttrPackageIapCountCol,
                "typeKey" => $this->pmtAttrPackageIapTypeCol,
                "priceKey" => $this->pmtAttrPackageIapPriceCol,
                "statusKey" => $this->pmtAttrPackageIapStatusCol,
                "currencyKey" => $this->pmtAttrPackageIapCurrencyCol,
                "currencies" => $currencies,
                'search'=>$conds['searchterm'] ,
                'types' => $types,
                'selected_type'=>$conds['type'] ,
                'selected_currency'=>$conds['currency_id'] ,
            ];
        }
        return $dataArr;
    }

    public function store($request)
    {
        DB::beginTransaction();

        try {
            // save core key table
            $coreKey = new \stdClass();
            $coreKey->name = $request->in_app_purchase_prd_id;
            $coreKey->description = $request->in_app_purchase_prd_id;
            $core_key = $this->coreKeyService->store($coreKey, $this->code);

            // save core key payment relations table
            $coreKeyPaymentRelation = new \stdClass();
            $coreKeyPaymentRelation->core_keys_id = $core_key->core_keys_id;
            $coreKeyPaymentRelation->payment_id = $this->packageInAppPurchasePaymentId;
            $this->coreKeyPaymentRelationService->store($coreKeyPaymentRelation);

            // save payment info table
            $paymentInfo = new PaymentInfo();
            $paymentInfo->core_keys_id = $core_key->core_keys_id;
            $paymentInfo->payment_id = $this->packageInAppPurchasePaymentId;
            if (isset($request->description) && !empty($request->description))
                $paymentInfo->value = $request->description;
            if (isset($request->added_user_id) && !empty($request->added_user_id))
                $paymentInfo->added_user_id = $request->added_user_id;
            else
                $paymentInfo->added_user_id = Auth::user()->id;
            $paymentInfo->save();

            // save payment attributes table For Type Col
            $paymentAttributeType = new PaymentAttribute();
            $paymentAttributeType->payment_id = $this->packageInAppPurchasePaymentId;
            $paymentAttributeType->core_keys_id = $core_key->core_keys_id;
            $paymentAttributeType->attribute_key = $this->pmtAttrPackageIapTypeCol;
            $paymentAttributeType->attribute_value = $request->type;
            $this->paymentAttributeService->store($paymentAttributeType);

            // save payment attributes table For Post Count Col
            $paymentAttributeCount = new PaymentAttribute();
            $paymentAttributeCount->payment_id = $this->packageInAppPurchasePaymentId;
            $paymentAttributeCount->core_keys_id = $core_key->core_keys_id;
            $paymentAttributeCount->attribute_key = $this->pmtAttrPackageIapCountCol;
            $paymentAttributeCount->attribute_value = $request->count;
            $this->paymentAttributeService->store($paymentAttributeCount);

            // save payment attributes table For Price Col
            $paymentAttributePrice = new PaymentAttribute();
            $paymentAttributePrice->payment_id = $this->packageInAppPurchasePaymentId;
            $paymentAttributePrice->core_keys_id = $core_key->core_keys_id;
            $paymentAttributePrice->attribute_key = $this->pmtAttrPackageIapPriceCol;
            $paymentAttributePrice->attribute_value = $request->price;
            $this->paymentAttributeService->store($paymentAttributePrice);

            // save payment attributes table For Status Col
            $paymentAttributeStatus = new PaymentAttribute();
            $paymentAttributeStatus->payment_id = $this->packageInAppPurchasePaymentId;
            $paymentAttributeStatus->core_keys_id = $core_key->core_keys_id;
            $paymentAttributeStatus->attribute_key = $this->pmtAttrPackageIapStatusCol;
            $paymentAttributeStatus->attribute_value = $request->status == false?'0':'1';
            $this->paymentAttributeService->store($paymentAttributeStatus);

            // save payment attributes table For Currency Col
            $paymentAttributeCurrency = new PaymentAttribute();
            $paymentAttributeCurrency->payment_id = $this->packageInAppPurchasePaymentId;
            $paymentAttributeCurrency->core_keys_id = $core_key->core_keys_id;
            $paymentAttributeCurrency->attribute_key = $this->pmtAttrPackageIapCurrencyCol;
            $paymentAttributeCurrency->attribute_value = $request->currency_id;
            $this->paymentAttributeService->store($paymentAttributeCurrency);

            DB::commit();
            return $paymentInfo;
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public function update($id, $request)
    {
    //    dd($request->all());
        DB::beginTransaction();

        try {
            // update core key table
            $coreKey = new \stdClass();
            $coreKey->name = $request->in_app_purchase_prd_id;
            $coreKey->description = $request->in_app_purchase_prd_id;
            $core_key_id= CoreKey::where('core_keys_id',$request->core_keys_id)->first()->id;
            $this->coreKeyService->update($request->core_keys_id, $coreKey);

            // update payment info table
            $paymentInfo = $this->getPaymentInfo($id);
            $paymentInfo->core_keys_id = $request->core_keys_id;
            $paymentInfo->payment_id = $this->packageInAppPurchasePaymentId;
            if (isset($request->description) && !empty($request->description))
                $paymentInfo->value = $request->description;
            if (isset($request->updated_user_id) && !empty($request->updated_user_id))
                $paymentInfo->updated_user_id = $request->updated_user_id;
            else
                $paymentInfo->updated_user_id = Auth::user()->id;
            $paymentInfo->update();

            // update payment attributes table For Type Col
            $conds['attribute_key'] = $this->pmtAttrPackageIapTypeCol;
            $conds['core_keys_id'] = $request->core_keys_id;
            $paymentAttributeType = $this->paymentAttributeService->getPaymentAttribute(null, $conds);
            if($paymentAttributeType){
                $paymentAttributeType->payment_id = $this->packageInAppPurchasePaymentId;
                $paymentAttributeType->core_keys_id = $request->core_keys_id;
                $paymentAttributeType->attribute_key = $this->pmtAttrPackageIapTypeCol;
                $paymentAttributeType->attribute_value = $request->type;
                $this->paymentAttributeService->update($paymentAttributeType);
            }else{
                $paymentAttributeType = new PaymentAttribute();
                $paymentAttributeType->payment_id = $this->packageInAppPurchasePaymentId;
                $paymentAttributeType->core_keys_id = $request->core_keys_id;
                $paymentAttributeType->attribute_key = $this->pmtAttrPackageIapTypeCol;
                $paymentAttributeType->attribute_value = $request->type;
                $this->paymentAttributeService->store($paymentAttributeType);
            }

            // update payment attributes table For Post Count Col
            $conds['attribute_key'] = $this->pmtAttrPackageIapCountCol;
            $conds['core_keys_id'] = $request->core_keys_id;
            $paymentAttributeCount = $this->paymentAttributeService->getPaymentAttribute(null, $conds);
            if($paymentAttributeCount){
                $paymentAttributeCount->payment_id = $this->packageInAppPurchasePaymentId;
                $paymentAttributeCount->core_keys_id = $request->core_keys_id;
                $paymentAttributeCount->attribute_key = $this->pmtAttrPackageIapCountCol;
                $paymentAttributeCount->attribute_value = $request->count;
                $this->paymentAttributeService->update($paymentAttributeCount);
            }else{
                $paymentAttributeCount = new PaymentAttribute();
                $paymentAttributeCount->payment_id = $this->packageInAppPurchasePaymentId;
                $paymentAttributeCount->core_keys_id = $request->core_keys_id;
                $paymentAttributeCount->attribute_key = $this->pmtAttrPackageIapCountCol;
                $paymentAttributeCount->attribute_value = $request->count;
                $this->paymentAttributeService->store($paymentAttributeCount);
            }

            // update payment attributes table For Price Col
            $conds['attribute_key'] = $this->pmtAttrPackageIapPriceCol;
            $conds['core_keys_id'] = $request->core_keys_id;
            $paymentAttributePrice = $this->paymentAttributeService->getPaymentAttribute(null, $conds);
            if($paymentAttributePrice){
                $paymentAttributePrice->payment_id = $this->packageInAppPurchasePaymentId;
                $paymentAttributePrice->core_keys_id = $request->core_keys_id;
                $paymentAttributePrice->attribute_key = $this->pmtAttrPackageIapPriceCol;
                $paymentAttributePrice->attribute_value = $request->price;
                $this->paymentAttributeService->update($paymentAttributePrice);
            }else{
                $paymentAttributePrice = new PaymentAttribute();
                $paymentAttributePrice->payment_id = $this->packageInAppPurchasePaymentId;
                $paymentAttributePrice->core_keys_id = $request->core_keys_id;
                $paymentAttributePrice->attribute_key = $this->pmtAttrPackageIapPriceCol;
                $paymentAttributePrice->attribute_value = $request->price;
                $this->paymentAttributeService->store($paymentAttributePrice);
            }

            // update payment attributes table For Status Col
            $conds['attribute_key'] = $this->pmtAttrPackageIapStatusCol;
            $conds['core_keys_id'] = $request->core_keys_id;
            $paymentAttributeStatus = $this->paymentAttributeService->getPaymentAttribute(null, $conds);
            if($paymentAttributeStatus){
                $paymentAttributeStatus->payment_id = $this->packageInAppPurchasePaymentId;
                $paymentAttributeStatus->core_keys_id = $request->core_keys_id;
                $paymentAttributeStatus->attribute_key = $this->pmtAttrPackageIapStatusCol;
                $paymentAttributeStatus->attribute_value = $request->status==false?'0':'1';
                $this->paymentAttributeService->update($paymentAttributeStatus);
            }else{
                $paymentAttributeStatus = new PaymentAttribute();
                $paymentAttributeStatus->payment_id = $this->packageInAppPurchasePaymentId;
                $paymentAttributeStatus->core_keys_id = $request->core_keys_id;
                $paymentAttributeStatus->attribute_key = $this->pmtAttrPackageIapStatusCol;
                $paymentAttributeStatus->attribute_value = $request->status==false?'0':'1';
                $this->paymentAttributeService->store($paymentAttributeStatus);
            }

            // update payment attributes table For Currency Col
            $conds['attribute_key'] = $this->pmtAttrPackageIapCurrencyCol;
            $conds['core_keys_id'] = $request->core_keys_id;
            $paymentAttributeCurrency = $this->paymentAttributeService->getPaymentAttribute(null, $conds);
            if($paymentAttributeCurrency){
                $paymentAttributeCurrency->payment_id = $this->packageInAppPurchasePaymentId;
                $paymentAttributeCurrency->core_keys_id = $request->core_keys_id;
                $paymentAttributeCurrency->attribute_key = $this->pmtAttrPackageIapCurrencyCol;
                $paymentAttributeCurrency->attribute_value = $request->currency_id;
                $this->paymentAttributeService->update($paymentAttributeCurrency);
            }else{
                $paymentAttributeCurrency = new PaymentAttribute();
                $paymentAttributeCurrency->payment_id = $this->packageInAppPurchasePaymentId;
                $paymentAttributeCurrency->core_keys_id = $request->core_keys_id;
                $paymentAttributeCurrency->attribute_key = $this->pmtAttrPackageIapCurrencyCol;
                $paymentAttributeCurrency->attribute_value = $request->currency_id;
                $this->paymentAttributeService->store($paymentAttributeCurrency);
            }

            DB::commit();
            return $paymentInfo;
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public function getPaymentInfos($relations = null, $limit = null, $offset = null, $conds = null)
    {
        $paymentInfos = PaymentInfo::when($relations, function ($query, $relations) {
            $query->with($relations);
        })
            ->when($conds, function ($query, $conds) {
                $query->where($conds);
            })
            ->when($limit, function ($query, $limit) {
                $query->limit($limit);
            })
            ->when($offset, function ($query, $offset) {
                $query->offset($offset);
            })
            ->latest()->get();
        return $paymentInfos;
    }

    public function getPaymentInfo($id, $relations = null, $conds = null)
    {
        $paymentInfo = PaymentInfo::where($this->paymentInfoIdCol, $id)
            ->when($relations, function ($query, $relations) {
                $query->with($relations);
            })
            ->when($conds, function ($query, $conds) {
                $query->where($conds);
            })
            ->first();
        return $paymentInfo;
    }

    public function create(){
        $availableCurrencies = $this->availableCurrencyService->getAvailableCurrencies($this->publish);
        $dataArr = [
            "availableCurrencies" => $availableCurrencies,
        ];
        return $dataArr;
    }

    public function edit($id)
    {
        $relations = ['core_key', 'payment_attributes'];
        $inAppPurchase = $this->getPaymentInfo($id, $relations);
        $count_attribute = '';
        $price_attribute = '';
        $type_attribute = '';
        $status_attribute = 0;
        $currency_attribute = '';

        foreach ($inAppPurchase['payment_attributes'] as $attribute) {
            if ($attribute['attribute_key'] == $this->pmtAttrPackageIapCountCol) {
                $count_attribute = $attribute['attribute_value'];
            }
            if ($attribute['attribute_key'] == $this->pmtAttrPackageIapPriceCol) {
                $price_attribute = $attribute['attribute_value'];
            }
            if ($attribute['attribute_key'] == $this->pmtAttrPackageIapTypeCol) {
                $type_attribute = $attribute['attribute_value'];
            }
            if ($attribute['attribute_key'] == $this->pmtAttrPackageIapStatusCol) {
                $status_attribute = $attribute['attribute_value'];
            }
            if ($attribute['attribute_key'] == $this->pmtAttrPackageIapCurrencyCol) {
                $currency_attribute = $attribute['attribute_value'];
            }
        }

        $availableCurrencies = $this->availableCurrencyService->getAvailableCurrencies($this->publish);

        $dataArr = [
            "inAppPurchase" => $inAppPurchase,
            "count_attribute" => $count_attribute,
            "price_attribute" => $price_attribute,
            "type_attribute" => $type_attribute,
            "status_attribute" => $status_attribute,
            "currency_attribute" => $currency_attribute,
            "availableCurrencies" => $availableCurrencies,
        ];
        return $dataArr;
    }

    public function destroy($id)
    {
        $paymentInfo = PaymentInfo::find($id);
        $coreKey = CoreKey::where($this->coreKeyCoreKeysIdCol, $paymentInfo->core_keys_id)->first();
        $coreKeyPaymentRelation = CoreKeyPaymentRelation::where($this->coreKeyPaymentRelationCoreKeysIdCol, $paymentInfo->core_keys_id)->first();
        $paymentAttributes = PaymentAttribute::where($this->paymentAttributeCoreKeysIdCol, $paymentInfo->core_keys_id)->get();
        $name = $coreKey->name;

        $paymentInfo->delete();
        $coreKey->delete();
        $coreKeyPaymentRelation->delete();

//        foreach ($paymentAttributes as $attribute) {
//            $attribute->delete();
//        }
        PaymentAttribute::destroy($paymentAttributes->pluck('id'));

        $dataArr = [
            'msg' => __('core__be_delete_success', ['attribute' => $name]),
            'flag' => $this->dangerFlag,
        ];

        return $dataArr;
    }

    public function takingForColumnAndFilterOption()
    {

        // for table
        $hideShowCoreFieldForColumnArr = [];
        $hideShowCustomFieldForColumnArr = [];

        $showUserCols = [];

        // for eye
        $hideShowFieldForColumnFilterArr = [];

        // for control
        $controlFieldArr = [];
        $controlFieldObj = $this->takingForColumnProps(__('core__be_action_label'), "action", "Action", false, 0);
        array_push($controlFieldArr, $controlFieldObj);


        $code = $this->code;
        $hiddenForCoreAndCustomField = $this->hiddenShownForCoreAndCustomField($this->hide, $code);
        $shownForCoreAndCustomField = $this->hiddenShownForCoreAndCustomField($this->show, $code);
        $hideShowForCoreAndCustomFields = $shownForCoreAndCustomField->merge($hiddenForCoreAndCustomField);

        foreach ($hideShowForCoreAndCustomFields as $showFields) {
            if ($showFields->coreField !== null) {

                $label = $showFields->coreField->label_name;
                $field = $showFields->coreField->field_name;
                $colName = $showFields->coreField->field_name;
                $type = $showFields->coreField->data_type;
                $isShowSorting = $showFields->coreField->is_show_sorting;
                $ordering = $showFields->coreField->ordering;

                $cols = $colName;
                $id = $showFields->id;
                $hidden = $showFields->is_show ? false : true;
                $moduleName = $showFields->coreField->module_name;
                $keyId = $showFields->coreField->id;

                $coreFieldObjForColumnsProps = $this->takingForColumnProps($label, $field, $type,$isShowSorting, $ordering);
                $coreFieldObjForColumnFilter = $this->takingForColumnFilterProps($id, $label, $field, $hidden, $moduleName, $keyId);

                array_push($hideShowFieldForColumnFilterArr, $coreFieldObjForColumnFilter);
                array_push($hideShowCoreFieldForColumnArr, $coreFieldObjForColumnsProps);
                array_push($showUserCols, $cols);
            }
            if ($showFields->customizeField !== null) {

                $label = $showFields->customizeField->name;
                $uiHaveAttribute = [$this->dropDownUi, $this->radioUi];
                if (in_array($showFields->customizeField->ui_type_id, $uiHaveAttribute)) {
                    $field = $showFields->customizeField->core_keys_id . "@@name";
                } else {
                    $field = $showFields->customizeField->core_keys_id;
                }
                $type = $showFields->customizeField->data_type;
                $isShowSorting = $showFields->customizeField->is_show_sorting;
                $ordering = $showFields->customizeField->ordering;

                $id = $showFields->id;
                $hidden = $showFields->is_show ? false : true;
                $moduleName = $showFields->customizeField->module_name;
                $keyId = $showFields->customizeField->core_keys_id;

                $customFieldObjForColumnsProps = $this->takingForColumnProps($label, $field, $type ,$isShowSorting, $ordering);
                $customFieldObjForColumnFilter = $this->takingForColumnFilterProps($id, $label, $field, $hidden, $moduleName, $keyId);

                array_push($hideShowFieldForColumnFilterArr, $customFieldObjForColumnFilter);
                array_push($hideShowCustomFieldForColumnArr, $customFieldObjForColumnsProps);
            }
        }

        // for columns props
        $showCoreAndCustomFieldArr = array_merge($hideShowCoreFieldForColumnArr, $controlFieldArr, $hideShowCustomFieldForColumnArr);
        $sortedColumnArr = columnOrdering("ordering", $showCoreAndCustomFieldArr);

        // for eye
        $hideShowCoreAndCustomFieldArr = array_merge($hideShowFieldForColumnFilterArr);

        $dataArr = [
            "arrForColumnProps" => $sortedColumnArr,
            "arrForColumnFilterProps" => $hideShowCoreAndCustomFieldArr,
            "showCoreField" => $showUserCols,
        ];
        return $dataArr;
    }

    public function hiddenShownForCoreAndCustomField($isShown, $code)
    {
        $hiddenShownForFields = ScreenDisplayUiSetting::with(['customizeField' => function ($q) {
            $q->where($this->customUiEnableCol, $this->enable)->where($this->customUiIsDelCol, $this->unDelete);
        }, 'coreField'=> function($q){
            $q->where($this->coreFieldFilterModuleNameCol, $this->code);
        }])
        ->where($this->coreFieldFilterModuleNameCol, $code)
            ->where($this->screenDisplayUiIsShowCol, $isShown)
            ->get();
        return $hiddenShownForFields;
    }

    public function takingForColumnProps($label, $field, $type, $isShowSorting, $ordering)
    {
        $hideShowCoreAndCustomFieldObj = new \stdClass();
        $hideShowCoreAndCustomFieldObj->label = $label;
        $hideShowCoreAndCustomFieldObj->field = $field;
        $hideShowCoreAndCustomFieldObj->type = $type;
        $hideShowCoreAndCustomFieldObj->sort = $isShowSorting;
        $hideShowCoreAndCustomFieldObj->ordering = $ordering;

        if ($type !== "Image" && $type !== "MultiSelect" && $type !== "Action") {
            $hideShowCoreAndCustomFieldObj->action = 'Action';
        }

        return $hideShowCoreAndCustomFieldObj;
    }

    public function takingForColumnFilterProps($id, $label, $field, $hidden, $moduleName, $keyId)
    {
        $hideShowCoreAndCustomFieldObj = new \stdClass();
        $hideShowCoreAndCustomFieldObj->id = $id;
        $hideShowCoreAndCustomFieldObj->label = $label;
        $hideShowCoreAndCustomFieldObj->key = $field;
        $hideShowCoreAndCustomFieldObj->hidden = $hidden;
        $hideShowCoreAndCustomFieldObj->module_name = $moduleName;
        $hideShowCoreAndCustomFieldObj->key_id = $keyId;

        return $hideShowCoreAndCustomFieldObj;
    }

    // From api
    public function indexFromApi($request)
    {
        /// check permission start
        $deviceToken = $request->header($this->deviceTokenParaApi);
        $userId = $request->login_user_id;

        // user token layer permission start
        $userAccessApiToken = $this->userAccessApiTokenService->getUserAccessApiToken($userId, $deviceToken);
        // user token layer permission end

        /// check permission end

        if (empty($userAccessApiToken)){
            $msg = __('payment__api_no_permission',[],$request->language_symbol);
            return responseMsgApi($msg, $this->forbiddenStatusCode);
        }else{
            $offset = $request->offset;
            $limit = $request->limit;

            $packageInAppPurchaseApiRelations = $this->packageInAppPurchaseApiRelations;
            $conds['payment_id'] = $this->packageInAppPurchasePaymentId;
            $conds['status'] = 1;
            $attributes = [
                $this->pmtAttrPackageIapTypeCol,
                $this->pmtAttrPackageIapCountCol,
                $this->pmtAttrPackageIapPriceCol,
                $this->pmtAttrPackageIapStatusCol,
                $this->pmtAttrPackageIapCurrencyCol
            ];
            $packageIapPayments = PackageInAppPurchaseSettingApiResource::collection($this->paymentSettingService->getPaymentInfos($packageInAppPurchaseApiRelations, $limit, $offset, $conds, true, null, $attributes));

            if ($offset > 0 && $packageIapPayments->isEmpty()) {
                // no paginate data
                $data = [];
                return responseDataApi($data);

            } else if($packageIapPayments->isEmpty()) {
                // no data db
                return responseMsgApi(__('payment__api_no_data'), $this->noContentStatusCode, $this->successStatus);
            } else {
                return responseDataApi($packageIapPayments);
            }
        }
    }

    public function packagePurchasedCountFromApi($request)
    {
        /// check permission start
        $deviceToken = $request->header($this->deviceTokenParaApi);
        $userId = $request->login_user_id;

        // user token layer permission start
        $userAccessApiToken = $this->userAccessApiTokenService->getUserAccessApiToken($userId, $deviceToken);
        // user token layer permission end

        /// check permission end

        if (empty($userAccessApiToken)){
            $msg = __('payment__api_no_permission',[],$request->language_symbol);
            return responseMsgApi($msg, $this->forbiddenStatusCode);
        }else{
            $offset = $request->offset;
            $limit = $request->limit;

            $packageInAppPurchaseApiRelations = ['payment', 'purchased_count', 'payment_info'];
            $conds['payment_id'] = $this->packageInAppPurchasePaymentId;
            $conds['status'] = 1;
            $attributes = [
                $this->pmtAttrPackageIapTypeCol,
                $this->pmtAttrPackageIapCountCol,
                $this->pmtAttrPackageIapPriceCol,
                $this->pmtAttrPackageIapStatusCol,
                $this->pmtAttrPackageIapCurrencyCol
            ];

            $packageIapPayments = PackageWithPurchasedCountApiResource::collection($this->paymentSettingService->getPaymentInfos($packageInAppPurchaseApiRelations, $limit, $offset, $conds, true, null, $attributes)->sortByDesc('purchased_count'));

            if ($offset > 0 && $packageIapPayments->isEmpty()) {
                // no paginate data
                $data = [];
                return responseDataApi($data);

            } else if($packageIapPayments->isEmpty()) {
                // no data db
                return responseMsgApi(__('payment__api_no_data'), $this->noContentStatusCode, $this->successStatus);
            } else {
                return responseDataApi($packageIapPayments);
            }
        }
    }


    public function makePublishOrUnpublish($id){
        // update payment attributes table For Status Col
        $conds['attribute_key'] = $this->pmtAttrPackageIapStatusCol;
        $conds['core_keys_id'] = $id;
        $paymentAttributeStatus = $this->paymentAttributeService->getPaymentAttribute(null, $conds);
        if($paymentAttributeStatus){
            $paymentAttributeStatus->payment_id = $this->packageInAppPurchasePaymentId;
            $paymentAttributeStatus->core_keys_id = $id;
            $paymentAttributeStatus->attribute_key = $this->pmtAttrPackageIapStatusCol;
            $paymentAttributeStatus->attribute_value = $paymentAttributeStatus->attribute_value=="1"?'0':'1';
            $this->paymentAttributeService->update($paymentAttributeStatus);
        }else{
            $paymentAttributeStatus = new PaymentAttribute();
            $paymentAttributeStatus->payment_id = $this->packageInAppPurchasePaymentId;
            $paymentAttributeStatus->core_keys_id = $id;
            $paymentAttributeStatus->attribute_key = $this->pmtAttrPackageIapStatusCol;
            $paymentAttributeStatus->attribute_value = 1;
            $this->paymentAttributeService->store($paymentAttributeStatus);
        }
        $dataArr = [
            'msg' => __('core__be_status_updated'),
            'flag' => $this->successFlag,
        ];
        return $dataArr;
    }

    public function getPackages(){

    }
}
