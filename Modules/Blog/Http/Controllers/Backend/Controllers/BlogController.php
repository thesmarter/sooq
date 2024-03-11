<?php

namespace Modules\Blog\Http\Controllers\Backend\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Blog\Http\Services\BlogService;
use Modules\Blog\Http\Requests\UpdateBlogRequest;
use Modules\Blog\Http\Requests\StoreBlogRequest;
use Modules\Core\Http\Services\CoreFieldFilterSettingService;

class BlogController extends Controller
{

    protected $blogService, $coreFieldFilterSettingService;

    public function __construct(BlogService $blogService, CoreFieldFilterSettingService $coreFieldFilterSettingService)
    {
        $this->blogService = $blogService;
        $this->coreFieldFilterSettingService = $coreFieldFilterSettingService;
    }

    public function index(Request $request)
    {
        return $this->blogService->index($request);
    }

    public function create()
    {
        return $this->blogService->create();
    }

    public function store(StoreBlogRequest $request)
    {
        return $this->blogService->store($request);
    }

    public function edit($id)
    {
        return $this->blogService->edit($id);
    }

    public function update(UpdateBlogRequest $request, $id)
    {
        return $this->blogService->update($id, $request);
    }

    public function destroy($id)
    {
        return $this->blogService->destroy($id);
    }

    public function statusChange($id){
        return $this->blogService->makePublishOrUnpublish($id);
    }

    public function screenDisplayUiStore(Request $request) {
        makeColumnHideShown($request);
        return redirect()->back();
    }

}
