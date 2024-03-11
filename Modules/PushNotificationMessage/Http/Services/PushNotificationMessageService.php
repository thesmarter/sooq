<?php
namespace Modules\PushNotificationMessage\Http\Services;

use App\Http\Services\PsService;
use Illuminate\Support\Facades\DB;
use Modules\Core\Constants\Constants;
use Modules\PushNotificationMessage\Entities\PushNotificationMessage;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Entities\CoreFieldFilterSetting;
use Modules\Core\Entities\CoreImage;
use Modules\Core\Entities\CustomizeUi;
use Modules\Core\Entities\PushNotificationUser;
use Modules\Core\Entities\ScreenDisplayUiSetting;
use Modules\Core\Http\Services\ImageService;
use Modules\PushNotificationMessage\Transformers\Backend\Model\PushNotificationMessage\PushNotificationMessageWithKeyResource;

class PushNotificationMessageService extends PsService
{
    protected $radioUi, $dropDownUi, $pushNotiMessageApiRelation, $tableName, $publish, $coverImgType, $imageService, $pushNotificationMessageIdCol, $pushNotificationMesssageMessageCol, $pushNotificationMessageDescriptionCol, $coreImageImgParentIdCol, $coreImageImgTypeCol, $successStatus, $createdStatusCode, $okStatusCode, $internalServerErrorStatusCode, $noContentStatusCode, $notFoundStatusCode, $show, $hide, $delete, $unDelete, $viewAnyAbility, $createAbility, $editAbility, $deleteAbility, $code, $screenDisplayUiKeyCol, $screenDisplayUiIdCol, $screenDisplayUiIsShowCol, $coreFieldFilterModuleNameCol, $customUiIsDelCol;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;

        $this->dropDownUi = Constants::dropDownUi;
        $this->radioUi = Constants::radioUi;
        $this->tableName = PushNotificationMessage::tableName;
        $this->pushNotificationMessageIdCol = PushNotificationMessage::id;
        $this->pushNotificationMesssageMessageCol = PushNotificationMessage::message;
        $this->pushNotificationMessageDescriptionCol = PushNotificationMessage::description;
        $this->coreImageImgParentIdCol = CoreImage::imgParentId;
        $this->coreImageImgTypeCol = CoreImage::imgType;
        $this->successStatus = Constants::successStatus;
        $this->createdStatusCode = Constants::createdStatusCode;
        $this->okStatusCode = Constants::okStatusCode;
        $this->internalServerErrorStatusCode = Constants::internalServerErrorStatusCode;
        $this->noContentStatusCode = Constants::noContentStatusCode;
        $this->notFoundStatusCode = Constants::notFoundStatusCode;

        $this->coverImgType = 'push_notification_message';
        $this->pushNotiMessageApiRelation = ['defaultPhoto'];

        $this->publish = Constants::publish;
        $this->show = Constants::show;
        $this->hide = Constants::hide;
        $this->delete = Constants::delete;
        $this->unDelete = Constants::unDelete;

        $this->viewAnyAbility = Constants::viewAnyAbility;
        $this->createAbility = Constants::createAbility;
        $this->editAbility = Constants::editAbility;
        $this->deleteAbility = Constants::deleteAbility;
        $this->code = Constants::pushNotificationMessage;

        $this->screenDisplayUiKeyCol = ScreenDisplayUiSetting::key;
        $this->screenDisplayUiIdCol = ScreenDisplayUiSetting::id;
        $this->screenDisplayUiIsShowCol = ScreenDisplayUiSetting::isShow;

        $this->coreFieldFilterModuleNameCol = CoreFieldFilterSetting::moduleName;

        $this->customUiIsDelCol = CustomizeUi::isDelete;
    }

    public function store($request)
    {
        DB::beginTransaction();

        try {
            $push_notification_message = new PushNotificationMessage();
            $push_notification_message->message = $request->message;
            $push_notification_message->description = $request->description;
            $push_notification_message->added_user_id = Auth::user()->id;
            $push_notification_message->save();

            $imgParentId = $push_notification_message->id;

            // save push notification photo
            $this->updateOrCreateImage($request, "cover", $imgParentId, $this->coverImgType);

            // send push noti to all user
			$data['subscribe'] = 0;
			$data['push'] = 1;
			$data['desc'] = $push_notification_message->description;
			$data['message'] = $push_notification_message->message;

			$status = send_android_fcm_topics_subscribe( $data );
            $status = send_android_fcm_topics_subscribe_fe( $data ,env('APP_URL') );

            DB::commit();
            return $push_notification_message;
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }

        return $push_notification_message;
    }

    public function updateOrCreateImage($request, $fileKey, $imgParentId, $imgType ){
        if ($request->file($fileKey)){

            $conds[$this->coreImageImgParentIdCol] = $imgParentId;
            $conds[$this->coreImageImgTypeCol] = $imgType;
            $image = $this->getImage($conds);

            $file = $request->file($fileKey);
            $data[$this->coreImageImgParentIdCol] = $imgParentId;
            $data[$this->coreImageImgTypeCol] = $imgType;

            // if image, delete existing file and update
            if(!empty($image)){
                // delete image from storage folder
                $this->imageService->deleteImage($image->img_path);
                $this->imageService->update(null,null,$file, $data, $image);
            } else {
                $this->imageService->update(null,null,$file, $data);
            }

        }
    }

    public function makeColumnHideShown($request){
        $hideShowFields = $request->value;
//        foreach ($hideShowFields as $hideShowField){
//            $ScreenDisplayUiSetting = ScreenDisplayUiSetting::find($hideShowField[$this->screenDisplayUiIdCol]);
//            $ScreenDisplayUiSetting->is_show = $hideShowField['hidden'] ? $this->hide : $this->show;
//            $ScreenDisplayUiSetting->update();
//        }
        foreach ($hideShowFields as $hideShowField){
            $hideShowFieldData[] = [
                $this->screenDisplayUiIdCol => $hideShowField['id'],
                $this->screenDisplayUiIsShowCol => $hideShowField['hidden'] ? $this->hide : $this->show,
            ];
        }
        ScreenDisplayUiSetting::upsert(
            $hideShowFieldData,
            [$this->screenDisplayUiIdCol], [$this->screenDisplayUiIsShowCol]
        );
    }

    public function getPushNotificationMessage($id = null, $relation = null){
        $push_notification_message = PushNotificationMessage::when($id, function ($q, $id){
                            $q->where($this->pushNotificationMessageIdCol, $id);
                        })
                        ->first();
        return $push_notification_message;
    }

    public function getPushNotificationMessages($relation = null, $status = null, $limit = null, $offset = null, $conds = null, $notIds=null,$noPagination = null,  $pagPerPage = null){
        $sort = '';

        if(isset($conds['order_by'])){
            $sort = $conds['order_by'];
        }
        $push_notification_messages = PushNotificationMessage::when($relation, function ($q, $relation){
                                    $q->with($relation);
                                })
                                ->when($limit, function ($query, $limit) {
                                    $query->limit($limit);
                                })
                                ->when($offset, function ($query, $offset) {
                                    $query->offset($offset);
                                })
                                ->when($conds, function ($query, $conds) {
                                    $query = $this->searching($query, $conds);
                                })
                                ->when($notIds, function ($query, $notIds) {
                                    $query->whereNotIn('id', $notIds);
                                })
                                ->when(empty($sort), function ($query, $extra) {
                                    $query->orderBy('message', 'asc');

                                });

        if ($pagPerPage){
            $push_notification_messages = $push_notification_messages->paginate($pagPerPage)->onEachSide(1)->withQueryString();

            } elseif ($noPagination){
                $push_notification_messages = $push_notification_messages->get();
            }else{
                $push_notification_messages = $push_notification_messages->get();
            }
        return $push_notification_messages;
    }
    public function searching($query, $conds){
        if (isset($conds['keyword']) && $conds['keyword']) {
            $conds['searchterm'] = $conds['keyword'];
        }
        // search term
        if (isset($conds['searchterm']) && $conds['searchterm']) {
            $search = $conds['searchterm'];
            $query->where(function ($query) use ($search) {
                $query->where($this->tableName . '.' . $this->pushNotificationMesssageMessageCol, 'like', '%' . $search . '%')
                        ->orWhere($this->tableName . '.' . $this->pushNotificationMessageDescriptionCol, 'like', '%' . $search . '%');
            });
        }

        // order by
        if (isset($conds['order_by']) && isset($conds['order_type']) && $conds['order_by'] && $conds['order_type']){

            if($conds['order_by'] == 'id'){
                $query->orderBy('categories.id', $conds['order_type']);
            }
            else{
                $query->orderBy($conds['order_by'], $conds['order_type']);
            }
        }

        return $query;
    }

    public function getImages($push_notification_message){
        $images = CoreImage::where($this->coreImageImgParentIdCol, $push_notification_message->id)->get();
        return $images;
    }

    public function getImage($conds){
        $image = $this->imageService->getImage($conds);
        return $image;
    }

    public function index($routeName,$request){
        // check permission
        $checkPermission = $this->checkPermission($this->viewAnyAbility, PushNotificationMessage::class, "admin.index");

        // search filter
        $conds['searchterm'] = $request->input('search') ?? '';

        $conds['order_by']= null;
        $conds['order_type'] = null;
        $row = $request->input('row') ?? Constants::dataTableDefaultRow;

        if($request->sort_field)
        {
            $conds['order_by'] = $request->sort_field;
            $conds['order_type'] = $request->sort_order;
        }

        $relation = ['owner', 'editor', 'cover'];
        $push_notification_messages = PushNotificationMessageWithKeyResource::collection($this->getPushNotificationMessages($relation,null, null,null, $conds,null,false, $row));

        // taking for column and columnFilterOption
        $columnAndColumnFilter = $this->takingForColumnAndFilterOption();
        $showProductCols = $columnAndColumnFilter['showCoreField'];
        $columnProps = $columnAndColumnFilter['arrForColumnProps'];
        $columnFilterOptionProps = $columnAndColumnFilter['arrForColumnFilterProps'];

        // changing item arr object with new format
        $changedObj = $push_notification_messages;
        // dd($changedObj);

        if($conds['order_by'])
        {
            $dataArr = [
                "checkPermission" => $checkPermission,
                'showCoreAndCustomFieldArr' => $columnProps,
                'hideShowFieldForFilterArr' => $columnFilterOptionProps,
                'push_notification_messages' => $changedObj,
                'sort_field' =>$conds['order_by'],
                'sort_order'=>$request->sort_order,
                'search'=>$conds['searchterm'],
            ];
        }
        else
        {
            $dataArr = [
                "checkPermission" => $checkPermission,
                'showCoreAndCustomFieldArr' => $columnProps,
                'hideShowFieldForFilterArr' => $columnFilterOptionProps,
                'push_notification_messages' => $changedObj,
                'search'=>$conds['searchterm'],
            ];
        }

        return $dataArr;
    }

    public function destroy($id){
        $push_notification_message = PushNotificationMessage::find($id);

        $checkPermission = $this->checkPermission($this->deleteAbility,$push_notification_message, "admin.index");

        $message = $push_notification_message->message;

        $images = $this->getImages($push_notification_message);

        deleteImages($images);
        $push_notification_message->delete();

        $dataArr = [
            "checkPermission" => $checkPermission,
            'title'   => $message,
        ];
        return $dataArr;
    }

    public function create($routeName){
        $checkPermission = $this->checkPermission($this->createAbility,PushNotificationMessage::class, "admin.index");
        $dataArr = [
            "checkPermission" => $checkPermission,
        ];
        return $dataArr;
    }

    public function edit($id, $routeName){
        $noti = PushNotificationMessage::find($id);
        $checkPermission = $this->checkPermission($this->editAbility,$noti, "admin.index");
        $conds = [
            'module_name' => Constants::pushNotificationMessage,
            'enable' => 1,
            'mandatory' => 1,
            'is_core_field' => 1,
        ];

        $core_headers = CoreFieldFilterSetting::where($conds)->get();

        $validation = [];

        foreach($core_headers as $core_header){
            if($core_header->field_name == 'photo'){
                array_push($validation, 'cover');
            }
        }

        $dataArr = [
            "validation" => $validation,
            "checkPermission" => $checkPermission,
            'pushNotificationMessage'   => $noti,
        ];
        return $dataArr;
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

    //for api
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

    public function allNotisFromApi($request){

        $offset = isset($request->offset)?$request->offset:null;
        $limit = isset($request->limit)?$request->limit:null;

        $notiIds = PushNotificationUser::where('user_id', $request->user_id)->onlyTrashed()->get()->pluck('noti_id');

        $pushNotiMessageApiRelation = $this->pushNotiMessageApiRelation;
        $noti_messages = $this->getPushNotificationMessages($pushNotiMessageApiRelation,null, $limit, $offset, null, $notiIds);

        return $noti_messages;
    }

    public function getByIdFromApi($request){


        $id = $request->id;

        $push_notification_message = PushNotificationMessage::where($this->pushNotificationMessageIdCol, $id)->first();

        if (!$push_notification_message){
            return  ['error' =>  __('core__api_record_not_found',[],$request->language_symbol),'status' =>  $this->noContentStatusCode ];
        } else {
            return $push_notification_message;
        }
    }


}
