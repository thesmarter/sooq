<?php

namespace Modules\Template\PSXFETemplate\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Routing\Controller;
use Modules\Blog\Http\Services\BlogService;
use Modules\Core\Entities\FrontendSetting;
use Modules\Core\Http\Services\FrontendSettingService;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;
use stdClass;

class BlogController extends Controller
{
    const parentPath = "Pages/vendor/views/";
    const blogListPage = self::parentPath."blog/list/BlogList";
    const blogDetailPage = self::parentPath."blog/detail/BlogDetail";

    protected $frontendSettingService, $blogService;
    public function __construct(FrontendSettingService $frontendSettingService, BlogService $blogService)
    {
        $this->frontendSettingService = $frontendSettingService;
        $this->blogService = $blogService;
    }

    public function feBlog(Request $request){
        //for meta
        setMeta( $this->frontendSettingService->getFrontendSetting()->frontend_meta_title ?? __('site_name'),
                        $this->frontendSettingService->getFrontendSetting()->frontend_meta_description ?? null, null );
                        
        $dataArr = [
            'blogs' => $this->blogService->searchFromApi($request)
        ];

        return renderView(self::blogListPage, $dataArr);
    }

    public function feBlogDetail(Request $request){
        //for meta
        $blog = $this->blogService->getBlog($request->blogId);
        $image =[
            'img_parent_id' => $request->blogId,
            'img_type' => 'blog'
        ];
        setMeta($blog->name, $blog->description, $image);

        $dataArr = [
            'blog' => $this->blogService->blogByIdForFE($request),
        ];

        return renderView(self::blogDetailPage, $dataArr);
    }
}
