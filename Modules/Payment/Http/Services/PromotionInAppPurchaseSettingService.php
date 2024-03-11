<?php

namespace Modules\Payment\Http\Services;

use App\Http\Services\PsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\CoreFieldFilterSetting;
use Modules\Core\Entities\CoreKey;
use Modules\Core\Entities\ScreenDisplayUiSetting;
use Modules\Core\Http\Services\CoreKeyService;
use Modules\Payment\Entities\CoreKeyPaymentRelation;
use Modules\Payment\Entities\PaymentAttribute;
use Modules\Payment\Entities\PaymentInfo;
use Modules\Payment\Transformers\Backend\NoModel\PromotionInAppPurchase\PromotionInAppPurchaseWithKeyResource;
use PHPUnit\TextUI\XmlConfiguration\Constant;
use Modules\Core\Entities\CustomizeUi;

class PromotionInAppPurchaseSettingService extends PsService
{
    protected $iapTypeAndroid, $iapTypeIOS, $dangerFlag, $successFlag, $offlinePaymentId, $paymentInfoIdCol, $paymentInfoCoreKeysIdCol, $pmtAttrPromoteIapStatusCol, $paymentInfoPaymentIdCol, $paymentInfoValueCol, $publish, $unPublish, $coreKeyPaymentRelationService, $paymentService, $inAppPurchaseSetting, $coreKeyService, $code, $paymentSettingService, $inAppPurchaseApiRelations, $pmtAttrPromoteIapTypeCol, $pmtAttrPromoteIapDayCol, $paymentAttributeService, $hide, $show, $coreFieldFilterModuleNameCol, $coreFieldFilterIdCol, $screenDisplayUiKeyCol, $screenDisplayUiIdCol, $screenDisplayUiIsShowCol;

    public function __construct(CoreKeyPaymentRelationService $coreKeyPaymentRelationService, PaymentService $paymentService, CoreKeyService $coreKeyService, PaymentSettingService $paymentSettingService,PaymentAttributeService $paymentAttributeService)
    {
        $this->paymentInfoIdCol = PaymentInfo::id;
        $this->paymentInfoCoreKeysIdCol = PaymentInfo::coreKeysId;
        $this->paymentInfoPaymentIdCol = PaymentInfo::paymentId;
        $this->paymentInfoValueCol = PaymentInfo::value;

        $this->publish = Constants::publish;
        $this->unPublish = Constants::unPublish;
        $this->offlinePaymentId = Constants::offlinePaymentId;
        $this->promotionInAppPurchasePaymentId = Constants::promotionInAppPurchasePaymentId;
        $this->code = Constants::payment;
        $this->pmtAttrPromoteIapTypeCol = Constants::pmtAttrPromoteIapTypeCol;
        $this->pmtAttrPromoteIapDayCol = Constants::pmtAttrPromoteIapDayCol;
        $this->pmtAttrPromoteIapStatusCol = Constants::pmtAttrPromoteIapStatusCol;

        $this->coreKeyPaymentRelationService = $coreKeyPaymentRelationService;
        $this->paymentService = $paymentService;
        $this->coreKeyService = $coreKeyService;
        $this->paymentSettingService = $paymentSettingService;
        $this->paymentAttributeService = $paymentAttributeService;

        $this->customUiEnableCol = CustomizeUi::enable;
        $this->customUiIsDelCol = CustomizeUi::isDelete;

        $this->inAppPurchaseApiRelations = ['payment', 'core_key', 'payment_attributes'];

        $this->show = Constants::show;
        $this->hide = Constants::hide;
        $this->enable = Constants::enable;
        $this->disable = Constants::disable;
        $this->delete = Constants::delete;
        $this->unDelete = Constants::unDelete;
        $this->successFlag= Constants::success;
        $this->dangerFlag = Constants::danger;

        $this->coreFieldFilterModuleNameCol = CoreFieldFilterSetting::moduleName;
        $this->coreFieldFilterIdCol = CoreFieldFilterSetting::id;

        $this->screenDisplayUiKeyCol = ScreenDisplayUiSetting::key;
        $this->screenDisplayUiIdCol = ScreenDisplayUiSetting::id;
        $this->screenDisplayUiIsShowCol = ScreenDisplayUiSetting::isShow;

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

        $this->iapTypeAndroid = Constants::iapTypeAndroid;
        $this->iapTypeIOS = Constants::iapTypeIOS;
    }

    public function index($request)
    {

        $code = $this->code;

        // Search and filter
        $conds['searchterm'] = $request->input('search') ?? '';
        $conds['type'] = $request->input('type_filter') == 'all'? null  : $request->type_filter;

        $conds['order_by']= null;
        $conds['order_type'] = null;
        $row = $request->input('row') ?? Constants::dataTableDefaultRow;

        if($request->sort_field)
        {
            $conds['order_by'] = $request->sort_field;
            $conds['order_type'] = $request->sort_order;
        }

        $conds['payment_id'] = $this->promotionInAppPurchasePaymentId;
        $relations = ['core_key','statusAttribute'];

        /**
         * attribute_key in payment_attributes table for promotion iap
         * used to convert row to col for attributes (type, status, day) in getPaymentInfos() function
         */
        $attributes = [
            $this->pmtAttrPromoteIapDayCol,
            $this->pmtAttrPromoteIapTypeCol,
            $this->pmtAttrPromoteIapStatusCol
        ];
        $service = 'PromotionIAP';
        $inAppPurchases = PromotionInAppPurchaseWithKeyResource::collection($this->paymentSettingService->getPaymentInfos($relations, null, null, $conds, false, $row, $attributes,$service));

        // taking for column and columnFilterOption
        $columnAndColumnFilter = $this->takingForColumnAndFilterOption();
        $columnProps = $columnAndColumnFilter['arrForColumnProps'];
        $columnFilterOptionProps = $columnAndColumnFilter['arrForColumnFilterProps'];

        // for pmtAttrPromoteIapTypeCol (Android or IOS)
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

        if($conds['order_by'])
        {
            $dataArr = [
                'showCoreAndCustomFieldArr' => $columnProps,
                'hideShowFieldForFilterArr' => $columnFilterOptionProps,
                "inAppPurchases" => $inAppPurchases,
                "dayKey" => $this->pmtAttrPromoteIapDayCol,
                "typeKey" => $this->pmtAttrPromoteIapTypeCol,
                "statusKey" => $this->pmtAttrPromoteIapStatusCol,
                'sort_field' =>$conds['order_by'],
                'sort_order'=>$request->sort_order,
                'search'=>$conds['searchterm'] ,
                'types' => $types,
                'selected_type'=>$conds['type'] ,
            ];
        }
        else
        {
            $dataArr = [
                'showCoreAndCustomFieldArr' => $columnProps,
                'hideShowFieldForFilterArr' => $columnFilterOptionProps,
                "inAppPurchases" => $inAppPurchases,
                "dayKey" => $this->pmtAttrPromoteIapDayCol,
                "typeKey" => $this->pmtAttrPromoteIapTypeCol,
                "statusKey" => $this->pmtAttrPromoteIapStatusCol,
                'search'=>$conds['searchterm'] ,
                'types' => $types,
                'selected_type'=>$conds['type'] ,
            ];
        }
        return $dataArr;
    }

    public function store($request)
    {
        DB::beginTransaction();

        try {
            // save in_app_purchase_prd_id as name to core_keys table (description not used yet but also save in_app_purchase_prd_id as descrition)
            $coreKey = new \stdClass();
            $coreKey->name = $request->in_app_purchase_prd_id;
            $coreKey->description = $request->in_app_purchase_prd_id;
            $core_key = $this->coreKeyService->store($coreKey, $this->code);

            // save core_keys_id and payment_id to core_key_payment_relations table
            $coreKeyPaymentRelation = new \stdClass();
            $coreKeyPaymentRelation->core_keys_id = $core_key->core_keys_id;
            $coreKeyPaymentRelation->payment_id = $this->promotionInAppPurchasePaymentId;
            $this->coreKeyPaymentRelationService->store($coreKeyPaymentRelation);

            // save core_keys_id, payment_id and value to payment_infos table
            $paymentInfo = new PaymentInfo();
            $paymentInfo->core_keys_id = $core_key->core_keys_id;
            $paymentInfo->payment_id = $this->promotionInAppPurchasePaymentId;
            // save description as value to payment_infos
            if (isset($request->description) && !empty($request->description))
                $paymentInfo->value = $request->description;
            if (isset($request->added_user_id) && !empty($request->added_user_id))
                $paymentInfo->added_user_id = $request->added_user_id;
            else
                $paymentInfo->added_user_id = Auth::user()->id;
            $paymentInfo->save();

            // save payment_attributes table For Type Col
            $paymentAttributeType = new PaymentAttribute();
            $paymentAttributeType->payment_id = $this->promotionInAppPurchasePaymentId;
            $paymentAttributeType->core_keys_id = $core_key->core_keys_id;
            $paymentAttributeType->attribute_key = $this->pmtAttrPromoteIapTypeCol;
            $paymentAttributeType->attribute_value = $request->type;
            $this->paymentAttributeService->store($paymentAttributeType);

            // save payment_attributes table For Day Col
            $paymentAttributeDay = new PaymentAttribute();
            $paymentAttributeDay->payment_id = $this->promotionInAppPurchasePaymentId;
            $paymentAttributeDay->core_keys_id = $core_key->core_keys_id;
            $paymentAttributeDay->attribute_key = $this->pmtAttrPromoteIapDayCol;
            $paymentAttributeDay->attribute_value = $request->day;
            $this->paymentAttributeService->store($paymentAttributeDay);

            // save payment_attributes table For Status Col
            $paymentAttributeStatus = new PaymentAttribute();
            $paymentAttributeStatus->payment_id = $this->promotionInAppPurchasePaymentId;
            $paymentAttributeStatus->core_keys_id = $core_key->core_keys_id;
            $paymentAttributeStatus->attribute_key = $this->pmtAttrPromoteIapStatusCol;
            $paymentAttributeStatus->attribute_value = $request->status == false?'0':'1';
            $this->paymentAttributeService->store($paymentAttributeStatus);

            DB::commit();
            return $paymentInfo;
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public function update($id, $request)
    {
        // dd($id);
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
            $paymentInfo->payment_id = $this->promotionInAppPurchasePaymentId;
            if (isset($request->description) && !empty($request->description))
                $paymentInfo->value = $request->description;
            if (isset($request->updated_user_id) && !empty($request->updated_user_id))
            $paymentInfo->updated_user_id = $request->updated_user_id;
            else
            $paymentInfo->updated_user_id = Auth::user()->id;
            $paymentInfo->update();

            // update payment attributes table For Type Col
            $conds['attribute_key'] = $this->pmtAttrPromoteIapTypeCol;
            $conds['core_keys_id'] = $request->core_keys_id;
            $paymentAttributeType = $this->paymentAttributeService->getPaymentAttribute(null, $conds);
            if($paymentAttributeType){
                $paymentAttributeType->payment_id = $this->promotionInAppPurchasePaymentId;
                $paymentAttributeType->core_keys_id = $request->core_keys_id;
                $paymentAttributeType->attribute_key = $this->pmtAttrPromoteIapTypeCol;
                $paymentAttributeType->attribute_value = $request->type;
                $this->paymentAttributeService->update($paymentAttributeType);
            }else{
                $paymentAttributeType = new PaymentAttribute();
                $paymentAttributeType->payment_id = $this->promotionInAppPurchasePaymentId;
                $paymentAttributeType->core_keys_id = $request->core_keys_id;
                $paymentAttributeType->attribute_key = $this->pmtAttrPromoteIapTypeCol;
                $paymentAttributeType->attribute_value = $request->type;
                $this->paymentAttributeService->store($paymentAttributeType);
            }

            // update payment attributes table For Day Col
            $conds['attribute_key'] = $this->pmtAttrPromoteIapDayCol;
            $conds['core_keys_id'] = $request->core_keys_id;
            $paymentAttributeDay = $this->paymentAttributeService->getPaymentAttribute(null, $conds);
            if($paymentAttributeDay){
                $paymentAttributeDay->payment_id = $this->promotionInAppPurchasePaymentId;
                $paymentAttributeDay->core_keys_id = $request->core_keys_id;
                $paymentAttributeDay->attribute_key = $this->pmtAttrPromoteIapDayCol;
                $paymentAttributeDay->attribute_value = $request->day;
                $this->paymentAttributeService->update($paymentAttributeDay);
            }else{
                $paymentAttributeDay = new PaymentAttribute();
                $paymentAttributeDay->payment_id = $this->promotionInAppPurchasePaymentId;
                $paymentAttributeDay->core_keys_id = $request->core_keys_id;
                $paymentAttributeDay->attribute_key = $this->pmtAttrPromoteIapDayCol;
                $paymentAttributeDay->attribute_value = $request->day;
                $this->paymentAttributeService->store($paymentAttributeDay);
            }

            // update payment attributes table For Status Col
            $conds['attribute_key'] = $this->pmtAttrPromoteIapStatusCol;
            $conds['core_keys_id'] = $request->core_keys_id;
            $paymentAttributeStatus = $this->paymentAttributeService->getPaymentAttribute(null, $conds);
            if($paymentAttributeStatus){
                $paymentAttributeStatus->payment_id = $this->promotionInAppPurchasePaymentId;
                $paymentAttributeStatus->core_keys_id = $request->core_keys_id;
                $paymentAttributeStatus->attribute_key = $this->pmtAttrPromoteIapStatusCol;
                $paymentAttributeStatus->attribute_value = $request->status==false?'0':'1';
                $this->paymentAttributeService->update($paymentAttributeStatus);
            }else{
                $paymentAttributeStatus = new PaymentAttribute();
                $paymentAttributeStatus->payment_id = $this->promotionInAppPurchasePaymentId;
                $paymentAttributeStatus->core_keys_id = $request->core_keys_id;
                $paymentAttributeStatus->attribute_key = $this->pmtAttrPromoteIapStatusCol;
                $paymentAttributeStatus->attribute_value = $request->status == false?'0':'1';
                $this->paymentAttributeService->store($paymentAttributeStatus);
            }

            DB::commit();
            return $paymentInfo;
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public function searching($query, $conds){

        // search term
        if (isset($conds['searchterm']) && $conds['searchterm']) {
            $search = $conds['searchterm'];
            $query->where(function ($query) use ($search) {
                $query->where($this->paymentInfoValueCol, 'like', '%' . $search . '%');
            });
        }

        if(isset($conds['added_date']) && $conds['added_date']){
            $date_filter = $conds['added_date'];
            $query->whereHas('added_date', function($q) use($date_filter){
                $q->where('added_date', $date_filter);
            });
        }

        if (isset($conds['added_user_id']) && $conds['added_user_id']) {
            $query->where('added_user_id', $conds['added_user_id']);
        }

        // order by
        if (isset($conds['order_by']) && isset($conds['order_type']) && $conds['order_by'] && $conds['order_type']){
            if($conds['order_by'] == 'id'){
                $query->orderBy('id', $conds['order_type']);
            }else{
                $query->orderBy($conds['order_by'], $conds['order_type']);
            }
        }

        return $query;
    }

    public function getPaymentInfos($relations = null, $limit = null, $offset = null, $conds = null, $noPagination = null, $pagPerPage = null)
    {
        $paymentInfos = PaymentInfo::when($relations, function ($query, $relations) {
                $query->with($relations);
            })
            ->when($conds, function ($query, $conds) {
                $query = $this->searching($query, $conds);
            })
            ->when($limit, function ($query, $limit) {
                $query->limit($limit);
            })
            ->when($offset, function ($query, $offset) {
                $query->offset($offset);
            })
            ->latest();

            if ($pagPerPage){
                $paymentInfos = $paymentInfos->paginate($pagPerPage)->onEachSide(1)->withQueryString();
            } elseif ($noPagination){
                $paymentInfos = $paymentInfos->get();
            }

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

    public function create()
    {
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

        $dataArr = [
            "types" => $types,
        ];
        return $dataArr;
    }

    public function edit($id)
    {
        $day_attribute = '';
        $type_attribute = '';
        $status_attribute = 0;

        $relations = ['core_key', 'payment_attributes'];
        $inAppPurchase = $this->getPaymentInfo($id, $relations);

        foreach($inAppPurchase['payment_attributes'] as $attribute){
            if ($attribute['attribute_key'] == $this->pmtAttrPromoteIapDayCol) {
                $day_attribute = $attribute['attribute_value'];
            }

            if ($attribute['attribute_key'] == $this->pmtAttrPromoteIapTypeCol) {
                $type_attribute = $attribute['attribute_value'];
            }

            if ($attribute['attribute_key'] == $this->pmtAttrPromoteIapStatusCol) {
                $status_attribute = $attribute['attribute_value'];
            }
        }

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

        $dataArr = [
            "inAppPurchase" => $inAppPurchase,
            "day_attribute" => $day_attribute,
            "type_attribute" => $type_attribute,
            "status_attribute" => $status_attribute,
            "types" => $types,
        ];
        return $dataArr;
    }

    public function destroy($id)
    {
        $paymentInfo = PaymentInfo::find($id);
        $coreKey = CoreKey::where('core_keys_id', $paymentInfo->core_keys_id)->first();
        $coreKeyPaymentRelation = CoreKeyPaymentRelation::where('core_keys_id', $paymentInfo->core_keys_id)->first();
        $paymentAttributes = PaymentAttribute::where('core_keys_id', $paymentInfo->core_keys_id)->get();
        $name = $coreKey->name;

        $paymentInfo->delete();
        $coreKey->delete();
        $coreKeyPaymentRelation->delete();

//        foreach($paymentAttributes as $attribute){
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

    public function makePublishOrUnpublish($id){
        // update payment attributes table For Status Col
        $conds['attribute_key'] = $this->pmtAttrPromoteIapStatusCol;
        $conds['core_keys_id'] = $id;
        $paymentAttributeStatus = $this->paymentAttributeService->getPaymentAttribute(null, $conds);
        if($paymentAttributeStatus){
            $paymentAttributeStatus->payment_id = $this->promotionInAppPurchasePaymentId;
            $paymentAttributeStatus->core_keys_id = $id;
            $paymentAttributeStatus->attribute_key = $this->pmtAttrPromoteIapStatusCol;
            $paymentAttributeStatus->attribute_value = $paymentAttributeStatus->attribute_value=="1"?'0':'1';
            $this->paymentAttributeService->update($paymentAttributeStatus);
        }else{
            $paymentAttributeStatus = new PaymentAttribute();
            $paymentAttributeStatus->payment_id = $this->promotionInAppPurchasePaymentId;
            $paymentAttributeStatus->core_keys_id = $id;
            $paymentAttributeStatus->attribute_key = $this->pmtAttrPromoteIapStatusCol;
            $paymentAttributeStatus->attribute_value = 1;
            $this->paymentAttributeService->store($paymentAttributeStatus);
        }
        $dataArr = [
            'msg' => __('core__be_status_updated'),
            'flag' => $this->successFlag,
        ];
        return $dataArr;
    }
}
