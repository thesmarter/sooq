<?php
namespace Modules\Core\Http\Services;

use App\Http\Services\PsService;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\AvailableCurrency;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Entities\CoreFieldFilterSetting;
use Modules\Core\Entities\CustomizeUi;
use Modules\Core\Entities\ScreenDisplayUiSetting;
use Modules\Core\Imports\AvailableCurrencyImport;
use Modules\Core\Transformers\Backend\Model\AvailableCurrency\AvailableCurrencyWithKeyResource;
use Illuminate\Support\Facades\Gate;

class AvailableCurrencyService extends PsService
{
    protected $successStatus, $createdStatusCode, $okStatusCode, $internalServerErrorStatusCode, $noContentStatusCode, $notFoundStatusCode, $currencyIdCol, $currencySymbolCol, $currencyShortFormCol, $publish, $unPublish, $default, $unDefault, $warningFlag, $successFlag, $dangerFlag, $available_currencyIsDefaultCol, $currencyStatusCol, $show, $hide, $delete, $unDelete, $viewAnyAbility, $createAbility, $editAbility, $deleteAbility, $code, $screenDisplayUiKeyCol, $screenDisplayUiIdCol, $screenDisplayUiIsShowCol, $coreFieldFilterModuleNameCol, $customUiIsDelCol;

    public function __construct()
    {
        $this->successStatus = Constants::successStatus;
        $this->createdStatusCode = Constants::createdStatusCode;
        $this->okStatusCode = Constants::okStatusCode;
        $this->internalServerErrorStatusCode = Constants::internalServerErrorStatusCode;
        $this->noContentStatusCode = Constants::noContentStatusCode;
        $this->notFoundStatusCode = Constants::notFoundStatusCode;
        $this->currencyIdCol = AvailableCurrency::id;
        $this->currencySymbolCol = AvailableCurrency::currencySymbol;
        $this->currencyShortFormCol = AvailableCurrency::currencyShortForm;
        $this->currencyStatusCol = AvailableCurrency::status;
        $this->currencyIsDefaultCol = AvailableCurrency::isDefault;
        $this->tableName = AvailableCurrency::tableName;

        $this->publish = Constants::publish;
        $this->unPublish = Constants::unPublish;
        $this->default = Constants::default;
        $this->unDefault = Constants::unDefault;
        $this->warningFlag = Constants::warning;
        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;

        $this->show = Constants::show;
        $this->hide = Constants::hide;
        $this->delete = Constants::delete;
        $this->unDelete = Constants::unDelete;

        $this->viewAnyAbility = Constants::viewAnyAbility;
        $this->createAbility = Constants::createAbility;
        $this->editAbility = Constants::editAbility;
        $this->deleteAbility = Constants::deleteAbility;
        $this->code = Constants::availableCurrency;

        $this->screenDisplayUiKeyCol = ScreenDisplayUiSetting::key;
        $this->screenDisplayUiIdCol = ScreenDisplayUiSetting::id;
        $this->screenDisplayUiIsShowCol = ScreenDisplayUiSetting::isShow;

        $this->coreFieldFilterModuleNameCol = CoreFieldFilterSetting::moduleName;

        $this->customUiIsDelCol = CustomizeUi::isDelete;

    }

    public function create($routeName){
        // check permission start
        $checkPermission = $this->checkPermission($this->createAbility,AvailableCurrency::class, "admin.index");
        // check permission end

        $code = $this->code;

        $coreFieldFilterSettings = $this->getCoreFieldFilteredLists($code);

        $dataArr = [
            "checkPermission" => $checkPermission,
            'coreFieldFilterSettings' => $coreFieldFilterSettings
        ];

        return $dataArr;
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            $available_currency = new AvailableCurrency();
            $available_currency->currency_symbol = $request->currency_symbol;
            $available_currency->currency_short_form = $request->currency_short_form;
            $available_currency->name = $request->name;
            $available_currency->added_user_id = Auth::user()->id;

            $available_currency->save();

            DB::commit();
        } catch (\Throwable $e){
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }

        return $available_currency;
    }

    public function update($id,$request)
    {

       DB::beginTransaction();
        try {
            $currency = $this->getAvailableCurrency($id);
            if (isset($request->currency_symbol) && !empty($request->currency_symbol)) {
                $currency->currency_symbol = $request->currency_symbol;
            }

            if (isset($request->currency_short_form) && !empty($request->currency_short_form)) {
                $currency->currency_short_form = $request->currency_short_form;
            }

            if (isset($request->name) && !empty($request->name)) {
                $currency->name = $request->name;
            }

            if (isset($request->status) && !empty($request->status)) {
                $currency->status = $request->status;
            }

            if (isset($request->updated_user_id) && !empty($request->updated_user_id)) {
                $currency->updated_user_id = $request->updated_user_id;
            } else {
                $currency->updated_user_id = Auth::user()->id;
            }

            $currency->update();

            DB::commit();
            return $currency;
        }catch (\Throwable $e){
            DB::rollBack();
            return['error' => $e->getMessage()];
        }
    }

    public function getAvailableCurrencies($status = null, $isDefault = null, $limit = null, $offset = null,$noPagination = null,  $pagPerPage = null, $conds = null){
        $available_currencies = AvailableCurrency::when($status, function ($q, $status){
                                    $q->where($this->currencyStatusCol, $status);
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
                                ->orderBy($this->currencyIsDefaultCol, 'desc')
                                ->latest();
                                if ($pagPerPage){
                                    $available_currencies = $available_currencies->paginate($pagPerPage)->onEachSide(1)->withQueryString();
                                } elseif ($noPagination){
                                    $available_currencies = $available_currencies->get();
                                }else{
                                    $available_currencies = $available_currencies->get();
                                }
        return $available_currencies;
    }

    public function searching($query, $conds){
        // search term
        if (isset($conds['searchterm']) && $conds['searchterm']) {
            $search = $conds['searchterm'];
            $query->where(function ($query) use ($search) {
                $query->where($this->tableName . '.' . $this->currencySymbolCol, 'like', '%' . $search . '%')
                    ->orWhere($this->tableName . '.' . $this->currencyShortFormCol, 'like', '%' . $search . '%');
            });
        }
        if (isset($conds['added_user_id']) && $conds['added_user_id']) {
            $query->where($this->itmTableName .'.'.$this->itmAddedUserIdCol, $conds['added_user_id']);
        }


        // order by
        if (isset($conds['order_by']) && isset($conds['order_type']) && $conds['order_by'] && $conds['order_type']){

            if($conds['order_by'] == 'id'){
                $query->orderBy('available_currencies.id', $conds['order_type']);
            }
            else{
                $query->orderBy($conds['order_by'], $conds['order_type']);
            }
        }

        return $query;
    }

    public function getAvailableCurrency($id = null, $conds = null){
        $available_currency = AvailableCurrency::when($id, function ($query, $id) {
                                    $query->where($this->currencyIdCol, $id);
                                })
                                ->when($conds, function ($query, $conds) {
                                    $query->where($conds);
                                })->first();
        return $available_currency;
    }

    public function delete($id){
        $currency = $this->getAvailableCurrency($id);
        $name = $currency->currency_short_form;
        if($currency->is_default == 1){
            $msg = 'The '.$name.' row cannot be deleted because of default language.';
            return $msg;
        }
        $currency->delete();
    }

    public function getCoreFieldFilteredLists($code){
        return CoreFieldFilterSetting::where($this->coreFieldFilterModuleNameCol, $code)->get();
    }

    public function makePublishOrUnpublish($id){
        $currency = $this->getAvailableCurrency($id);
        if ($currency->is_default !== $this->default) {
            if ($currency->status == $this->publish) {
                $currency->status = $this->unPublish;
            } else {
                $currency->status = $this->publish;
            }
            $currency->updated_user_id = Auth::user()->id;
            $currency->update();
        } else {
            $msg = ' Sorry, the ' . $currency->currency_short_form . ' row cannot be able to unpublish in default currency.';
            return $msg;
        }
    }

    // ----------------
    public function index($routeName,$request){
        // check permission
        $checkPermission = $this->checkPermission($this->viewAnyAbility, AvailableCurrency::class, "admin.index");


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

        $available_currencies = AvailableCurrencyWithKeyResource::collection($this->getAvailableCurrencies(null,null, null,null,false,  $row, $conds));

        // taking for column and columnFilterOption
        $columnAndColumnFilter = $this->takingForColumnAndFilterOption();
        $showAvailableCurrencyCols = $columnAndColumnFilter['showCoreField'];
        $columnProps = $columnAndColumnFilter['arrForColumnProps'];
        $columnFilterOptionProps = $columnAndColumnFilter['arrForColumnFilterProps'];

         // changing item arr object with new format
        $changedCurrencyObj = $available_currencies;

        if($conds['order_by'])
        {
            $dataArr = [
                "checkPermission" => $checkPermission,
                'showCoreAndCustomFieldArr' => $columnProps,
                'hideShowFieldForFilterArr' => $columnFilterOptionProps,

                // 'owners' => $owners,
                'currencies' => $changedCurrencyObj,
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
                'currencies' => $changedCurrencyObj,
                'search'=>$conds['searchterm'] ,
                // 'uis'=>$uis,
            ];
        }

        return $dataArr;
    }

    public function edit($id, $routeName){


        $code = $this->code;
        $coreFieldFilterSettings = $this->getCoreFieldFilteredLists($code);

        $currency = $this->getAvailableCurrency($id);

        // check permission start
        $checkPermission = $this->checkPermission($this->editAbility, $currency, "admin.index");
        // check permission end
        $dataArr = [
            "checkPermission" => $checkPermission,
            "currency" => $currency,
            'coreFieldFilterSettings' => $coreFieldFilterSettings
        ];
        return $dataArr;
    }


    public function destroy($id){
        $currency = $this->getAvailableCurrency($id);

        // check permission start
        $checkPermission = $this->checkPermission($this->deleteAbility, $currency, "admin.index");
        // check permission end

        $name = $this->getAvailableCurrency($id)->currency_short_form;
        $msg = $this->delete($id);

        // if default currency
        if ($msg){
            $dataArr = [
                "checkPermission" => $checkPermission,
                "msg" => $msg,
                "flag" => $this->warningFlag
            ];
            return $dataArr;
        }

        $msg = 'The '.$name.' row has been deleted successfully.';
        $dataArr = [
            "checkPermission" => $checkPermission,
            "msg" => $msg,
            "flag" => $this->successFlag
        ];
        return $dataArr;
    }

    public function statusChange($id){
        $name = $this->getAvailableCurrency($id)->available_currency_short_form;

        $msg = $this->makePublishOrUnpublish($id);

        // if default available_currency
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
        $available_currency = $this->getAvailableCurrency($id);
        if($available_currency->is_default == $this->default){

            $status = [
                'msg' => ' Sorry, the '.$available_currency->currency_short_form.' row cannot be changed to default status because it is default.',
                'flag' => $this->warningFlag,
            ];

            return $status;
        }else{
            AvailableCurrency::where($this->currencyIsDefaultCol, $this->default)->update([$this->currencyIsDefaultCol => $this->unDefault]);

            $available_currency->status = $this->publish;
            $available_currency->is_default = $this->default;
            $available_currency->updated_user_id = Auth::user()->id;
        }
        $available_currency->update();

        $status = [
            'msg' => 'The '.$available_currency->currency_short_form.' row has been changed to default status successfully.',
            'flag' => $this->successFlag,
        ];
        return $status;
    }

    public function clearDefault(){
        $available_currency = AvailableCurrency::where($this->currencyIsDefaultCol, $this->default)->first();
        $available_currency->is_default = $this->unDefault;
        $available_currency->update();
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

}
