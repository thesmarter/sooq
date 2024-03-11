<?php
namespace Modules\Blog\Http\Services;

use App\Config\ps_constant;
use App\Http\Services\PsService;
use Illuminate\Support\Facades\DB;
use Modules\Core\Constants\Constants;
use Modules\Blog\Entities\Blog;
use Illuminate\Support\Facades\Auth;
use Modules\Blog\Transformers\Backend\Model\Blog\BlogWithKeyResource;
use Modules\Core\Entities\CoreFieldFilterSetting;
use Modules\Core\Entities\LocationCity;
use Modules\Core\Entities\ScreenDisplayUiSetting;
use Modules\Core\Http\Services\ImageService;
use Modules\Core\Http\Services\LocationCityService;
use Illuminate\Support\Facades\Gate;
use Modules\Blog\Transformers\Api\App\V1_0\BlogApiResource;
use Modules\Core\Http\Services\CoreFieldFilterSettingService;
use Modules\Core\Http\Services\MobileSettingService;

class BlogService extends PsService
{
    const parentPath = "blog/";
    const indexPath = self::parentPath."Index";
    const createPath = self::parentPath."Create";
    const editPath = self::parentPath."Edit";
    const indexRoute = "blog.index";
    const createRoute = "blog.create";
    const editRoute = "blog.edit";

    protected $imageService, $mobileSettingService, $locationCityService, $coreFieldFilterSettingService, $blogApiRelation;

    public function __construct(ImageService $imageService, LocationCityService $locationCityService, CoreFieldFilterSettingService $coreFieldFilterSettingService, MobileSettingService $mobileSettingService)
    {
        $this->imageService = $imageService;
        $this->mobileSettingService = $mobileSettingService;
        $this->locationCityService = $locationCityService;
        $this->coreFieldFilterSettingService = $coreFieldFilterSettingService;
        $this->blogApiRelation = ['city', 'cover'];
    }

    public function create()
    {
        // check permission start
        $checkPermission = $this->checkPermission(Constants::createAbility,Blog::class, "admin.index");
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        // check permission end

        $cities = $this->locationCityService->getLocationCityList(null, Constants::publish);
        $coreFieldFilterSettings = $this->coreFieldFilterSettingService->getCoreFields(withNoPag: 1, moduleName: Constants::blog);
        $dataArr = [
            //"checkPermission" => $checkPermission,
            'cities' => $cities,
            'coreFieldFilterSettings' => $coreFieldFilterSettings
        ];
        return renderView(self::createPath, $dataArr);
    }

    public function store($request)
    {
        DB::beginTransaction();

        try {
            // save blog
            $blog = new Blog();
            $blog->fill($request->validated());
            $blog->added_user_id = Auth::user()->id;
            $blog->save();

            // save blog cover photo
            $imgParentId = $blog->id;
            $this->imageService->updateOrCreateImage($request, "cover", $imgParentId, Constants::blogCoverImgType);

            DB::commit();
            return redirectView(self::indexRoute, $blog);
        } catch (\Throwable $e) {

            DB::rollBack();
            return redirectView(self::createRoute, $e->getMessage(), Constants::danger);
        }

        // return $blog;
    }

    public function update($id, $request)
    {
        DB::beginTransaction();

        try {
            // update blog
            $blog = $this->getBlog($id);
            $blog->updated_user_id = Auth::user()->id;
            $blog->update($request->validated());

            // update blog cover photo
            $imgParentId = $blog->id;
            $this->imageService->updateOrCreateImage($request, "cover", $imgParentId, Constants::blogCoverImgType);

            DB::commit();
            return redirectView(self::indexRoute, $blog);
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirectView(self::editRoute, $e->getMessage(), Constants::danger, $id);
        }
    }

    public function getBlogs($relation = null, $status = null, $limit = null, $offset = null,$noPagination = null,  $pagPerPage = null, $conds = null)
    {

        $sort = '';
        if(isset($conds['order_by'])){
            $sort = $conds['order_by'];
        }
        $blogs = Blog::select(Blog::tableName.'.*')
        ->when(isset($conds['order_by']) && $conds['order_by'] , function ($q) use($sort){
            if($sort == Blog::locationCityId.'@@name')
            {
                $q->join(LocationCity::tableName, LocationCity::tableName.'.'.LocationCity::id, '=', Blog::locationCityId);
                $q->select(LocationCity::tableName.'.'.LocationCity::name.' as city_name', Blog::tableName.'.*');
            }
        })
        ->when($relation, function ($q, $relation){
            $q->with($relation);
        })
        ->when($status, function ($q, $status){
            $q->where(Blog::status, $status);
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
            $query->orderBy(Blog::tableName.'.added_date','desc')->orderBy(Blog::tableName.'.'.Blog::status, 'desc')->orderBy(Blog::tableName.'.'.Blog::name, 'asc');
        });
        if ($pagPerPage){
            $blogs = $blogs->paginate($pagPerPage)->onEachSide(1)->withQueryString();
        } elseif ($noPagination){
            $blogs = $blogs->get();
        }
        return $blogs;
    }

    public function searching($query, $conds)
    {
        $sort = '';
        // $pagPerPage = null;
        if (isset($conds['order_by'])) {
            $sort = $conds['order_by'];
        }

        // search term
        if (isset($conds['searchterm']) && $conds['searchterm']) {
            $search = $conds['searchterm'];
            $query->where(function ($query) use ($search) {
                $query->where(Blog::tableName . '.' . Blog::name, 'like', '%' . $search . '%');
            });
        }

        if(isset($conds[Blog::locationCityId]) && $conds[Blog::locationCityId]){
            $city_filter = $conds[Blog::locationCityId];
            $query->whereHas('city', function($q) use($city_filter){
                $q->where(Blog::tableName .'.'.Blog::locationCityId, $city_filter);
            });
        }

        if (isset($conds['added_user_id']) && $conds['added_user_id']) {
            $query->where(Blog::tableName .'.'.Blog::addedUserId, $conds['added_user_id']);
        }


        // order by
        if (isset($conds['order_by']) && isset($conds['order_type']) && $conds['order_by'] && $conds['order_type']){

            if($conds['order_by'] == 'id'){
                $query->orderBy(Blog::tableName.'.'.Blog::id, $conds['order_type']);
            } elseif($conds['order_by'] == 'location_city_id@@name'){

                $query->orderBy('city_name', $conds['order_type']);
            }
            else{
                $query->orderBy($conds['order_by'], $conds['order_type']);
            }
        }

        return $query;
    }

    public function getBlog($id, $relation = null)
    {
        $blog = Blog::where(Blog::id, $id)
        ->when($relation, function ($q, $relation) {
            $q->with($relation);
        })->first();
        return $blog;
    }

    public function makePublishOrUnpublish($id)
    {
        $blog = Blog::find($id);
        if($blog->status == Constants::publish){
            $blog->status = Constants::unPublish;
        }else{
            $blog->status = Constants::publish;
        }
        $blog->updated_user_id = Auth::user()->id;
        $blog->update();

        return redirectView(self::indexRoute, __('core__be_status_updated'));
    }

    public function controlFieldArr()
    {
        // for control
        $controlFieldArr = [];
        $controlFieldObj = takingForColumnProps(__('core__be_action'), "action", "Action", false, 0);
        array_push($controlFieldArr, $controlFieldObj);
        return $controlFieldArr;
    }

    public function index($request)
    {
        // check permission
        $checkPermission = $this->checkPermission(Constants::viewAnyAbility, Blog::class, "admin.index");
        if (!empty($checkPermission)){
            return $checkPermission;
        }

        // search filter
        $conds['searchterm'] = $request->input('search') ?? '';
        $conds['location_city_id'] =$request->input('city_filter') == "all" ? null : $request->city_filter;

        $conds['order_by']= null;
        $conds['order_type'] = null;
        $row = $request->input('row') ?? Constants::dataTableDefaultRow;

        if($request->sort_field)
        {
            $conds['order_by'] = $request->sort_field;
            $conds['order_type'] = $request->sort_order;
        }

        // manipulate blog data
        $relations = ['city', 'owner', 'editor'];
        $blogs = BlogWithKeyResource::collection($this->getBlogs($relations,null, null,null,false,  $row, $conds));

        // taking for column and columnFilterOption
        $columnAndColumnFilter = takingForColumnAndFilterOption(Constants::blog, $this->controlFieldArr());

        // changing item arr object with new format
        $changedBlogObj = $blogs;

        $dataArr = [
            "checkPermission" => $checkPermission,
            'showCoreAndCustomFieldArr' => $columnAndColumnFilter[ps_constant::handlingColumn],
            'hideShowFieldForFilterArr' => $columnAndColumnFilter[ps_constant::handlingFilter],
            'blogs' => $changedBlogObj,
            'sort_field' =>$conds['order_by'],
            'sort_order'=>$request->sort_order,
            'search'=>$conds['searchterm'] ,
        ];
        return renderView(self::indexPath, $dataArr);
    }

    public function edit($id)
    {
        $coreFieldFilterSettings = $this->coreFieldFilterSettingService->getCoreFields(withNoPag: 1, moduleName: Constants::blog);
        $dataWithRelation = ['cover', 'city'];
        $blog = $this->getBlog($id, $dataWithRelation);
        // check permission start
        $checkPermission = $this->checkPermission(Constants::editAbility, $blog, "admin.index");
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        // check permission end
        $cities = $this->locationCityService->getLocationCityList(null, Constants::publish);

        $conds = [
            'module_name' => Constants::blog,
            'enable' => 1,
            'mandatory' => 1,
            'is_core_field' => 1,
        ];

        $core_headers = CoreFieldFilterSetting::where($conds)->get();

        $validation = [];

        foreach($core_headers as $core_header){
            if($core_header->field_name == 'blog_photo'){
                array_push($validation, 'cover');
            }
        }

        $dataArr = [
            "checkPermission" => $checkPermission,
            "blog" => $blog,
            "cities" => $cities,
            'coreFieldFilterSettings' => $coreFieldFilterSettings,
            'validation' => $validation
        ];
        return renderView(self::editPath, $dataArr);
    }

    public function destroy($id){
        $blog = Blog::find($id);

        // check permission start
        $checkPermission = $this->checkPermission(Constants::deleteAbility, $blog, "admin.index");
        if (!empty($checkPermission)){
            return $checkPermission;
        }
        // check permission end

        deleteImages($this->imageService->getImages($blog->id));
        $blog->delete();

        $dataArr = [
           // "checkPermission" => $checkPermission,
            "msg" =>  __('core__be_delete_success', ['attribute' => $blog->name]),
            "flag" => Constants::success
        ];

        return redirectView(self::indexRoute, $dataArr['msg'], $dataArr['flag']);
    }

    // for api
    public function searchFromApi($request)
    {
        $defaultLimit = $this->mobileSettingService->getMobileSetting()->default_loading_limit;
        $offset = $request->offset;
        $limit = $request->limit ? $request->limit : ($defaultLimit ? $defaultLimit :  9);

        $conds['searchterm'] = $request->keyword;
        $conds['location_city_id'] = $request->location_city_id;
        $conds['order_by'] = $request->order_by;
        $conds['order_type'] = $request->order_type;

        $blogApiRelation = $this->blogApiRelation;
        $blogs = $this->getBlogs($blogApiRelation, Constants::publish, $limit, $offset, 1, null, $conds);
        $data = BlogApiResource::collection($blogs);

        $hasError = noDataError( $request->offset, $data);

        if($hasError)
            return $hasError;
        else {
            return $data;
        }

    }

    public function blogByIdForFE($request)
    {
        $id = $request->id ? $request->id : $request->blogId;

        $blogApiRelation = $this->blogApiRelation;
        $blog = $this->getBlog($id, $blogApiRelation);

        if(!empty($blog)){
            $data = new BlogApiResource($blog);
        } else {
            $data = null;
        }
        return $data;
    }

    public function detailFromApi($request)
    {
        $blog = $this->blogByIdForFE($request);

        if (!$blog){
            $response =  ['error' =>  __('core__api_record_not_found',[],$request->language_symbol),'status' =>  Constants::noContentStatusCode ];
            return responseMsgApi($response['error'], Constants::notFoundStatusCode);
        } else {
            return responseDataApi($blog);
        }

    }

}
