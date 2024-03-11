<?php

namespace Modules\Package\Http\Controllers\Backend\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Constants\Constants;
use Modules\Package\Http\Services\PackageBoughtTransactionService;
use Modules\Package\Http\Services\PackageService;
use App\Config\ps_constant;

class OfflinePackageController extends Controller
{
    const parentPath = "offline_package/";
    const indexPath = self::parentPath . "Index";
    const editPath = self::parentPath . "Edit";
    const indexRoute = "offline_package.index";
    const editRoute = "offline_package.edit";

    protected $packageService, $packageBoughtTransactionService, $successFlag, $dangerFlag, $offlinePaymentMethod, $categoryService, $subcategoryService, $publish;

    public function __construct(PackageBoughtTransactionService $packageBoughtTransactionService, PackageService $packageService)
    {
        $this->packageBoughtTransactionService = $packageBoughtTransactionService;
        $this->packageService = $packageService;
        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;
        $this->offlinePaymentMethod = Constants::offlinePaymentMethod;
        $this->publish = Constants::publish;

    }


    public function index(Request $request)
    {
        if (!permissionControl(Constants::offlinePackageBoughtModule,ps_constant::readPermission)){
            return redirect()->route('admin.index');
        }
        $dataArr = $this->packageBoughtTransactionService->offlinePackageBoughtIndex($request);

        return renderView(self::indexPath, $dataArr);
    }

    public function edit($id)
    {
        $relations = ['package', 'user'];
        $dataArr['transaction'] = $this->packageBoughtTransactionService->getPackageBoughtTransaction($id, null, $relations);
        // $dataArr['packages'] = $this->packageService->getPackages();

        return renderView(self::editPath, $dataArr);
    }

    public function update(Request $request, $id)
    {
        $transaciotn = $this->packageBoughtTransactionService->update($id, $request);

        // if have error
        if (isset($transaciotn['error'])) {
            $msg = $transaciotn['error'];
            return redirectView(self::editRoute, $msg, $this->dangerFlag, $id);
        }

        return redirect()->back();
    }

    public function destroy($id)
    {
        $this->packageBoughtTransactionService->destroy($id);

        // $msg = 'This row has been deleted successfully.';
        $msg = __('core__be_delete_success',['attribute'=>'row']);
        return redirectView(self::indexRoute, $msg);
    }
}
