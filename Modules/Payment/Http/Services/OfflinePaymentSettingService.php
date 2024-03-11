<?php

namespace Modules\Payment\Http\Services;

use App\Config\ps_constant;
use App\Http\Services\PsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\CoreFieldFilterSetting;
use Modules\Core\Entities\CoreImage;
use Modules\Core\Entities\CoreKey;
use Modules\Core\Entities\ScreenDisplayUiSetting;
use Modules\Core\Http\Services\CoreKeyService;
use Modules\Core\Http\Services\ImageService;
use Modules\Core\Http\Services\UserAccessApiTokenService;
use Modules\Payment\Entities\CoreKeyPaymentRelation;
use Modules\Payment\Entities\PaymentAttribute;
use Modules\Payment\Entities\PaymentInfo;
use Modules\Payment\Transformers\Api\App\V1_0\Payment\OfflinePaymentSettingApiResource;
use Modules\Payment\Transformers\Backend\NoModel\OfflinePaymentSetting\OfflinePaymentSettingWithKeyResource;

class OfflinePaymentSettingService extends PsService
{
    protected $coreImageImgTypeCol, $coreImageImgParentIdCol, $offlinePaymentId, $successFlag, $paymentAttributeService, $pmtAttrOfflinePaymentStatusCol, $dangerFlag, $hide, $show, $successStatus, $noContentStatusCode, $iconImgType, $imageService, $paymentInfoIdCol, $paymentInfoCoreKeysIdCol, $paymentInfoPaymentIdCol, $paymentInfoValueCol, $publish, $unPublish, $coreKeyPaymentRelationService, $paymentService, $offlinePaymentSetting, $coreKeyService, $code, $paymentSettingService, $offlinePaymentApiRelations, $deviceTokenParaApi, $loginUserIdParaApi, $userAccessApiTokenService, $forbiddenStatusCode, $coreFieldFilterModuleNameCol, $coreFieldFilterIdCol, $screenDisplayUiKeyCol, $screenDisplayUiIdCol, $screenDisplayUiIsShowCol;

    public function __construct(PaymentAttributeService $paymentAttributeService, UserAccessApiTokenService $userAccessApiTokenService, ImageService $imageService, CoreKeyPaymentRelationService $coreKeyPaymentRelationService, PaymentService $paymentService, CoreKeyService $coreKeyService, PaymentSettingService $paymentSettingService)
    {
        $this->imageService = $imageService;
        $this->coreImageImgParentIdCol = CoreImage::imgParentId;
        $this->coreImageImgTypeCol = CoreImage::imgType;
        $this->iconImgType = 'offline-payment-icon';

        $this->paymentInfoIdCol = PaymentInfo::id;
        $this->paymentInfoCoreKeysIdCol = PaymentInfo::coreKeysId;
        $this->paymentInfoPaymentIdCol = PaymentInfo::paymentId;
        $this->paymentInfoValueCol = PaymentInfo::value;

        $this->publish = Constants::publish;
        $this->unPublish = Constants::unPublish;
        $this->offlinePaymentId = Constants::offlinePaymentId;
        $this->code = Constants::payment;
        $this->hide = Constants::hide;
        $this->show = Constants::show;
        $this->dangerFlag = Constants::danger;
        $this->successFlag = Constants::success;
        $this->pmtAttrOfflinePaymentStatusCol = Constants::pmtAttrOfflinePaymentStatusCol;

        $this->coreKeyPaymentRelationService = $coreKeyPaymentRelationService;
        $this->paymentService = $paymentService;
        $this->coreKeyService = $coreKeyService;
        $this->paymentSettingService = $paymentSettingService;
        $this->paymentAttributeService = $paymentAttributeService;

        $this->offlinePaymentApiRelations = ['payment', 'core_key', 'offline_icon'];

        $this->noContentStatusCode = Constants::noContentStatusCode;
        $this->successStatus = Constants::successStatus;
        $this->forbiddenStatusCode = Constants::forbiddenStatusCode;

        $this->userAccessApiTokenService = $userAccessApiTokenService;
        $this->loginUserIdParaApi = ps_constant::loginUserIdParaFromApi;
        $this->deviceTokenParaApi = ps_constant::deviceTokenKeyFromApi;

        $this->coreFieldFilterModuleNameCol = CoreFieldFilterSetting::moduleName;
        $this->coreFieldFilterIdCol = CoreFieldFilterSetting::id;

        $this->screenDisplayUiKeyCol = ScreenDisplayUiSetting::key;
        $this->screenDisplayUiIdCol = ScreenDisplayUiSetting::id;
        $this->screenDisplayUiIsShowCol = ScreenDisplayUiSetting::isShow;

    }

    public function index($request)
    {
        $code = $this->code;

        // Search and filter
        $conds['searchterm'] = $request->input('search') ?? '';

        $conds['order_by']= null;
        $conds['order_type'] = null;
        $row = $request->input('row') ?? Constants::dataTableDefaultRow;

        if($request->sort_field)
        {
            $conds['order_by'] = $request->sort_field;
            $conds['order_type'] = $request->sort_order;
        }

        $conds['payment_id'] = $this->offlinePaymentId;

        $offlinePayments = OfflinePaymentSettingWithKeyResource::collection($this->paymentSettingService->getPaymentInfos(['core_key', 'statusAttribute'], null, null, $conds, false,$row));
        // dd($offlinePayments);

        if($conds['order_by'])
        {
            $dataArr = [
                "offlinePayments" => $offlinePayments,
                'sort_field' =>$conds['order_by'],
                'sort_order'=>$request->sort_order,
                'search'=>$conds['searchterm'] ,
            ];
        }
        else
        {
            $dataArr = [
                "offlinePayments" => $offlinePayments,
                'search'=>$conds['searchterm'] ,
            ];
        }

        return $dataArr;
    }

    public function store($request)
    {
        DB::beginTransaction();

        try {
            // save title as name to core_keys table (description not used yet but also save title as descrition)
            $coreKey = new \stdClass();
            $coreKey->name = $request->title;
            $coreKey->description = $request->title;
            $core_key = $this->coreKeyService->store($coreKey, $this->code);

            // save core_keys_id and payment_id to core_key_payment_relations table
            $coreKeyPaymentRelation = new \stdClass();
            $coreKeyPaymentRelation->core_keys_id = $core_key->core_keys_id;
            $coreKeyPaymentRelation->payment_id = $this->offlinePaymentId;
            // dd($coreKeyPaymentRelation);
            $this->coreKeyPaymentRelationService->store($coreKeyPaymentRelation);

            // save core_keys_id, payment_id and value to payment_infos table
            $paymentInfo = new PaymentInfo();
            $paymentInfo->core_keys_id = $core_key->core_keys_id;
            $paymentInfo->payment_id = $this->offlinePaymentId;
            // description as value to save payment_infos
            if (isset($request->description) && !empty($request->description))
                $paymentInfo->value = $request->description;
            if (isset($request->added_user_id) && !empty($request->added_user_id))
                $paymentInfo->added_user_id = $request->added_user_id;
            else
                $paymentInfo->added_user_id = Auth::user()->id;
            $paymentInfo->save();

            $imgParentId = $paymentInfo->id;

            // save offline payment icon photo to core_images with image type (offline-payment-icon)
            $this->updateOrCreateImage($request, "icon", $imgParentId, $this->iconImgType);

            // save payment_attributes table For Status Col
            $paymentAttributeStatus = new PaymentAttribute();
            $paymentAttributeStatus->payment_id = $this->offlinePaymentId;
            $paymentAttributeStatus->core_keys_id = $core_key->core_keys_id;
            $paymentAttributeStatus->attribute_key = $this->pmtAttrOfflinePaymentStatusCol;
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
        DB::beginTransaction();




            // update core_keys table
            $coreKey = new \stdClass();
            $coreKey->name = $request->title;
            $coreKey->description = $request->title;
            $this->coreKeyService->update($request->core_keys_id, $coreKey);

            // update payment_infos table
            $paymentInfo = $this->getPaymentInfo($id);
            $paymentInfo->core_keys_id = $request->core_keys_id;
            $paymentInfo->payment_id = $this->offlinePaymentId;
            if (isset($request->description) && !empty($request->description))
                $paymentInfo->value = $request->description;
            if (isset($request->updated_user_id) && !empty($request->updated_user_id))
                $paymentInfo->updated_user_id = $request->updated_user_id;
            else
                $paymentInfo->updated_user_id = Auth::user()->id;
            $paymentInfo->update();

            $imgParentId = $paymentInfo->id;

            // update or create offline payment icon photo to core_images
            $this->updateOrCreateImage($request, "icon", $imgParentId, $this->iconImgType);

            // update payment_attributes table For Status Col
            $conds['attribute_key'] = $this->pmtAttrOfflinePaymentStatusCol;
            $conds['core_keys_id'] = $request->core_keys_id;
            $paymentAttributeStatus = $this->paymentAttributeService->getPaymentAttribute(null, $conds);
            $paymentAttributeStatus->payment_id = $this->offlinePaymentId;
            $paymentAttributeStatus->core_keys_id = $request->core_keys_id;
            $paymentAttributeStatus->attribute_key = $this->pmtAttrOfflinePaymentStatusCol;
            $paymentAttributeStatus->attribute_value = $request->status==false?'0':'1';
            $this->paymentAttributeService->update($paymentAttributeStatus);

            DB::commit();
            return $paymentInfo;

    }

    public function updateOrCreateImage($request, $fileKey, $imgParentId, $imgType)
    {
        if ($request->file($fileKey)) {

            $conds[$this->coreImageImgParentIdCol] = $imgParentId;
            $conds[$this->coreImageImgTypeCol] = $imgType;
            $image = $this->getImage($conds);

            $file = $request->file($fileKey);
            $data[$this->coreImageImgParentIdCol] = $imgParentId;
            $data[$this->coreImageImgTypeCol] = $imgType;

            // if image, delete existing file and update
            if (!empty($image)) {
                // delete image from storage folder
                dd("here");
                $this->imageService->deleteImage($image->img_path);
                $this->imageService->update(null,null,$file, $data, $image);
            } else {
                $this->imageService->update(null,null,$file, $data);
            }
        }
    }

    public function getImages($paymentInfo)
    {
        $images = CoreImage::where($this->coreImageImgParentIdCol, $paymentInfo->id)->get();
        return $images;
    }

    public function getImage($conds)
    {
        $image = $this->imageService->getImage($conds);
        return $image;
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

        if (isset($conds['payment_id']) && $conds['payment_id']) {
            $query->where('payment_id', $conds['payment_id']);
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

    public function edit($id)
    {
        $dataWithRelation = ['core_key', 'offline_icon','statusAttribute'];
        $offlinePayment = $this->getPaymentInfo($id, $dataWithRelation);
        // dd($offlinePayment);

        $dataArr = [
            "offlinePayment" => $offlinePayment,
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

        $images = $this->getImages($paymentInfo);
        deleteImages($images);

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


    public function takingForColumnProps($label, $field, $type)
    {
        $hideShowCoreAndCustomFieldObj = new \stdClass();
        $hideShowCoreAndCustomFieldObj->label = $label;
        $hideShowCoreAndCustomFieldObj->field = $field;
        $hideShowCoreAndCustomFieldObj->type = $type;
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

            $offlinePaymentApiRelations = $this->offlinePaymentApiRelations;
            $conds['payment_id'] = $this->offlinePaymentId;
            $conds['status'] = 1;

            $attributes = [
                $this->pmtAttrOfflinePaymentStatusCol
            ];
            $offlinePayments = OfflinePaymentSettingApiResource::collection($this->paymentSettingService->getPaymentInfos($offlinePaymentApiRelations, $limit, $offset, $conds, true,null, $attributes));

            if ($offset > 0 && $offlinePayments->isEmpty()) {
                // no paginate data
                $data = [];
                return responseDataApi($data);

            } else if($offlinePayments->isEmpty()) {
                // no data db
                return responseMsgApi(__('payment__api_no_data'), $this->noContentStatusCode, $this->successStatus);
            } else {
                return responseDataApi($offlinePayments);
            }
        }
    }

    public function makePublishOrUnpublish($id){
        // update payment attributes table For Status Col
        $conds['attribute_key'] = $this->pmtAttrOfflinePaymentStatusCol;
        $conds['core_keys_id'] = $id;
        $paymentAttributeStatus = $this->paymentAttributeService->getPaymentAttribute(null, $conds);
        if($paymentAttributeStatus){
            $paymentAttributeStatus->payment_id = $this->offlinePaymentId;
            $paymentAttributeStatus->core_keys_id = $id;
            $paymentAttributeStatus->attribute_key = $this->pmtAttrOfflinePaymentStatusCol;
            $paymentAttributeStatus->attribute_value = $paymentAttributeStatus->attribute_value=="1"?'0':'1';
            $this->paymentAttributeService->update($paymentAttributeStatus);
        }else{
            $paymentAttributeStatus = new PaymentAttribute();
            $paymentAttributeStatus->payment_id = $this->offlinePaymentId;
            $paymentAttributeStatus->core_keys_id = $id;
            $paymentAttributeStatus->attribute_key = $this->pmtAttrOfflinePaymentStatusCol;
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
