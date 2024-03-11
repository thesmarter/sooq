<?php

namespace Modules\Rating\Http\Services;

use App\Http\Services\PsService;
use Illuminate\Support\Facades\DB;
use Modules\Rating\Entities\Rating;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\CoreFieldFilterSetting;
use Modules\Core\Entities\CustomizeUi;
use Modules\Core\Entities\ScreenDisplayUiSetting;
use Modules\Core\Http\Services\ImageService;
use Modules\Core\Http\Services\PushNotificationTokenService;
use Modules\Core\Http\Services\UserService;
use Modules\Rating\Emails\SendEmailRatingReview;
use Modules\Rating\Transformers\Api\App\V1_0\RatingApiResource;
use Modules\Rating\Transformers\Backend\Model\Rating\RatingWithKeyResource;
use Modules\Core\Http\Services\UserAccessApiTokenService;
use App\Config\ps_constant;

class RatingService extends PsService
{
    protected $userAccessApiTokenService,$createdStatusCode, $ratingStatusCol, $coverImgType, $imageService, $ratingIdCol, $coreImageImgParentIdCol, $publish, $unPublish, $coreImageImgTypeCol, $ratingApiRelation, $pushNotificationTokenService, $userService, $ratingUserType, $noContentStatusCode, $successStatus, $show, $hide, $delete, $unDelete, $viewAnyAbility, $createAbility, $editAbility, $deleteAbility, $code, $screenDisplayUiKeyCol, $screenDisplayUiIdCol, $screenDisplayUiIsShowCol, $coreFieldFilterModuleNameCol, $customUiIsDelCol;

    public function __construct(ImageService $imageService, PushNotificationTokenService $pushNotificationTokenService, UserService $userService,UserAccessApiTokenService $userAccessApiTokenService)
    {
        $this->imageService = $imageService;
        $this->pushNotificationTokenService = $pushNotificationTokenService;
        $this->userService = $userService;

        $this->ratingIdCol = Rating::id;
        $this->ratingTitleCol = Rating::title;
        $this->ratingDescriptionCol = Rating::description;
        $this->ratingRatingCol = Rating::rating;
        $this->ratingTypeCol = Rating::type;
        $this->ratingFromUserIdCol = Rating::fromUserId;
        $this->ratingToUserIdCol = Rating::toUserId;

        $this->ratingApiRelation = ['fromUser', 'toUser'];

        $this->ratingUserType = Constants::ratingUserType;

        $this->noContentStatusCode = Constants::noContentStatusCode;
        $this->successStatus = Constants::successStatus;
        $this->createdStatusCode = Constants::createdStatusCode;
        $this->internalServerErrorStatusCode = Constants::internalServerErrorStatusCode;

        $this->show = Constants::show;
        $this->hide = Constants::hide;
        $this->delete = Constants::delete;
        $this->unDelete = Constants::unDelete;

        $this->viewAnyAbility = Constants::viewAnyAbility;
        $this->createAbility = Constants::createAbility;
        $this->editAbility = Constants::editAbility;
        $this->deleteAbility = Constants::deleteAbility;
        $this->code = Constants::rating;

        $this->screenDisplayUiKeyCol = ScreenDisplayUiSetting::key;
        $this->screenDisplayUiIdCol = ScreenDisplayUiSetting::id;
        $this->screenDisplayUiIsShowCol = ScreenDisplayUiSetting::isShow;

        $this->coreFieldFilterModuleNameCol = CoreFieldFilterSetting::moduleName;

        $this->customUiIsDelCol = CustomizeUi::isDelete;

        $this->forbiddenStatusCode = Constants::forbiddenStatusCode;

        $this->loginUserIdParaApi = ps_constant::loginUserIdParaFromApi;
        $this->deviceTokenParaApi = ps_constant::deviceTokenKeyFromApi;

        $this->userAccessApiTokenService = $userAccessApiTokenService;
    }

    public function store($request)
    {
        DB::beginTransaction();

        try {
            $rating = new Rating();

            if(isset($request->title) && !empty($request->title))
                $rating->title = $request->title;

            if (isset($request->description) && !empty($request->description))
                $rating->description = $request->description;

            if (isset($request->rating) && !empty($request->rating))
                $rating->rating = $request->rating;

            if (isset($request->from_user_id) && !empty($request->from_user_id))
                $rating->from_user_id = $request->from_user_id;

            if (isset($request->to_user_id) && !empty($request->to_user_id))
                $rating->to_user_id = $request->to_user_id;

            if (isset($request->transaction_header_id) && !empty($request->transaction_header_id))
                $rating->transaction_header_id = $request->transaction_header_id;

            if (isset($request->shop_id) && !empty($request->shop_id))
                $rating->shop_id = $request->shop_id;

            if (isset($request->item_id) && !empty($request->item_id))
                $rating->item_id = $request->item_id;

            if (isset($request->type) && !empty($request->type))
                $rating->type = $request->type;

            if (isset($request->added_user_id) && !empty($request->added_user_id))
                $rating->added_user_id = $request->added_user_id;
            else
                $rating->added_user_id = Auth::user()->id;

            $rating->save();

            DB::commit();
            return $rating;
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }

        return $rating;
    }

    public function update($id, $request)
    {
        DB::beginTransaction();

        try {
            $rating = $this->getRating($id);

            if (isset($request->title) && !empty($request->title))
                $rating->title = $request->title;

            if (isset($request->description) && !empty($request->description))
                $rating->description = $request->description;

            if (isset($request->rating) && !empty($request->rating))
                $rating->rating = $request->rating;

            if (isset($request->from_user_id) && !empty($request->from_user_id))
                $rating->from_user_id = $request->from_user_id;

            if (isset($request->to_user_id) && !empty($request->to_user_id))
                $rating->to_user_id = $request->to_user_id;

            if (isset($request->transaction_header_id) && !empty($request->transaction_header_id))
                $rating->transaction_header_id = $request->transaction_header_id;

            if (isset($request->shop_id) && !empty($request->shop_id))
                $rating->shop_id = $request->shop_id;

            if (isset($request->item_id) && !empty($request->item_id))
                $rating->item_id = $request->item_id;

            if (isset($request->type) && !empty($request->type))
                $rating->type = $request->type;

            if (isset($request->updated_user_id) && !empty($request->updated_user_id))
                $rating->updated_user_id = $request->updated_user_id;
            else
                $rating->updated_user_id = Auth::user()->id;

            $rating->save();

            $rating->update();

            DB::commit();
            return $rating;
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public function getRatings($relation = null, $limit = null, $offset = null, $conds = null)
    {
        $ratings = Rating::when($relation, function ($q, $relation) {
                        $q->with($relation);
                    })
                    ->when($limit, function ($query, $limit) {
                        $query->limit($limit);
                    })
                    ->when($offset, function ($query, $offset) {
                        $query->offset($offset);
                    })
                    ->when($conds, function ($query, $conds) {
                        if (isset($conds['from_user_id']) && !empty($conds['from_user_id'])) {
                            $query->where($this->ratingFromUserIdCol, $conds['from_user_id']);
                        }
                        if (isset($conds['to_user_id']) && !empty($conds['to_user_id'])) {
                            $query->where($this->ratingToUserIdCol, $conds['to_user_id']);
                        }
                        if (isset($conds['type']) && !empty($conds['type'])) {
                            $query->where($this->ratingTypeCol, $conds['type']);
                        }
                    })
                    ->latest()->get();
        return $ratings;
    }

    public function deleteAllBy($conds = null)
    {
        DB::beginTransaction();
        try {
            Rating::when($conds, function ($query, $conds) {
                $query->where($conds);

            })->delete();

            DB::commit();
            return true;
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public function getRating($id, $relation = null)
    {
        $rating = Rating::where($this->ratingIdCol, $id)
                    ->when($relation, function ($q, $relation) {
                        $q->with($relation);
                    })->first();
        return $rating;
    }

    //--------------
	public function index(){
        // check permission
        $checkPermission = $this->checkPermission($this->viewAnyAbility, Rating::class, "admin.index");

        $relation = ['fromUser', 'toUser'];
        $ratings = RatingWithKeyResource::collection($this->getRatings($relation));

        // taking for column and columnFilterOption
        $columnAndColumnFilter = $this->takingForColumnAndFilterOption();
        $showRatingCols = $columnAndColumnFilter['showCoreField'];
        $columnProps = $columnAndColumnFilter['arrForColumnProps'];
        $columnFilterOptionProps = $columnAndColumnFilter['arrForColumnFilterProps'];

        $dataArr = [
            'checkPermission' => $checkPermission,
            'ratings' => $ratings,
            'showRatingCols' => $showRatingCols,
            'showCoreAndCustomFieldArr' => $columnProps,
            'hideShowFieldForFilterArr' => $columnFilterOptionProps,
        ];
        return $dataArr;

    }

    public function edit($id)
    {
        $rating = $this->getRating($id);

        $dataArr = [
            "rating" => $rating,
        ];
        return $dataArr;
    }

    public function gallery($id)
    {
        $dataArr = [
            "id" => $id,
        ];
        return $dataArr;
    }

    public function destroy($id)
    {
        $rating = Rating::find($id);
        $name = $rating->name;

        $rating->delete();

        return $name;
    }

    public function countRatings($conds){
        return Rating::where($conds)->count();
    }

    public function sumRatings($col,$userId){
        return Rating::where('to_user_id',$userId)->sum($col);
    }

    // for api
    public function ratingFromApi($request)
    {
        // check permission start
        $deviceToken = $request->header($this->deviceTokenParaApi);
        $userAccessApiToken = $this->userAccessApiTokenService->getUserAccessApiToken( $request->login_user_id, $deviceToken);

        if (empty($userAccessApiToken)){
            $msg = __('core__api_update_no_permission',[],$request->language_symbol);
            return ['error' =>  $msg,'status' =>  $this->forbiddenStatusCode ];
        }
        // check permission end

        $exist = Rating::where('to_user_id',$request->to_user_id)
                    ->where('from_user_id',$request->from_user_id)->first();
        if($exist){
            $rating = $this->update($exist->id,$request);
        }else{
            $rating = $this->store($request);
        }



        // start update rating value at user
        if($request->type == $this->ratingUserType){
            $conds_rating['to_user_id'] = $rating->to_user_id;
            $total_rating_count = $this->countRatings($conds_rating);
            $sum_rating_value = $this->sumRatings('rating',$rating->to_user_id);

            if ($total_rating_count > 0) {
                $total_rating_value = number_format((float) ($sum_rating_value / $total_rating_count), 1, '.', '');
            } else {
                $total_rating_value = 0;
            }

            $user_data = new \stdClass();
            $user_data->id = $rating->to_user_id;
            $user_data->overall_rating = $total_rating_value;
            $this->userService->updateCoreFieldValues($user_data);
        }
        // end update rating value at user

        // start noti send to to_user_id when reviewed
        $conds['user_id'] = $rating['to_user_id'];
        $notiTokens = $this->pushNotificationTokenService->getNotiTokens(null, $conds);
        $device_ids = [];
        $platform_names = [];
        foreach($notiTokens as $token){
            $device_ids[] = $token->device_token;
            $platform_names[] = $token->platform_name;
        }
        $data['message'] = $rating['title'];
        $data['rating'] = $rating['rating'];
        $data['flag'] = Constants::reviewNotiFlag;
        $data['review_user_id'] = $rating['from_user_id'];
        send_android_fcm($device_ids, $data, $platform_names);
        // end noti send to to_user_id when reviewed

        // start send email to to_user_id
        $user_data = $this->userService->getUser($rating->to_user_id);
        $user_email = $user_data->email;

        if ($user_email ) {
            // send mail
            $to = $user_email;
            $subject = $rating->title;
            $to_name = $user_data->name;
            $title = $rating->title;
            $body = __('rating__receive_rating_from',[],$request->language_symbol) . ' ' . $this->userService->getUser($rating->from_user_id)->name;
            sendMail($to, $to_name, null, $subject, $body);
            // if(!sendMail($to, $to_name, null, $subject, $body)){
            //     return  ['error' => __('rating__email_not_sent',[],$request->language_symbol) ,'status' =>  $this->internalServerErrorStatusCode ];
            // }
        }
        // end send email to to_user_id

        $rating = $this->getRating($rating->id, $this->ratingApiRelation);
        return $rating;
    }

    public function noDataError($offset, $data){
        if ($offset > 0 && $data->isEmpty()) {
            // no paginate data
            $data = [];
            return responseDataApi($data);

        } else if($data->isEmpty()) {
            // no data db
            return responseMsgApi(__('core__no_data'), $this->noContentStatusCode, $this->successStatus);
        }
    }

    public function searchFromApi($request)
    {
        // check permission start
        $deviceToken = $request->header($this->deviceTokenParaApi);
        $userAccessApiToken = $this->userAccessApiTokenService->getUserAccessApiToken( $request->login_user_id, $deviceToken);

        if (empty($userAccessApiToken)){
            $msg = __('core__api_update_no_permission',[],$request->language_symbol);
            return ['error' =>  $msg,'status' =>  $this->forbiddenStatusCode ];
        }
        // check permission end

        $offset = $request->offset;
        $limit = $request->limit;

        $conds['to_user_id'] = $request->user_id;
        $conds['type'] = $request->type;

        $ratingApiRelation = $this->ratingApiRelation;
        $ratings = $this->getRatings($ratingApiRelation, $limit, $offset, $conds);

        return $ratings;

    }

    public function takingForColumnProps($label, $field, $type, $isShowSorting, $ordering){
        $hideShowCoreAndCustomFieldObj = new \stdClass();
        $hideShowCoreAndCustomFieldObj->label = $label;
        $hideShowCoreAndCustomFieldObj->field = $field;
        $hideShowCoreAndCustomFieldObj->type = $type;
        $hideShowCoreAndCustomFieldObj->sort = $isShowSorting;
        $hideShowCoreAndCustomFieldObj->ordering = $ordering;
        if ($type !== "Image" && $type !== "MultiSelect" && $type !== "Action"){
        $hideShowCoreAndCustomFieldObj->action = 'Action';
        }

        return $hideShowCoreAndCustomFieldObj;
    }

    public function takingForColumnAndFilterOption(){

        // for table
        $hideShowCoreFieldForColumnArr = [];
        $hideShowCustomFieldForColumnArr = [];

        $showProductCols = [];

        // for eye
        $hideShowFieldForColumnFilterArr = [];

        // for control
        $controlFieldArr = [];
        $controlFieldObj = $this->takingForColumnProps(__('core__be_action'), "action", "Action", false, 0);
        array_push($controlFieldArr, $controlFieldObj);

        $code = $this->code;
        $hiddenForCoreAndCustomField = $this->hiddenShownForCoreAndCustomField($this->hide, $code);
        $shownForCoreAndCustomField = $this->hiddenShownForCoreAndCustomField($this->show, $code);
        $hideShowForCoreAndCustomFields = $shownForCoreAndCustomField->merge($hiddenForCoreAndCustomField);

        foreach ($hideShowForCoreAndCustomFields as $showFields){
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
            array_push($showProductCols, $cols);

        }if ($showFields->customizeField !== null) {

            $label = $showFields->customizeField->name;
            $uiHaveAttribute = [$this->dropDownUi, $this->radioUi];
            if (in_array($showFields->customizeField->ui_type_id, $uiHaveAttribute)){
                $field = $showFields->customizeField->core_keys_id."@@name";
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
        "showCoreField" => $showProductCols,
        ];
        return $dataArr;

    }

    public function hiddenShownForCoreAndCustomField($isShown, $code){
        $hiddenShownForFields = ScreenDisplayUiSetting::with(['customizeField' => function ($q){
        $q->where($this->customUiIsDelCol, $this->unDelete);
        }, 'coreField'=> function($q){
            $q->where($this->coreFieldFilterModuleNameCol, $this->code);
        }])
        ->where($this->coreFieldFilterModuleNameCol, $code)
        ->where($this->screenDisplayUiIsShowCol, $isShown)
        ->get();
        return $hiddenShownForFields;
    }

    public function takingForColumnFilterProps($id, $label, $field, $hidden, $moduleName, $keyId){
        $hideShowCoreAndCustomFieldObj = new \stdClass();
        $hideShowCoreAndCustomFieldObj->id = $id;
        $hideShowCoreAndCustomFieldObj->label = $label;
        $hideShowCoreAndCustomFieldObj->key = $field;
        $hideShowCoreAndCustomFieldObj->hidden = $hidden;
        $hideShowCoreAndCustomFieldObj->module_name = $moduleName;
        $hideShowCoreAndCustomFieldObj->key_id = $keyId;

        return $hideShowCoreAndCustomFieldObj;
    }
}
