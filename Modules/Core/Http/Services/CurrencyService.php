<?php
namespace Modules\Core\Http\Services;

use App\Http\Services\PsService;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\Currency;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Entities\CoreFieldFilterSetting;
use Modules\Core\Entities\CustomizeUi;
use Modules\Core\Entities\ScreenDisplayUiSetting;
use Modules\Core\Imports\CategoryImport;
use Modules\Core\Transformers\Backend\Model\Currency\CurrencyWithKeyResource;

class CurrencyService extends PsService
{
    protected $dropDownUi, $radioUi, $danger, $currencyAddedUserIdCol, $tableName, $successStatus, $createdStatusCode, $okStatusCode, $internalServerErrorStatusCode, $noContentStatusCode, $notFoundStatusCode, $currencyIdCol, $currencySymbolCol, $currencyShortFormCol, $publish, $unPublish, $default, $unDefault, $warningFlag, $successFlag, $dangerFlag, $currencyIsDefaultCol, $currencyStatusCol, $show, $hide, $delete, $unDelete, $viewAnyAbility, $createAbility, $editAbility, $deleteAbility, $code, $screenDisplayUiKeyCol, $screenDisplayUiIdCol, $screenDisplayUiIsShowCol, $coreFieldFilterModuleNameCol, $customUiIsDelCol;
    public function __construct()
    {
        $this->danger = Constants::danger;
        $this->dropDownUi = Constants::dropDownUi;
        $this->radioUi = Constants::radioUi;

        $this->successStatus = Constants::successStatus;
        $this->createdStatusCode = Constants::createdStatusCode;
        $this->okStatusCode = Constants::okStatusCode;
        $this->internalServerErrorStatusCode = Constants::internalServerErrorStatusCode;
        $this->noContentStatusCode = Constants::noContentStatusCode;
        $this->notFoundStatusCode = Constants::notFoundStatusCode;
        $this->tableName = Currency::tableName;
        $this->currencyIdCol = Currency::id;
        $this->currencySymbolCol = Currency::currencySymbol;
        $this->currencyShortFormCol = Currency::currencyShortForm;
        $this->publish = Constants::publish;
        $this->unPublish = Constants::unPublish;
        $this->default = Constants::default;
        $this->unDefault = Constants::unDefault;
        $this->warningFlag = Constants::warning;
        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;

        $this->currencyIsDefaultCol = Currency::isDefault;
        $this->currencyStatusCol = Currency::status;
        $this->currencyAddedUserIdCol = Currency::addedUserId;

        $this->show = Constants::show;
        $this->hide = Constants::hide;
        $this->delete = Constants::delete;
        $this->unDelete = Constants::unDelete;

        $this->viewAnyAbility = Constants::viewAnyAbility;
        $this->createAbility = Constants::createAbility;
        $this->editAbility = Constants::editAbility;
        $this->deleteAbility = Constants::deleteAbility;
        $this->code = Constants::currency;

        $this->screenDisplayUiKeyCol = ScreenDisplayUiSetting::key;
        $this->screenDisplayUiIdCol = ScreenDisplayUiSetting::id;
        $this->screenDisplayUiIsShowCol = ScreenDisplayUiSetting::isShow;

        $this->coreFieldFilterModuleNameCol = CoreFieldFilterSetting::moduleName;

        $this->customUiIsDelCol = CustomizeUi::isDelete;
    }

    public function createShow(){
        $checkPermission = $this->checkPermission($this->createAbility, Currency::class, "admin.index");

        $dataArr = [
            'checkPermission' => $checkPermission,
        ];
        return $dataArr;
    }

    public function create($request)
    {
        DB::beginTransaction();
        try {
            if($request->is_default == $this->default){
                DB::table($this->tableName)->update([$this->currencyIsDefaultCol => $this->unDefault]);
            }

            $currency = new Currency();
            $currency->currency_symbol = $request->currency_symbol;
            $currency->currency_short_form = $request->currency_short_form;
            $currency->is_default = $request->is_default;
            $currency->added_user_id = Auth::user()->id;
            $currency->save();

            DB::commit();

        } catch (\Throwable $e){
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
       return $currency;
    }

    public function update($id,$request)
    {

       DB::beginTransaction();
        try {
            $currency = $this->getCurrency($id);
            $currency->currency_symbol = $request->currency_symbol;
            $currency->currency_short_form = $request->currency_short_form;
            $currency->updated_user_id = Auth::user()->id;
            $currency->update();

            DB::commit();

        }catch (\Throwable $e){
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }

        return $currency;
    }

    public function getCurrencies($status = null, $isDefault = null, $limit = null, $offset = null,$noPagination = null,  $pagPerPage = null, $conds = null){
        $sort = '';
        if(isset($conds['order_by'])){
            $sort = $conds['order_by'];
        }
        $currencies = Currency::when($status, function ($q, $status){
                                    $q->where($this->currencyStatusCol, $status);
                                })
                                // ->when($isDefault, function ($q, $isDefault){
                                //     $q->where($this->currencyIsDefaultCol, $isDefault);
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
                                ->when(empty($sort), function ($query, $conds){
                                    $query->orderBy($this->currencyIsDefaultCol, 'desc')
                                        ->orderBy("added_date", 'desc')
                                        ->orderBy($this->currencyStatusCol, 'desc')
                                        ->orderBy($this->currencyShortFormCol, 'asc');
                                });
                                if ($pagPerPage){
                                $currencies = $currencies->paginate($pagPerPage)->onEachSide(1)->withQueryString();

                                } elseif ($noPagination){
                                    $currencies = $currencies->get();
                                }else{
                                    $currencies = $currencies->get();
                                }
                                return $currencies;
    }

    public function searching($query, $conds){
        if (isset($conds['keyword']) && $conds['keyword']) {
            $conds['searchterm'] = $conds['keyword'];
        }
        // search term
        if (isset($conds['searchterm']) && $conds['searchterm']) {
            $search = $conds['searchterm'];
            $query->where(function ($query) use ($search) {
                $query->where($this->tableName . '.' . $this->currencySymbolCol, 'like', '%' . $search . '%')
                    ->orWhere($this->tableName . '.' . $this->currencyShortFormCol, 'like', '%' . $search . '%');
            });
        }
        if (isset($conds['added_user_id']) && $conds['added_user_id']) {
            $query->where($this->tableName .'.'.$this->currencyAddedUserIdCol, $conds['added_user_id']);
        }

        // order by
        if (isset($conds['order_by']) && isset($conds['order_type']) && $conds['order_by'] && $conds['order_type']){

            if($conds['order_by'] == 'id'){
                $query->orderBy($this->tableName.'.'.$this->currencyIdCol, $conds['order_type']);
            }
            else{
                $query->orderBy($conds['order_by'], $conds['order_type']);
            }
        }

        return $query;
    }

    public function getCurrency($id){
        $currency = Currency::findOrFail($id);
        return $currency;
    }

    public function delete($id){
        $currency = $this->getCurrency($id);
        $name = $currency->currency_short_form;
        if($currency->is_default == 1){
            $msg = 'The '.$name.' row cannot be deleted because of default language.';
            return $msg;
        }
        $currency->delete();
    }

    public function makePublishOrUnpublish($id){
        $currency = $this->getCurrency($id);
        if ($currency->is_default !== $this->default){
            if($currency->status == $this->publish){
                $currency->status = $this->unPublish;
            }else{
                $currency->status = $this->publish;
            }
            $currency->updated_user_id = Auth::user()->id;
            $currency->update();
        } else {
            // $msg = ' Sorry, the '.$currency->currency_short_form.' row cannot be able to unpublish in default currency.';
            $msg = __('core__be_undefault_currency',['attribute'=>$currency->currency_short_form]);
            return $msg;
        }

    }

    // ----------------
    public function index($routeName,$request){
        // check permission
        $checkPermission = $this->checkPermission($this->viewAnyAbility, Currency::class, "admin.index");

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

        $currencies = CurrencyWithKeyResource::collection($this->getCurrencies(null,null, null,null,false,  $row, $conds));

        // taking for column and columnFilterOption
        $columnAndColumnFilter = $this->takingForColumnAndFilterOption();
        $showProductCols = $columnAndColumnFilter['showCoreField'];
        $columnProps = $columnAndColumnFilter['arrForColumnProps'];
        $columnFilterOptionProps = $columnAndColumnFilter['arrForColumnFilterProps'];

        // changing item arr object with new format
        $changedCurrencyObj = $currencies;

        if($conds['order_by'])
        {
            $dataArr = [
                "checkPermission" => $checkPermission,
                'showCoreAndCustomFieldArr' => $columnProps,
                'hideShowFieldForFilterArr' => $columnFilterOptionProps,
                'currencies' => $changedCurrencyObj,
                'sort_field' =>$conds['order_by'],
                'sort_order'=>$request->sort_order,
                'search'=>$conds['searchterm'] ,
            ];
        }
        else
        {
            $dataArr = [
                "checkPermission" => $checkPermission,
                'showCoreAndCustomFieldArr' => $columnProps,
                'hideShowFieldForFilterArr' => $columnFilterOptionProps,
                'currencies' => $changedCurrencyObj,
                'search'=>$conds['searchterm'] ,
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
                $this->screenDisplayUiIdCol => $hideShowField['id'],
                $this->screenDisplayUiIsShowCol => $hideShowField['hidden'] ? $this->hide : $this->show,
            ];
        }
        ScreenDisplayUiSetting::upsert(
            $hideShowFieldData,
            [$this->screenDisplayUiIdCol], [$this->screenDisplayUiIsShowCol]
        );
    }

    public function edit($id){

        $currency = $this->getCurrency($id);

        $checkPermission = $this->checkPermission($this->editAbility, $currency, "admin.index");


        $dataArr = [
            'checkPermission' => $checkPermission,
            "currency" => $currency
        ];
        return $dataArr;
    }

    public function destroy($id){
        $currency = $this->getCurrency($id);

        $checkPermission = $this->checkPermission($this->deleteAbility, $currency, "admin.index");
        $name = $this->getCurrency($id)->currency_short_form;
        $msg = $this->delete($id);

        // if default currency
        if ($msg){
            $dataArr = [
                'checkPermission' => $checkPermission,
                "msg" => $msg,
                "flag" => $this->warningFlag
            ];
            return $dataArr;
        }

        $msg =  __('core__be_delete_success', ['attribute' => $name]);
        $dataArr = [
            'checkPermission' => $checkPermission,
            "msg" => $msg,
            "flag" => $this->successFlag
        ];
        return $dataArr;
    }

    public function statusChange($id){
        $name = $this->getCurrency($id)->currency_short_form;

        $msg = $this->makePublishOrUnpublish($id);

        // if default currency
        if ($msg){
            $dataArr = [
                "msg" => $msg,
                "flag" => $this->warningFlag
            ];
            return $dataArr;
        }

        // $msg = 'The status of '.$name.' row has been updated successfully.';
        $msg = __('core__be_default_currency',['attribute'=>$name]);
        $dataArr = [
            "msg" => $msg,
            "flag" => $this->successFlag
        ];
        return $dataArr;
    }

    public function defaultChange($id){
        $currency = $this->getCurrency($id);
        if($currency->is_default == $this->default){

            $status = [
                'msg' => ' Sorry, the '.$currency->currency_short_form.' row cannot be changed to default status because it is default.',
                'flag' => $this->warningFlag,
            ];

            return $status;
        }else{
            Currency::where($this->currencyIsDefaultCol, $this->default)->update([$this->currencyIsDefaultCol => $this->unDefault]);

            $currency->status = $this->publish;
            $currency->is_default = $this->default;
            $currency->updated_user_id = Auth::user()->id;
        }
        $currency->update();

        $status = [
            // 'msg' => 'The '.$currency->currency_short_form.' row has been changed to default status successfully.',
            'msg' =>  __('core__be_default_currency',['attribute'=>$currency->currency_short_form]),
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

            $customFieldObjForColumnsProps = $this->takingForColumnProps($label, $field, $type,$isShowSorting, $ordering);
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
            return responseMsgApi(__('currency__api_no_data'), $this->noContentStatusCode, $this->successStatus);
        }
    }

    public function indexFromApi($request){
        $offset = $request->offset;
        $limit = $request->limit;
        $currencies = $this->getCurrencies( $this->publish, $this->default, $limit, $offset);
        return $currencies;
    }

}
