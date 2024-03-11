<?php
namespace Modules\Core\Http\Services;

use Carbon\Carbon;
use App\Http\Services\PsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\Category;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Entities\CoreImage;
use Modules\Core\Exports\CategoryReportExport;
use Modules\Core\Http\Services\ImageService;
use Modules\Core\Imports\CategoryImport;
use Modules\Core\Entities\ScreenDisplayUiSetting;
use Modules\Core\Entities\CoreFieldFilterSetting;
use Modules\Core\Entities\CustomizeUi;
use Modules\Core\Transformers\Backend\Model\Category\CategoryWithKeyResource;
use Modules\Core\Transformers\Backend\NoModel\CategoryReport\CategoryReportWithKeyResource;
use PHPUnit\TextUI\XmlConfiguration\Constant;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;


class CategoryService extends PsService
{
    protected $notFromHomePageSearch, $dropDownUi, $radioUi, $catNameCol, $categoryApiRelation, $tableName, $searchHistoryService, $searchHistoryCategoryType, $deleteAbility, $editAbility, $createAbility, $viewAnyAbility, $catStatusCol, $csvFileName, $iconImgType, $coverImgType, $imageService, $catIdCol, $coreImageImgParentIdCol, $publish, $unPublish, $coreImageImgTypeCol, $noContentStatusCode, $successStatus,
    $customUiIsDelCol,$screenDisplayUiIdCol, $screenDisplayUiIsShowCol, $screenDisplayUiKeyCol,$code,$coreFieldFilterModuleNameCol,$delete, $unDelete, $enable, $disable, $hide, $show;

    public function __construct(ImageService $imageService, SearchHistoryService $searchHistoryService)
    {
        $this->dropDownUi = Constants::dropDownUi;
        $this->radioUi = Constants::radioUi;

        $this->imageService = $imageService;
        $this->searchHistoryService = $searchHistoryService;
        $this->searchHistoryCategoryType = Constants::searchHistoryCategoryType;

        $this->tableName = Category::tableName;
        $this->catIdCol = Category::id;
        $this->catStatusCol = Category::status;
        $this->catNameCol = Category::name;
        $this->coreImageImgParentIdCol = CoreImage::imgParentId;
        $this->coreImageImgTypeCol = CoreImage::imgType;

        $this->publish = Constants::publish;
        $this->unPublish = Constants::unPublish;
        $this->show = Constants::show;
        $this->hide = Constants::hide;
        $this->enable = Constants::enable;
        $this->disable = Constants::disable;
        $this->delete = Constants::delete;
        $this->unDelete = Constants::unDelete;

        $this->coverImgType = "category-cover";
        $this->iconImgType = "category-icon";
        $this->csvFileName = "category_report";
        $this->categoryApiRelation = ['defaultPhoto', 'defaultIcon'];

        $this->noContentStatusCode = Constants::noContentStatusCode;
        $this->successStatus = Constants::successStatus;

        $this->viewAnyAbility = Constants::viewAnyAbility;
        $this->createAbility = Constants::createAbility;
        $this->editAbility = Constants::editAbility;
        $this->deleteAbility = Constants::deleteAbility;
        $this->code = Constants::category;

        $this->screenDisplayUiKeyCol = ScreenDisplayUiSetting::key;
        $this->screenDisplayUiIdCol = ScreenDisplayUiSetting::id;
        $this->screenDisplayUiIsShowCol = ScreenDisplayUiSetting::isShow;

        $this->coreFieldFilterModuleNameCol = CoreFieldFilterSetting::moduleName;

        $this->customUiIsDelCol = CustomizeUi::isDelete;

        $this->notFromHomePageSearch = Constants::notFromHomePageSearch;

    }

    public function createOrUpdate($request, $categoryId = 0)
    {
        $catStatus =  $this->unPublish;
        if($request->status == 0){
            $catStatus = $this->unPublish;
        }else{
            $catStatus = $this->publish;
        }
        DB::beginTransaction();
        // try {

            $category = Category::lockForUpdate()->updateOrCreate(
                ['id' => $categoryId],
                [
                    'name' => $request->name,
                    'ordering' => $request->ordering,
                    'status' => $catStatus,
                    'added_user_id' => Auth::user()->id
                ],
            );

            $imgParentId = $category->id;

            if($request->images)
            {
                $images = $request->images;
                // dd($files);
                foreach($images as $image)
                {
                    $image_description = "";
                    $path = public_path('storage/uploads/items/'.$image);

                    // dd($path);

                    $file_exist = File::exists($path);

                    if($file_exist){
                        $image_arr = $request->extra_caption;
                        if($image_arr){
                            foreach($image_arr as $key=>$value){
                                if($value['name'] == $image){
                                    $image_description = $value['value'];
                                }
                                // dd($value['name']);
                            }
                            // $key = array_search($image, $image_arr);
                            // dd($key);
                        }
                        $data[$this->coreImageImgParentIdCol] = $imgParentId;
                        $data[$this->coreImageImgTypeCol] = 'category';
                        $file = Image::make($path);
                        // dd($file);
                        $this->imageService->update(null,null,$file, $data,false,'itemMulti');
                        $image_path = CoreImage::where('img_path',$image)->first();
                        // dd($image_path);
                        // dd($image_description);
                        if($image_description){
                            $image_path->img_desc = $image_description;
                            $image_path->update();
                        }



                    }
                }
            }


            // save category cover photo
            $this->updateOrCreateImage($request, "cover", $imgParentId, $this->coverImgType);

            // save category icon
            $this->updateOrCreateImage($request, "icon", $imgParentId, $this->iconImgType);

            DB::commit();

        // }catch (\Throwable $e){
        //     DB::rollBack();
        //     return ['error' => $e->getMessage()];
        // }

        return $category;

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

    public function getCategories($relation = null, $status = null, $limit = null, $offset = null, $conds = null,$noPagination = null,  $pagPerPage = null,$touchCount = null){
        $sort = '';
        if(isset($conds['order_by'])){
            $sort = $conds['order_by'];
        }
        $categories = Category::when($relation, function ($q, $relation){
                                    $q->with($relation);
                                })
                                ->when($status, function ($q, $status){
                                    $q->where($this->catStatusCol, $status);
                                })
                                ->when($touchCount, function ($q, $touchCount){
                                    $q->withCount(['category_touch']);
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
                                ->when(empty($sort), function ($query, $conds){
                                    $query->orderBy('added_date', 'desc')->orderBy('status', 'desc')->orderBy('name', 'asc');
                                });

                                if ($pagPerPage){
                                $categories = $categories->paginate($pagPerPage)->onEachSide(1)->withQueryString();
                                } elseif ($noPagination){
                                    $categories = $categories->get();
                                }else{
                                    $categories = $categories->get();
                                }
                                return $categories;
    }

    public function searching($query, $conds){
        // search term
        if (isset($conds['keyword']) && $conds['keyword']) {
            $conds['searchterm'] = $conds['keyword'];
        }
        if (isset($conds['searchterm']) && $conds['searchterm']) {
            $search = $conds['searchterm'];
            $query->where(function ($query) use ($search) {
                $query->where($this->tableName . '.' . $this->catNameCol, 'like', '%' . $search . '%');
            });
        }
        if(isset($conds['selected_date']) && $conds['selected_date']){
            $date_filter = $conds['selected_date'];
                // dd($date_filter);
            if($date_filter[1] == ''){
                $date_filter[1] = Carbon::now();
            }
            $query->whereBetween($this->tableName . '.added_date', $date_filter);
            // $date_filter=$conds['selected_date'];
            // $new_date=date('Y-m-d', strtotime($date_filter));

            // $query->whereDate($this->tableName . '.added_date','=',$new_date);
        }

        if(isset($conds['added_date']) && $conds['added_date']){
            $date_filter = $conds['added_date'];
            $query->where($this->tableName .'.added_date', $date_filter);
        }

        if(isset($conds['date_range']) && $conds['date_range']){
            $date_filter = $conds['date_range'];
            if($date_filter[1] == ''){
                $date_filter[1] = Carbon::now();
            }
            $query->whereBetween($this->tableName . '.added_date', $date_filter);
        }

        if (isset($conds['added_user_id']) && $conds['added_user_id']) {
            $query->where('added_user', $conds['added_user_id']);
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

    public function getCategory($id){
        $category = Category::where($this->catIdCol, $id)->with(['icon', 'cover'])->first();
        return $category;
    }

    public function getImages($category){
        $images = CoreImage::where($this->coreImageImgParentIdCol, $category->id)->get();
        return $images;
    }

    public function getImage($conds){
        $image = $this->imageService->getImage($conds);
        return $image;
    }

    public function importCSVFile($request, $key){
        $import = new CategoryImport();
        $import->import($request->file($key));
    }

    public function makePublishOrUnpublish($id){

        $category = Category::find($id);

        if($category->status == $this->publish){
            $category->status = $this->unPublish;
        }else{
            $category->status = $this->publish;
        }
        $category->updated_user_id = Auth::user()->id;
        $category->update();
    }

    public function authorizations($abilities = []){
        return collect(array_flip($abilities))->map(function ($index, $ability){
            return Gate::denies($ability);
        });
    }
    //--------------
    // ----------------
    public function index($routeName,$request){
        // check permission
        $checkPermission = $this->checkPermission($this->viewAnyAbility, Category::class, "admin.index");

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

        $relation = ['owner', 'editor'];
        $categories = CategoryWithKeyResource::collection($this->getCategories($relation,null, null,null, $conds,false, $row));

        // taking for column and columnFilterOption
        $columnAndColumnFilter = $this->takingForColumnAndFilterOption();
        $showProductCols = $columnAndColumnFilter['showCoreField'];
        $columnProps = $columnAndColumnFilter['arrForColumnProps'];
        $columnFilterOptionProps = $columnAndColumnFilter['arrForColumnFilterProps'];

        // changing item arr object with new format
        $changedObj = $categories;
        // $owners = $this->userService->getUsers(null, $this->publish, $this->unBan);

        if($conds['order_by'])
        {
            $dataArr = [
                "checkPermission" => $checkPermission,
                'showCoreAndCustomFieldArr' => $columnProps,
                'hideShowFieldForFilterArr' => $columnFilterOptionProps,
                'categories' => $changedObj,
                'sort_field' =>$conds['order_by'],
                'sort_order'=>$request->sort_order,
                'search'=>$conds['searchterm']
            ];
        }
        else
        {
            $dataArr = [
                "checkPermission" => $checkPermission,
                'showCoreAndCustomFieldArr' => $columnProps,
                'hideShowFieldForFilterArr' => $columnFilterOptionProps,
                'categories' => $changedObj,
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
                'id' => $hideShowField['id'],
                'is_show' => $hideShowField['hidden'] ? $this->hide : $this->show,
            ];
        }
        ScreenDisplayUiSetting::upsert(
            $hideShowFieldData,
            ['id'], ['is_show']
        );
    }

    public function create(){
        // check permission
        $checkPermission = $this->checkPermission($this->createAbility,Category::class, "admin.index");
        $images = CoreImage::where('img_type','category')->get();

        $dataArr = [
            "checkPermission" => $checkPermission,
            "images"=>$images,
        ];
        return $dataArr;
    }

    public function edit($id){
        $category = $this->getCategory($id);
        // check permission
        $checkPermission = $this->checkPermission($this->editAbility, $category, "admin.index");

        $conds = [
            'module_name' => Constants::category,
            'enable' => 1,
            'mandatory' => 1,
            'is_core_field' => 1,
        ];

        $core_headers = CoreFieldFilterSetting::where($conds)->get();

        $validation = [];

        foreach($core_headers as $core_header){
            if($core_header->field_name == 'cat_photo'){
                array_push($validation, 'cover');
            }
            if($core_header->field_name == 'cat_icon'){
                array_push($validation, 'icon');
            }
        }

        $dataArr = [
            "validation" => $validation,
            "category" => $category,
            "checkPermission" => $checkPermission,
        ];
        return $dataArr;
    }

    public function destroy($id){
        $category = Category::find($id);

        if (permissionControl(15,4)){

        }

        // check permission
        $checkPermission = $this->checkPermission($this->deleteAbility, $category, "admin.index");
        if (!empty($checkPermission)){
            $dataArr = [
                "checkPermission" => $checkPermission,
            ];
            return $dataArr;
        }
        $name = $category->name;

        $images = $this->getImages($category);

        deleteImages($images);
        $category->delete();

        $dataArr = [
            "name" => $name,
            "checkPermission" => $checkPermission,
        ];
        return $dataArr;
    }

    // for category report
    public function categoryReportIndex($request){

        // search filter
        $conds['searchterm'] = $request->input('search') ?? '';
        $conds['order_by']= 'category_touch_count';
        $conds['order_type'] = 'desc';
        // $conds['selected_date'] = $request->input('date_filter') == 'all'? null  : $request->date_filter;

        $date_range = null;
        if(!empty($request->date_filter) && $request->date_filter != 'all'){
            $start_date = $request->date_filter[0];
            $end_date = $request->date_filter[1];
            if(empty($end_date)){
                $end_date = Carbon::now();
            }
            $date_range = [$start_date, $end_date];
        }

        $conds['selected_date'] = $request->input('date_filter') == 'all'? null  : $date_range;
        $row = $request->input('row') ?? Constants::dataTableDefaultRow;

        if($request->sort_field)
        {
            $conds['order_by'] = $request->sort_field;
            $conds['order_type'] = $request->sort_order;
        }

        $relation = ['cover', 'icon'];
        $categories = CategoryReportWithKeyResource::collection($this->getCategories($relation,null, null,null, $conds,false, $row,true));

        // changing item arr object with new format
        $changedObj = $categories;

        if($conds['order_by'])
        {
            $dataArr = [
                'categories' => $changedObj,
                'sort_field' =>$conds['order_by'],
                'sort_order'=>$request->sort_order,
                'search'=>$conds['searchterm'] ,
                'selectedDate'=> $conds['selected_date'],
            ];
        }
        else
        {
            $dataArr = [
                'categories' => $changedObj,
                'search'=>$conds['searchterm'] ,
                'selectedDate'=> $conds['selected_date'],
            ];
        }


        return $dataArr;
    }

    public function categoryReportShow($id){
        $category = Category::where($this->catIdCol, $id)->with(['cover', 'icon'])->first();
        $dataArr = [
            "category" => $category,
        ];
        return $dataArr;
    }

    public function categoryReportCsvExport(){
        $filename = newFileNameForExport($this->csvFileName);
        return (new CategoryReportExport)->download($filename, \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv',
        ]);
    }

    // for api
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

    public function searchFromApi($request){
        $offset = $request->offset;
        $limit = $request->limit;

        $conds['keyword'] = $request->keyword;
        $conds['order_by'] = $request->order_by;
        $conds['order_type'] = $request->order_type;

        $touchCount = 'category_touch';

        $categoryApiRelation = $this->categoryApiRelation;

        $categories = $this->getCategories($categoryApiRelation, $this->publish, $limit, $offset, $conds, null, null, $touchCount);

        // save search history data
        if(isset($request->keyword) && !empty($request->keyword)){
            $searchdata = new \stdClass;
            $searchdata->user_id = $request->login_user_id;
            $searchdata->keyword = $request->keyword;
            $searchdata->type = $this->searchHistoryCategoryType;
            $searchdata->is_home_page_search = $this->notFromHomePageSearch;
            $searchdata->added_user_id = $request->login_user_id;
            $this->searchHistoryService->store($searchdata);
        }

        return $categories;

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

                $coreFieldObjForColumnsProps = $this->takingForColumnProps($label, $field, $type, $isShowSorting, $ordering);
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

                $customFieldObjForColumnsProps = $this->takingForColumnProps($label, $field, $type, $isShowSorting, $ordering);
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
