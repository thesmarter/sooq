<?php
namespace Modules\Core\Http\Services;

use App\Config\ps_constant;
use App\Http\Services\PsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\Category;
use Modules\Core\Entities\CoreImage;
use Modules\Core\Entities\Subcategory;
use Modules\Core\Http\Services\ImageService;
use Modules\Core\Transformers\Backend\Model\Subcategory\SubcategoryWithKeyResource;
use Modules\Core\Entities\ScreenDisplayUiSetting;
use Modules\Core\Entities\CoreFieldFilterSetting;
use Modules\Core\Entities\CustomizeUi;

class SubcategoryService extends PsService
{
    protected $subCategoryType, $notFromHomePageSearch, $searchHistoryService, $dropDownUi, $radioUi, $coreFieldFilterModuleNameCol, $screenDisplayUiKeyCol, $screenDisplayUiIdCol, $screenDisplayUiIsShowCol, $customUiIsDelCol, $code, $show, $hide, $enable, $disable, $delete, $unDelete, $iconImgType, $csvFileName, $coverImgType, $coreImageImgParentIdCol, $coreImageImgTypeCol, $subCategoryApiRelation, $successFlag, $dangerFlag, $publish, $unPublish,$subcattableName, $CategoryIdCol, $subCatNameCol, $subCatIdCol, $subCatStatusCol, $categoryService, $imageService, $forbiddenStatusCode, $notFoundStatusCode, $internalServerErrorStatusCode, $okStatusCode, $createdStatusCode, $deviceTokenParaApi, $userAccessApiTokenService, $loginUserIdParaApi, $deleteAbility, $editAbility, $createAbility, $viewAnyAbility, $noContentStatusCode, $successStatus;

    public function __construct(UserAccessApiTokenService $userAccessApiTokenService, ImageService $imageService, CategoryService $categoryService, SearchHistoryService $searchHistoryService)
    {
        $this->dropDownUi = Constants::dropDownUi;
        $this->radioUi = Constants::radioUi;

        $this->searchHistoryService = $searchHistoryService;

        $this->notFromHomePageSearch = Constants::notFromHomePageSearch;
        $this->subCategoryType = Constants::subCategoryType;

        $this->userAccessApiTokenService = $userAccessApiTokenService;
        $this->successStatus = Constants::successStatus;
        $this->createdStatusCode = Constants::createdStatusCode;
        $this->okStatusCode = Constants::okStatusCode;
        $this->internalServerErrorStatusCode = Constants::internalServerErrorStatusCode;
        $this->noContentStatusCode = Constants::noContentStatusCode;
        $this->notFoundStatusCode = Constants::notFoundStatusCode;
        $this->forbiddenStatusCode = Constants::forbiddenStatusCode;
        $this->imageService = $imageService;
        $this->categoryService = $categoryService;
        $this->subCatStatusCol = Subcategory::status;
        $this->subCatIdCol = Subcategory::id;
        $this->subCatNameCol = Subcategory::name;
        $this->CategoryIdCol = Subcategory::CategoryId;
        $this->subcattableName = Subcategory::tableName;
        $this->publish = Constants::publish;
        $this->unPublish = Constants::unPublish;
        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;
        $this->subCategoryApiRelation = ['defaultPhoto', 'defaultIcon'];
        $this->coreImageImgParentIdCol = CoreImage::imgParentId;
        $this->coreImageImgTypeCol = CoreImage::imgType;
        $this->coverImgType = "subCategory-cover";
        $this->iconImgType = "subCategory-icon";
        $this->csvFileName = "subCategory_report";

        $this->viewAnyAbility = Constants::viewAnyAbility;
        $this->createAbility = Constants::createAbility;
        $this->editAbility = Constants::editAbility;
        $this->deleteAbility = Constants::deleteAbility;

        $this->code = Constants::subcategory;
        $this->show = Constants::show;
        $this->hide = Constants::hide;
        $this->enable = Constants::enable;
        $this->disable = Constants::disable;
        $this->delete = Constants::delete;
        $this->unDelete = Constants::unDelete;

        $this->coreFieldFilterModuleNameCol = CoreFieldFilterSetting::moduleName;
        $this->screenDisplayUiKeyCol = ScreenDisplayUiSetting::key;
        $this->screenDisplayUiIdCol = ScreenDisplayUiSetting::id;
        $this->screenDisplayUiIsShowCol = ScreenDisplayUiSetting::isShow;
        $this->customUiIsDelCol = CustomizeUi::isDelete;

        $this->loginUserIdParaApi = ps_constant::loginUserIdParaFromApi;
        $this->deviceTokenParaApi = ps_constant::deviceTokenKeyFromApi;

        $this->catTableName = Category::tableName;
        $this->catIdCol = Category::id;
        $this->catNameCol = Category::name;

    }

    public function store($request)
    {
        DB::beginTransaction();

        try {
            $subcategory = new Subcategory();
            $subcategory->name = $request->name;
            $subcategory->category_id = $request->category_id;
            $subcategory->status = $request->status;
            $subcategory->ordering = $request->ordering;
            if (!empty($_GET['login_user_id'])){
                $userId = $_GET['login_user_id'];
            } else {
                $userId = Auth::user()->id;
            }
            $subcategory->added_user_id = $userId;
            $subcategory->save();

            $imgParentId = $subcategory->id;

            // save category cover photo
            $this->updateOrCreateImage($request, "cover", $imgParentId, $this->coverImgType);

            // save category icon
            $this->updateOrCreateImage($request, "icon", $imgParentId, $this->iconImgType);

            DB::commit();
        } catch (\Throwable $e){
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }

        return $subcategory;
    }

    public function update($id, $request)
    {
        DB::beginTransaction();

        try {
            $subcategory = $this->getSubcategory($id);
            $subcategory->name = $request->name;
            $subcategory->category_id = $request->category_id;
            $subcategory->ordering = $request->ordering;
            $subcategory->status = $request->status;
            $subcategory->updated_user_id = Auth::user()->id;
            $subcategory->update();

            $imgParentId = $subcategory->id;

            // save category cover photo
            $this->updateOrCreateImage($request, "cover", $imgParentId, $this->coverImgType);

            // save category icon
            $this->updateOrCreateImage($request, "icon", $imgParentId, $this->iconImgType);

            DB::commit();
        } catch (\Throwable $e){
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }

        return $subcategory;
    }

    public function updateOrCreateImage($request, $fileKey, $imgParentId, $imgType ){
        if ($request->file($fileKey)){

            $conds[$this->coreImageImgParentIdCol] = $imgParentId;
            $conds[$this->coreImageImgTypeCol] = $imgType;
            $image = $this->imageService->getImage($conds);

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

    public function getSubCategories($relation = null, $status = null, $limit = null, $offset = null, $conds = null,$noPagination = null,  $pagPerPage = null){

        $sort = '';
        if(isset($conds['order_by'])){
            $sort = $conds['order_by'];
        }
        $subcategories = Subcategory::select($this->subcattableName.'.*')
                ->when(isset($conds['order_by']) && $conds['order_by'] , function ($q) use($sort){
                    if($sort == 'category_id@@name')
                    {
                        $q->join($this->catTableName, $this->catTableName.'.'.$this->catIdCol, '=', $this->CategoryIdCol);
                        $q->select($this->catTableName.'.'.$this->catNameCol.' as cat_name', $this->subcattableName.'.*');
                    }
                })
                ->when($relation, function ($q, $relation){
                    $q->with($relation);
                })
                ->when($limit, function ($query, $limit) {
                    $query->limit($limit);
                })
                ->when($offset, function ($query, $offset) {
                    $query->offset($offset);
                })
                ->when($status, function ($q, $status){
                    $q->where($this->subCatStatusCol, $status);
                })
                ->when($conds, function ($query, $conds) {
                    $query = $this->searching($query, $conds);
                })
                ->orderBy("added_date", 'desc')
                ->orderBy($this->subCatNameCol, 'asc')
                ->orderBy($this->subCatStatusCol, 'desc')
                ->latest();
                if ($pagPerPage){
                    $subcategories = $subcategories->paginate($pagPerPage)->onEachSide(1)->withQueryString();

                    } elseif ($noPagination){
                        $subcategories = $subcategories->get();
                    }else{
                        $subcategories = $subcategories->get();
                    }
        return $subcategories;
    }

    public function getSubcategory($id = null, $name = null, $relation = null){

        $subCategory = Subcategory::when($id, function ($q, $id){
                            $q->where($this->subCatIdCol, $id);
                        })
                        ->when($name, function ($q, $name){

                            $q->where($this->subCatNameCol, $name);
                        })
                        ->when($relation, function ($q, $relation){
                            $q->with($relation);
                        })
                        ->first();
        return $subCategory;
    }

    public function searching($query, $conds){
        if (isset($conds['keyword']) && $conds['keyword']) {
            $conds['searchterm'] = $conds['keyword'];
        }
        // search term
        if (isset($conds['searchterm']) && $conds['searchterm']) {
            $search = $conds['searchterm'];
            $query->where(function ($query) use ($search) {
                $query->where($this->subcattableName . '.' . $this->subCatNameCol, 'like', '%' . $search . '%');
            });
        }

        if(isset($conds['category_id']) && $conds['category_id']){
            $category_filter=$conds['category_id'];
            $query->whereHas('category', function($q) use($category_filter){
                $q->where($this->subcattableName .'.'.$this->CategoryIdCol, $category_filter);
            });
        }
        // order by
        if (isset($conds['order_by']) && isset($conds['order_type']) && $conds['order_by'] && $conds['order_type']){

            if($conds['order_by'] == 'id'){
                $query->orderBy($this->subcattableName.'.'.$this->subCatIdCol, $conds['order_type']);
            }
            elseif($conds['order_by'] == 'category_id@@name'){

                $query->orderBy('cat_name', $conds['order_type']);
            }
            else{
                $query->orderBy($conds['order_by'], $conds['order_type']);
            }

        }

        return $query;
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

    public function controlFieldArr(){
        // for control
        $controlFieldArr = [];
        $controlFieldObj = $this->takingForColumnProps(__('core__be_action'), "action", "Action", false, 0);
        array_push($controlFieldArr, $controlFieldObj);
        return $controlFieldArr;
    }

    public function index($routeName,$request){
        // check permission
        $checkPermission = $this->checkPermission($this->viewAnyAbility,Subcategory::class, "admin.index");

        // Search and filter
        $conds['searchterm'] = $request->input('search') ?? '';
        $conds['category_id'] = $request->input('category_filter') == 'all'? null  : $request->category_filter;

        $conds['order_by']= null;
        $conds['order_type'] = null;
        $row = $request->input('row') ?? Constants::dataTableDefaultRow;

        if($request->sort_field)
        {
            $conds['order_by'] = $request->sort_field;
            $conds['order_type'] = $request->sort_order;
        }

        $subcatRelation =['category', 'owner', 'editor'];
        $subcategories = SubcategoryWithKeyResource::collection($this->getSubCategories($subcatRelation,  null, null,null,$conds,  false,$row ));
        // dd($subcategories);

        // taking for column and columnFilterOption
        $keys = [ps_constant::handlingColumn, ps_constant::handlingFilter];
        $columnAndColumnFilter = takingForColumnAndFilterOption($this->code, $this->controlFieldArr(), $keys);

        // changing item arr object with new format
        $changedProductObj = $subcategories;
        // $categories = $this->categoryService->getCategories(null, $this->publish);
        $selected_category = $this->categoryService->getCategory($conds['category_id']);

        if($conds['order_by'])
        {
            $dataArr = [
                "checkPermission" => $checkPermission,
                'showCoreAndCustomFieldArr' => $columnAndColumnFilter[ps_constant::handlingColumn],
                'hideShowFieldForFilterArr' => $columnAndColumnFilter[ps_constant::handlingFilter],
                'subcategories' => $changedProductObj,
                'sort_field' =>$conds['order_by'],
                'sort_order'=>$request->sort_order,
                'search'=>$conds['searchterm'] ,
                'selectedCategory'=>$selected_category ? $selected_category : ''  ,
            ];
        }
        else
        {
            $dataArr = [
                "checkPermission" => $checkPermission,
                'showCoreAndCustomFieldArr' => $columnAndColumnFilter[ps_constant::handlingColumn],
                'hideShowFieldForFilterArr' => $columnAndColumnFilter[ps_constant::handlingFilter],
                'subcategories' => $changedProductObj,
                'search'=>$conds['searchterm'] ,
                'selectedCategory'=>$selected_category ? $selected_category : ''  ,
            ];
        }


        return $dataArr;
    }

    public function create(){
        // check permission
        $checkPermission = $this->checkPermission($this->createAbility,Subcategory::class, "admin.index");
        $categories = $this->categoryService->getCategories(null, $this->publish);
        $dataArr = [
            "checkPermission" => $checkPermission,
            'categories' => $categories
        ];
        return $dataArr;
    }

    public function edit($id){
        $subcategory = $this->getSubcategory($id, null , ['defaultIcon', 'defaultPhoto']);
        // check permission
        $checkPermission = $this->checkPermission($this->editAbility, $subcategory, "admin.index");
        $categories = $this->categoryService->getCategories(null, $this->publish);

        $conds = [
            'module_name' => Constants::subcategory,
            'enable' => 1,
            'mandatory' => 1,
            'is_core_field' => 1,
        ];

        $core_headers = CoreFieldFilterSetting::where($conds)->get();

        $validation = [];

        foreach($core_headers as $core_header){
            if($core_header->field_name == 'sub_cat_photo'){
                array_push($validation, 'cover');
            }
            if($core_header->field_name == 'sub_cat_icon'){
                array_push($validation, 'icon');
            }
        }

        $dataArr = [
            "checkPermission" => $checkPermission,
            'subcategory' => $subcategory,
            'categories' => $categories,
            'validation' => $validation
        ];
        return $dataArr;
    }

    public function destroy($id){
        $subcategory = $this->getSubcategory($id);

        // check permission
        $checkPermission = $this->checkPermission($this->deleteAbility, $subcategory, "admin.index");

        if (empty($subcategory)){
            $msg = "Record Not Found with given Id";
            return $msg;
        }
        $images = $this->imageService->getImages($subcategory->id);

        if(count($images)>0){
            foreach($images as $image){
                // delete image from storage folder
                $this->imageService->deleteImage($image->img_path);
                $image->delete();
            }
        }

        $name = $subcategory->name;
        $subcategory->delete();

        $status = [
            "checkPermission" => $checkPermission,
            // 'msg' => 'The '.$name.' row has been deleted successfully.',
            'msg'=>__('core__be_delete_success',['attribute'=>$name]),
            'flag' => $this->dangerFlag,
        ];

        return $status;
    }

    public function statusChange($id){
        $subcategory = $this->getSubcategory($id);
        if($subcategory->status == $this->publish){
            $subcategory->status = $this->unPublish;
        }else{
            $subcategory->status = $this->publish;
        }
        $subcategory->updated_user_id = Auth::user()->id;
        $subcategory->update();

        $status = [
            // 'msg' => 'The status of '.$subcategory->name.' row has been updated successfully.',
            'msg' =>  __('core__be_status_attribute_updated',['attribute'=>$subcategory->name]),
            'flag' => $this->successFlag,
        ];

        return $status;
    }

    public function takingForColumnAndFilterOption(){

        // for table
        $hideShowCoreFieldForColumnArr = [];
        $hideShowCustomFieldForColumnArr = [];

        $showProductCols = [];

        // for eye
        $hideShowFieldForColumnFilterArr = [];


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
        }, 'coreField' => function($q){
            $q->where($this->coreFieldFilterModuleNameCol, $this->code);
        }])
            ->where($this->coreFieldFilterModuleNameCol, $code)
            ->where($this->screenDisplayUiIsShowCol, $isShown)
            ->get();
        return $hiddenShownForFields;
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
            return responseMsgApi(__('subcore__no_data'), $this->noContentStatusCode, $this->successStatus);
        }
    }


    public function searchFromApi($request){
        $offset = $request->offset;
        $limit = $request->limit;

        $conds['keyword'] = $request->keyword;
        $conds['category_id'] = $request->category_id;
        $conds['order_by'] = $request->order_by;
        $conds['order_type'] = $request->order_type;

        $subCategoryApiRelation = $this->subCategoryApiRelation;
        $subCategories = $this->getSubCategories($subCategoryApiRelation, $this->publish, $limit, $offset, $conds);

        // save search history data
        if(isset($request->keyword) && !empty($request->keyword)){
            $searchdata = new \stdClass;
            $searchdata->user_id = $request->login_user_id;
            $searchdata->keyword = $request->keyword;
            $searchdata->type = $this->subCategoryType;
            $searchdata->is_home_page_search = $this->notFromHomePageSearch;
            $searchdata->added_user_id = $request->login_user_id;
            $this->searchHistoryService->store($searchdata);
        }

        return $subCategories;
    }


}
