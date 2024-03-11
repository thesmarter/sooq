<?php

namespace Modules\BlueMarkUser\Http\Services;

use App\Http\Services\PsService;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\BlueMarkUser\Entities\BlueMarkUser;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\CoreFieldFilterSetting;
use Modules\Core\Entities\CustomizeUi;
use Modules\Core\Entities\CustomizeUiDetail;
use Modules\Core\Entities\UserInfo;
use Modules\Core\Http\Services\UserService;
use Modules\Core\Http\Services\UserInfoService;
use Modules\Core\Entities\Role;
use Modules\Core\Entities\ScreenDisplayUiSetting;
use Modules\Core\Entities\UserPermission;
use Modules\Core\Http\Services\RoleService;
use Carbon\Carbon;
use Modules\Core\Transformers\Backend\NoModel\BlueMarkUser\BlueMarkUserWithKeyResource;

class BlueMarkUserService extends PsService
{
    protected $blueMarkUserUserIdCol, $customUiDetailCoreKeysIdCol, $customUiUiTypeIdCol, $dangerFlag, $roleService, $userInfoIdCol, $userInfoCoreKeysIdCol, $userInfoUserIdCol, $userInfoValueCol, $publish, $unPublish, $userService, $userInfoService;

    public function __construct(RoleService $roleService, UserService $userService, UserInfoService $userInfoService)
    {
        $this->roleService = $roleService;
        $this->userInfoIdCol = UserInfo::id;
        $this->userInfoCoreKeysIdCol = UserInfo::coreKeysId;
        $this->userInfoUserIdCol = UserInfo::userId;
        $this->userInfoValueCol = UserInfo::value;

        $this->publish = Constants::publish;
        $this->unPublish = Constants::unPublish;
        $this->code = Constants::user;

        $this->userService = $userService;
        $this->userInfoService = $userInfoService;

        $this->hide = Constants::hide;
        $this->show = Constants::show;
        $this->enable = Constants::enable;
        $this->unDelete = Constants::unDelete;
        $this->dangerFlag = Constants::danger;

        $this->customUiIdCol = CustomizeUi::id;
        $this->customUiCoreKeysIdCol = CustomizeUi::coreKeysId;
        $this->customUiEnableCol = CustomizeUi::enable;
        $this->customUiIsDelCol = CustomizeUi::isDelete;
        $this->customUiMandatoryCol = CustomizeUi::mandatory;
        $this->customUiModuleName = CustomizeUi::moduleName;
        $this->customUiUiTypeIdCol = CustomizeUi::uiTypeId;

        $this->customUiDetailCoreKeysIdCol = CustomizeUiDetail::coreKeysId;

        $this->blueMarkUserUserIdCol = BlueMarkUser::userId;

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
    }

    public function index($request){

        $code = $this->code;

        $customizeUis = CustomizeUi::where($this->customUiModuleName, $code)->where($this->customUiUiTypeIdCol, 'uit00001')->latest()->get();

        $uis = [];
        $userRelations = [];

        $customizeUisByModule = CustomizeUi::where($this->customUiModuleName, $code)->latest()->get();
        $customizeDetails = CustomizeUiDetail::whereIn($this->customUiDetailCoreKeysIdCol, $customizeUisByModule->pluck($this->customUiDetailCoreKeysIdCol))->get()->groupBy($this->customUiDetailCoreKeysIdCol)->toArray();

        $date_range = null;
        if(!empty($request->date_filter) && $request->date_filter != 'all'){
            $start_date = $request->date_filter[0];
            $end_date = $request->date_filter[1];
            if(empty($end_date)){
                $end_date = Carbon::now();
            }
            $date_range = [$start_date, $end_date];
        }

        $conds['bluemark_updated_at'] = $request->input('date_filter') == 'all'? null  : $date_range;

        // $conds['bluemark_updated_at'] =$request->input('date_filter') == "all" ? null : $request->date_filter;


        foreach($customizeUis as $customizeUi){

            $uis[$customizeUi->core_keys_id] = CustomizeUiDetail::where($this->customUiDetailCoreKeysIdCol, $customizeUi->core_keys_id)->get()->toArray();
        }

        // Search and filter
        $conds['searchterm'] = $request->input('search') ?? '';
        $conds['bluemark_status'] = $request->input('status_filter') == 'all'? null  : $request->status_filter;

        if(!empty($userRelations))
        {
            $conds['user_relation'] =$userRelations;
        }
        $conds['order_by']= "name";
        $conds['order_type'] = "asc";
        $conds['is_banned'] = 0;
        $row = $request->input('row') ?? Constants::dataTableDefaultRow;

        if($request->sort_field)
        {
            $conds['order_by'] = $request->sort_field;
            $conds['order_type'] = $request->sort_order;
        }

        $userRelation =[
            'role',
            'userRelation.uiType',
            'userRelation.customizeUi',
            'userRelation'
        ];

        // echo json_encode($conds);exit;

        $users = BlueMarkUserWithKeyResource::collection($this->userInfoService->getBlueMarkUsers($conds, false, $row));

        // taking for column and columnFilterOption
        $columnAndColumnFilter = $this->takingForColumnAndFilterOption();
        $showUserCols = $columnAndColumnFilter['showCoreField'];
        $columnProps = $columnAndColumnFilter['arrForColumnProps'];
        $columnFilterOptionProps = $columnAndColumnFilter['arrForColumnFilterProps'];

        // changing user arr object with new format
        $changedUserObj = $users;
        $roles = $this->roleService->getRoles();

        $verifyBlueMarkList  = [];

        $apply = new \stdClass();
        $apply->id = 1;
        $apply->name = __('bluemarkuser__be_applied_label');
        array_push($verifyBlueMarkList, $apply);

        $pending = new \stdClass();
        $pending->id = 2;
        $pending->name = __('bluemarkuser__be_pending_label');
        array_push($verifyBlueMarkList, $pending);

        $reject = new \stdClass();
        $reject->id = 3;
        $reject->name = __('bluemarkuser__be_rejected_label');
        array_push($verifyBlueMarkList, $reject);

        if($conds['order_by'])
        {
            $dataArr = [
                'showCoreAndCustomFieldArr' => $columnProps,
                'hideShowFieldForFilterArr' => $columnFilterOptionProps,
                'roles' => $roles,
                'users' => $changedUserObj,
                'sort_field' =>$conds['order_by'],
                'sort_order'=>$request->sort_order,
                'search'=>$conds['searchterm'] ,
                'selectedStatus'=>$conds['bluemark_status'],
                'selectedDate'=>$conds['bluemark_updated_at'] ,
                'uis'=>$uis,
                'usrIsVerifyBlueMark' => Constants::usrIsVerifyBlueMark,
                'usrBlueMarkNote' => Constants::usrBlueMarkNote,
                'verifyBlueMarkList' => $verifyBlueMarkList,
                'customizeDetails' => $customizeDetails,
                'customizeHeaders' => $customizeUisByModule
            ];
        }
        else
        {
            $dataArr = [
                'showCoreAndCustomFieldArr' => $columnProps,
                'hideShowFieldForFilterArr' => $columnFilterOptionProps,
                'roles' => $roles,
                'users' => $changedUserObj,
                'search'=>$conds['searchterm'] ,
                'selectedDate'=>$conds['bluemark_updated_at'] ,
                'uis'=>$uis,
                'usrIsVerifyBlueMark' => Constants::usrIsVerifyBlueMark,
                'usrBlueMarkNote' => Constants::usrBlueMarkNote,
                'verifyBlueMarkList' => $verifyBlueMarkList,
                'selectedStatus'=>$conds['bluemark_status'],
                'customizeDetails' => $customizeDetails,
                'customizeHeaders' => $customizeUisByModule
            ];
        }
        return $dataArr;
    }

    public function saveBlueMark($request){
        DB::beginTransaction();

        try {
            $blueMarkUser = new BlueMarkUser();

            $blueMarkUser->user_id = $request->user_id;

            if (isset($request->added_user_id) && !empty($request->added_user_id))
                $blueMarkUser->added_user_id = $request->added_user_id;
            else
                $blueMarkUser->added_user_id = $request->user_id;

            $blueMarkUser->save();

            DB::commit();
            return $blueMarkUser;
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public function store($request)
    {
        DB::beginTransaction();

        try {
            $userInfo = new UserInfo();

            if (isset($request->core_keys_id) && !empty($request->core_keys_id))
                $userInfo->core_keys_id = $request->core_keys_id;

            if (isset($request->user_id) && !empty($request->user_id))
                $userInfo->user_id = $request->user_id;

            if (isset($request->value) && !empty($request->value))
                $userInfo->value = $request->value;

            if (isset($request->ui_type_id) && !empty($request->ui_type_id)) {
                $userInfo->ui_type_id = $request->ui_type_id;
            }
            if (isset($request->added_user_id) && !empty($request->added_user_id))
                $userInfo->added_user_id = $request->added_user_id;
            else
                $userInfo->added_user_id = Auth::user()->id;

            $userInfo->save();

            DB::commit();
            return $userInfo;
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public function update($id, $request)
    {
        DB::beginTransaction();

        try {
            $userInfo = $this->getUserInfo($id);

            if (isset($request->core_keys_id) && !empty($request->core_keys_id))
                $userInfo->core_keys_id = $request->core_keys_id;

            if (isset($request->user_id) && !empty($request->user_id))
                $userInfo->user_id = $request->user_id;

            if (isset($request->value) && !empty($request->value))
                $userInfo->value = $request->value;

            if (isset($request->ui_type_id) && !empty($request->ui_type_id)) {
                $userInfo->ui_type_id = $request->ui_type_id;
            }

            if (isset($request->updated_user_id) && !empty($request->updated_user_id))
                $userInfo->updated_user_id = $request->updated_user_id;
            else
                $userInfo->updated_user_id = Auth::user()->id;

            $userInfo->update();

            DB::commit();
            return $userInfo;
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }


    public function edit($id, $routeName)
    {
        $dataWithRelation = ["userRelation"];
        $user = $this->userService->getUser($id, null, $dataWithRelation);
        $code = Constants::user;
        $blueMarkNote = '';
        $blueMarkStatus = 1;
        $blueMarkStatusId = '';
        foreach($user->userRelation as $relation){
            if($relation->core_keys_id == Constants::usrBlueMarkNote){
                $blueMarkNote = $relation->value;
            }
            if($relation->core_keys_id == Constants::usrIsVerifyBlueMark){
                $blueMarkStatus= $relation->value;
                $blueMarkStatusId= $relation->id;
            }
        }
        $coreFieldFilterSettings = $this->userService->getCoreFieldFilteredLists($code);
        $customizeHeader = $this->userService->getCustomizeFields($code,null,null,0);
        $customizeDetail = $this->userService->getCustomizeFieldAttrs();
        $bluemarkStatusList = [
            [
                'id' => 1,
                'label' => __('bluemarkuser__be_applied_label')
            ],
            [
                'id' => 2,
                'label' => __('bluemarkuser__be_pending_label')
            ],
            [
                'id' => 3,
                'label' => __('bluemarkuser__be_rejected_label')
            ]
        ];

        $dataArr = [
            'user'   => $user,
            "customizeHeaders" => $customizeHeader,
            "customizeDetails" => $customizeDetail,
            'coreFieldFilterSettings' => $coreFieldFilterSettings,
            'bluemarkStatusList' => $bluemarkStatusList,
            'blueMarkNote' => $blueMarkNote,
            'blueMarkStatus' => $blueMarkStatus,
            'blueMarkStatusId' => $blueMarkStatusId
        ];

        return $dataArr;
    }

    public function deleteAllBy($conds = null)
    {
        DB::beginTransaction();
        try {
            BlueMarkUser::when($conds, function ($query, $conds) {
                $query->where($conds);

            })->delete();

            DB::commit();
            return true;
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public function getUserInfos($relations = null, $limit = null, $offset = null, $conds = null)
    {
        $userInfos = UserInfo::when($relations, function ($query, $relations) {
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
        return $userInfos;
    }

    public function getUserInfo($id, $relations = null, $conds = null)
    {
        $userInfo = UserInfo::where($this->userInfoIdCol, $id)
            ->when($relations, function ($query, $relations) {
                $query->with($relations);
            })
            ->when($conds, function ($query, $conds) {
                $query->where($conds);
            })
            ->first();
        return $userInfo;
    }

    public function takingForColumnAndFilterOption($flag = null)
    {

        // for table
        $hideShowCoreFieldForColumnArr = [];
        $hideShowCustomFieldForColumnArr = [];

        $showUserCols = [];

        // for eye
        $hideShowFieldForColumnFilterArr = [];

        // for control
        $controlFieldArr = [];
        if(isset($flag) && !empty($flag) && $flag == 'banned_user'){
            $controlFieldObj = $this->takingForColumnProps(__('core__be_view_lable'), "detail", "Action", false, 0);
            array_push($controlFieldArr, $controlFieldObj);
        }else{
            $controlFieldObj = $this->takingForColumnProps(__('core__be_action_label'), "action", "Action", false, 0);
            array_push($controlFieldArr, $controlFieldObj);
        }

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

    public function makeColumnHideShown($request){
        $hideShowFields = $request->value;
//        foreach ($hideShowFields as $hideShowField){
//            $ScreenDisplayUiSetting = ScreenDisplayUiSetting::find($hideShowField[$this->screenDisplayUiIdCol]);
//            $ScreenDisplayUiSetting->is_show = $hideShowField['hidden'] ? $this->hide : $this->show;
//            $ScreenDisplayUiSetting->update();
//        }
        foreach ($hideShowFields as $hideShowField){
            $hideShowFieldData[] = [
                'id' => $hideShowField['id'],
                'is_show' => $hideShowField['hidden'] ? $this->hide : $this->show,
            ];
        }
        ScreenDisplayUiSetting::upsert(
            $hideShowFieldData,
            ['id'], ['is_show']
        );
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


    public function destroy($id)
    {
        //delete blue mark user status
        $conds[$this->userInfoUserIdCol] = $id;
        $conds[$this->userInfoCoreKeysIdCol] = 'ps-usr00002';
        $user = UserInfo::where($conds)->delete();

        //delete blue mark user note
        $conds[$this->userInfoUserIdCol] = $id;
        $conds[$this->userInfoCoreKeysIdCol] = 'ps-usr00003';
        $user = UserInfo::where($conds)->delete();

        // delete from blue_mark_users
        $blueMarkUser = BlueMarkUser::where($this->blueMarkUserUserIdCol, $id)->delete();

        $user = User::find($id);
        $name = $user->name;

        $dataArr = [
            'msg' => __('core__be_delete_success', ['attribute' => $name]),
            'flag' => $this->dangerFlag,
        ];

        return $dataArr;
    }

}
