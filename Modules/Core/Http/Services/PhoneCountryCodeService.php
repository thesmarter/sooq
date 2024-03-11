<?php
namespace Modules\Core\Http\Services;

use App\Http\Services\PsService;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\PhoneCountryCode;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Entities\CoreFieldFilterSetting;
use Modules\Core\Entities\CustomizeUi;
use Modules\Core\Entities\ScreenDisplayUiSetting;
use Modules\Core\Imports\CategoryImport;
use Modules\Core\Transformers\Backend\Model\PhoneCountryCode\PhoneCountryCodeWithKeyResource;
use Illuminate\Support\Facades\Gate;

class PhoneCountryCodeService extends PsService
{
    protected $successStatus, $createdStatusCode, $okStatusCode, $internalServerErrorStatusCode, $noContentStatusCode, $notFoundStatusCode, $phoneCountryCodeIdCol, $countryNameCol, $countryCodeCol, $publish, $unPublish, $default, $unDefault, $warningFlag, $successFlag, $dangerFlag, $phoneCountryCodeIsDefaultCol, $phoneCountryCodeStatusCol, $show, $hide, $delete, $unDelete, $viewAnyAbility, $createAbility, $editAbility, $deleteAbility, $code, $screenDisplayUiKeyCol, $screenDisplayUiIdCol, $screenDisplayUiIsShowCol, $coreFieldFilterModuleNameCol, $customUiIsDelCol;
    public function __construct()
    {
        $this->successStatus = Constants::successStatus;
        $this->createdStatusCode = Constants::createdStatusCode;
        $this->okStatusCode = Constants::okStatusCode;
        $this->internalServerErrorStatusCode = Constants::internalServerErrorStatusCode;
        $this->noContentStatusCode = Constants::noContentStatusCode;
        $this->notFoundStatusCode = Constants::notFoundStatusCode;
        $this->tableName = PhoneCountryCode::tableName;
        $this->phoneCountryCodeIdCol = PhoneCountryCode::id;
        $this->countryNameCol = PhoneCountryCode::countryName;
        $this->countryCodeCol = PhoneCountryCode::countryCode;
        $this->publish = Constants::publish;
        $this->unPublish = Constants::unPublish;
        $this->default = Constants::default;
        $this->unDefault = Constants::unDefault;
        $this->warningFlag = Constants::warning;
        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;

        $this->phoneCountryCodeIsDefaultCol = PhoneCountryCode::isDefault;
        $this->phoneCountryCodeStatusCol = PhoneCountryCode::status;

        $this->show = Constants::show;
        $this->hide = Constants::hide;
        $this->delete = Constants::delete;
        $this->unDelete = Constants::unDelete;

        $this->viewAnyAbility = Constants::viewAnyAbility;
        $this->createAbility = Constants::createAbility;
        $this->editAbility = Constants::editAbility;
        $this->deleteAbility = Constants::deleteAbility;
        $this->code = Constants::phoneCountryCode;

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
            $phone_country_code = new PhoneCountryCode();
            $phone_country_code->country_code = $request->country_code;
            $phone_country_code->country_name = $request->country_name;
            $phone_country_code->added_user_id = Auth::user()->id;

            $phone_country_code->save();

            DB::commit();
        } catch (\Throwable $e){
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }

        return $phone_country_code;
    }

    public function create($routeName){
        // check permission start
        $checkPermission = $this->checkPermission($this->createAbility,PhoneCountryCode::class, "admin.index");
        // check permission end

        $code = $this->code;

        $coreFieldFilterSettings = $this->getCoreFieldFilteredLists($code);

        $dataArr = [
            "checkPermission" => $checkPermission,
            'coreFieldFilterSettings' => $coreFieldFilterSettings
        ];

        return $dataArr;
    }

    public function getCoreFieldFilteredLists($code){
        return CoreFieldFilterSetting::where($this->coreFieldFilterModuleNameCol, $code)->get();
    }

    public function update($id,$request)
    {

       DB::beginTransaction();
        try {
            $phoneCountryCode = $this->getPhoneCountryCode($id);
            $phoneCountryCode->country_code = $request->country_code;
            $phoneCountryCode->country_name = $request->country_name;
            $phoneCountryCode->updated_user_id = Auth::user()->id;
            $phoneCountryCode->update();

            DB::commit();

        }catch (\Throwable $e){
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }

        return $phoneCountryCode;
    }


    public function getCountryCodes($status = null, $isDefault = null, $limit = null, $offset = null,$noPagination = null,  $pagPerPage = null, $conds = null){

        $sort = '';

        if(isset($conds['order_by'])){
            $sort = $conds['order_by'];
        }
        $phoneCountryCodes = PhoneCountryCode::when($status, function ($q, $status){
                                    $q->where($this->phoneCountryCodeStatusCol, $status);
                                })
                                // ->when($isDefault, function ($q, $isDefault){
                                //     $q->where($this->phoneCountryCodeIsDefaultCol, $isDefault);
                                // })
                                ->when($limit, function ($query, $limit) {
                                    $query->limit($limit);
                                })
                                ->when($conds, function ($query, $conds) {
                                    $query = $this->searching($query, $conds);
                                })
                                ->when($offset, function ($query, $offset) {
                                    $query->offset($offset);
                                })
                                ->when(empty($sort), function ($query, $extra) {
                                    $query->orderBy('is_default', 'desc')
                                    ->orderBy('status','desc')
                                    ->orderBy('country_name','asc');

                                });
                                if ($pagPerPage){
                                $phoneCountryCodes = $phoneCountryCodes->paginate($pagPerPage)->onEachSide(1)->withQueryString();

                                } elseif ($noPagination){
                                    $phoneCountryCodes = $phoneCountryCodes->get();
                                }else{
                                    $phoneCountryCodes = $phoneCountryCodes->get();
                                }
                                return $phoneCountryCodes;
    }

    public function searching($query, $conds){
        if (isset($conds['keyword']) && $conds['keyword']) {
            $conds['searchterm'] = $conds['keyword'];
        }
        // search term
        if (isset($conds['searchterm']) && $conds['searchterm']) {
            $search = $conds['searchterm'];
            $query->where(function ($query) use ($search) {
                $query->where($this->tableName . '.' . $this->countryNameCol, 'like', '%' . $search . '%')
                    ->orWhere($this->tableName . '.' . $this->countryCodeCol, 'like', '%' . $search . '%');
            });
        }
        if (isset($conds['added_user_id']) && $conds['added_user_id']) {
            $query->where($this->itmTableName .'.'.$this->itmAddedUserIdCol, $conds['added_user_id']);
        }


        // order by
        if (isset($conds['order_by']) && isset($conds['order_type']) && $conds['order_by'] && $conds['order_type']){

            if($conds['order_by'] == 'id'){
                $query->orderBy('phoneCountryCodes.id', $conds['order_type']);
            }else if($conds['order_by'] == 'country_name'){
                $query->orderBy($this->tableName . '.' . $this->countryNameCol, $conds['order_type']);
            }
            else if($conds['order_by'] == 'country_code'){
                $query->orderBy($this->tableName . '.' . $this->countryCodeCol, $conds['order_type']);
            }
            else{
                $query->orderBy($conds['order_by'], $conds['order_type']);
            }
        }

        return $query;
    }

    public function getPhoneCountryCode($id){
        $phoneCountryCode = PhoneCountryCode::findOrFail($id);
        return $phoneCountryCode;
    }

    public function delete($id){
        $phoneCountryCode = $this->getPhoneCountryCode($id);
        $name = $phoneCountryCode->country_name;
        if($phoneCountryCode->is_default == 1){
            // $msg = 'The '.$name.' row cannot be deleted because of default code.';
            $msg = __('core__be_ph_not_delete',['attribute'=>$name]);
            return $msg;
        }
        $phoneCountryCode->delete();
    }

    public function makePublishOrUnpublish($id){
        // dd("here");
        $phoneCountryCode = $this->getPhoneCountryCode($id);
        if ($phoneCountryCode->is_default !== $this->default){
            if($phoneCountryCode->status == $this->publish){
                $phoneCountryCode->status = $this->unPublish;
            }else{
                $phoneCountryCode->status = $this->publish;
            }
            $phoneCountryCode->updated_user_id = Auth::user()->id;
            $phoneCountryCode->update();
        } else {

            // $msg = ' Sorry, the '.$phoneCountryCode->country_name.' row cannot be able to unpublish in default phoneCountryCode.';
            $msg = __('core__be_undefault_currency',['attribute'=>$phoneCountryCode->country_name]);
            return $msg;
        }

    }

    // ----------------
    public function index($routeName,$request){
        // check permission
        $checkPermission = $this->checkPermission($this->viewAnyAbility, PhoneCountryCode::class, "admin.index");

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

        // if($conds['searchterm'] != ''){
            // echo $conds['searchterm'];exit;
        // }

        $phoneCountryCodes = PhoneCountryCodeWithKeyResource::collection($this->getCountryCodes(null,null, null,null,false,  $row, $conds));

        // echo json_encode($phoneCountryCodes);exit;

        // taking for column and columnFilterOption
        $columnAndColumnFilter = $this->takingForColumnAndFilterOption();
        $showProductCols = $columnAndColumnFilter['showCoreField'];
        $columnProps = $columnAndColumnFilter['arrForColumnProps'];
        $columnFilterOptionProps = $columnAndColumnFilter['arrForColumnFilterProps'];

        // changing item arr object with new format
        $changedPhoneCountryCodeObj = $phoneCountryCodes;
        // $owners = $this->userService->getUsers(null, $this->publish, $this->unBan);

        if($conds['order_by'])
        {
            $dataArr = [
                "checkPermission" => $checkPermission,
                'showCoreAndCustomFieldArr' => $columnProps,
                'hideShowFieldForFilterArr' => $columnFilterOptionProps,

                // 'owners' => $owners,
                'phoneCountryCodes' => $changedPhoneCountryCodeObj,
                'sort_field' =>$conds['order_by'],
                'sort_order'=>$request->sort_order,
                'search'=>$conds['searchterm'] ,
                // 'uis'=>$uis,
            ];
        }
        else
        {
            $dataArr = [
                "checkPermission" => $checkPermission,
                'showCoreAndCustomFieldArr' => $columnProps,
                'hideShowFieldForFilterArr' => $columnFilterOptionProps,

                // 'owners' => $owners,
                'phoneCountryCodes' => $changedPhoneCountryCodeObj,
                'search'=>$conds['searchterm'] ,
                // 'uis'=>$uis,
            ];
        }


        return $dataArr;

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

    public function edit($id){
        $checkPermission = $this->checkPermission($this->editAbility, PhoneCountryCode::class, "admin.index");

        $code = $this->code;
        $coreFieldFilterSettings = $this->getCoreFieldFilteredLists($code);

        $phoneCountryCode = $this->getPhoneCountryCode($id);
        $dataArr = [
            'checkPermission' => $checkPermission,
            "phone_country_code" => $phoneCountryCode,
            'coreFieldFilterSettings' => $coreFieldFilterSettings
        ];
        return $dataArr;
    }

    public function destroy($id){
        $checkPermission = $this->checkPermission($this->deleteAbility, PhoneCountryCode::class, "admin.index");
        $name = $this->getPhoneCountryCode($id)->country_name;
        $msg = $this->delete($id);

        // if default phoneCountryCode
        if ($msg){
            $dataArr = [
                'checkPermission' => $checkPermission,
                "msg" => $msg,
                "flag" => $this->warningFlag
            ];
            return $dataArr;
        }

        // $msg = 'The '.$name.' row has been deleted successfully.';
        $msg = __('core__be_delete_success',['attribute'=>$name]);
        
        $dataArr = [
            'checkPermission' => $checkPermission,
            "msg" => $msg,
            "flag" => $this->successFlag
        ];
        return $dataArr;
    }

    public function statusChange($id){
        $name = $this->getPhoneCountryCode($id)->country_name;

        $msg = $this->makePublishOrUnpublish($id);

        // if default phoneCountryCode
        if ($msg){
            $dataArr = [
                "msg" => $msg,
                "flag" => $this->warningFlag
            ];
            return $dataArr;
        }

        $msg = 'The status of '.$name.' row has been updated successfully.';
        $dataArr = [
            "msg" => $msg,
            "flag" => $this->successFlag
        ];
        return $dataArr;
    }

    public function defaultChange($id){
        $phoneCountryCode = $this->getPhoneCountryCode($id);
        if($phoneCountryCode->is_default == $this->default){

            $status = [
                'msg' => ' Sorry, the '.$phoneCountryCode->country_name.' row cannot be changed to default status because it is default.',
                'flag' => $this->warningFlag,
            ];

            return $status;
        }else{
            PhoneCountryCode::where($this->phoneCountryCodeIsDefaultCol, $this->default)->update([$this->phoneCountryCodeIsDefaultCol => $this->unDefault]);

            $phoneCountryCode->status = $this->publish;
            $phoneCountryCode->is_default = $this->default;
            $phoneCountryCode->updated_user_id = Auth::user()->id;
        }
        $phoneCountryCode->update();

        $status = [
            'msg' => 'The '.$phoneCountryCode->country_name.' row has been changed to default status successfully.',
            'flag' => $this->successFlag,
        ];
        return $status;
    }

    public function import($request){
        try {
            Excel::import(new CategoryImport(), $request->file('file')->store('temp'));
            $status = [
                'msg' => "Category is imported successfully",
                'flag' => $this->successFlag,
            ];
            return $status;
        } catch (\Throwable $e) {
            $status = [
                'msg' => $e,
                'flag' => $this->danger,
            ];
            return $status;
        }
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

    // for api
    public function noDataError($offset, $data){
        if ($offset > 0 && $data->isEmpty()) {
            // no paginate data
            $data = [];
            return responseDataApi($data);

        } else if($data->isEmpty()) {
            // no data db
            return responseMsgApi(__('core__api_no_data'), $this->noContentStatusCode, $this->successStatus);
        }
    }

    public function indexFromApi($request){
        $offset = $request->offset;
        $limit = $request->limit;

        $conds['searchterm'] = $request->keyword;
        $conds['order_by'] = $request->order_by;
        $conds['order_type']= $request->order_type;

        //$phoneCountryCodeApiRelation = $this->phoneCountryCodeApiRelation;
        $phoneCountryCodes = $this->getCountryCodes( $this->publish, $this->default, $limit, $offset,false,null,$conds);


        return $phoneCountryCodes;

    }

}
