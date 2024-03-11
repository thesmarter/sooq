<?php

namespace Modules\Package\Http\Controllers\Backend\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Routing\Controller;
use Modules\Package\Http\Services\PackageBoughtTransactionService;
use Illuminate\Http\Request;
use Modules\Core\Constants\Constants;
use App\Config\ps_constant;

class PackageReportController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */

    const parentPath = "package_report/";
    const indexPath = self::parentPath."Index";
    const editPath = self::parentPath."Edit";
    const indexRoute = "package_report.index";
    const editRoute = "package_report.edit";

    protected $packageBoughtTransactionService, $packageService, $successFlag, $dangerFlag, $csvFile, $warningFlag;

    public function __construct(PackageBoughtTransactionService $packageBoughtTransactionService)
    {
        $this->packageBoughtTransactionService = $packageBoughtTransactionService;
        
        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;
        $this->warningFlag = Constants::warning;
        $this->csvFile = Constants::csvFile;

    }

    public function index(Request $request)
    {
        if (!permissionControl(Constants::packageReportModule,ps_constant::readPermission)){
            return redirect()->route('admin.index');
        }
        $dataArr = $this->packageBoughtTransactionService->packageReportIndex($request);
        
        return renderView(self::indexPath, $dataArr);
    }

    public function show($id)
    {
        $relations = ['package', 'user'];
        if (!permissionControl(Constants::packageReportModule,ps_constant::readPermission)){
            return redirect()->route('admin.index');
        }
        $dataArr['transaction'] = $this->packageBoughtTransactionService->getPackageBoughtTransaction($id, null, $relations);
        
        // $dataArr['packages'] = $this->packageService->getPackages();

        // $dataArr = $this->packageBoughtTransactionService->packageReportShow($id);
        return renderView(self::editPath, $dataArr);
    }

    public function csvExport(){
        // filename
		return $this->packageBoughtTransactionService->packageReportCsvExport();
    }
}
