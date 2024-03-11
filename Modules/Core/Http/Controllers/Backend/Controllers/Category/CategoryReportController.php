<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\Category;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\Shop;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\Category;
use Modules\Core\Entities\Currency;
use Modules\Core\Entities\LocationCity;
use Illuminate\Contracts\Support\Renderable;
use Modules\Core\Exports\CategoryReportExport;
use Modules\Core\Http\Services\CategoryService;
use App\Config\ps_constant;

class CategoryReportController extends Controller
{

    const parentPath = "category_report/";
    const indexPath = self::parentPath."Index";
    const createPath = self::parentPath."Create";
    const editPath = self::parentPath."Edit";
    const indexRoute = "category.index";
    const createRoute = "category.create";
    const editRoute = "category.edit";

    protected $categoryService, $successFlag, $dangerFlag, $csvFile;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;
        $this->csvFile = Constants::csvFile;
    }

    // Category Report
    public function categoryReportIndex(Request $request)
    {
        if (!permissionControl(Constants::categoryReportModule,ps_constant::readPermission)){
            return redirect()->route('admin.index');
        }

        $dataArr = $this->categoryService->categoryReportIndex($request);
        return renderView(self::indexPath, $dataArr);
    }

    public function categoryReportShow($id)
    {
        $dataArr = $this->categoryService->categoryReportShow($id);
        return renderView(self::editPath, $dataArr);
    }

    public function categoryReportCsvExport(){
        // filename
		return $this->categoryService->categoryReportCsvExport();
    }



}
