<?php

namespace Modules\DemoDataDeletion\Http\Controllers\Backend\Controllers;

use App\Config\ps_constant;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\DemoDataDeletion\Constants\Constants;
use Modules\DemoDataDeletion\Http\Services\DemoDataDeletionService;

class DemoDataDeletionController extends Controller
{
    const parentPath = "demo_data_deletion/";
    const indexPath = self::parentPath . "Index";
    const indexRoute = "demo_data_deletion.index";

    protected $demoDataDeletionService;

    public function __construct(DemoDataDeletionService $demoDataDeletionService)
    {
        $this->demoDataDeletionService = $demoDataDeletionService;
    }

    public function index()
    {
        if (!permissionControl(Constants::dataReset,ps_constant::readPermission)){
            return redirect()->route('admin.index');
        }
        $dataArr = $this->demoDataDeletionService->index();
        return renderView(self::indexPath, $dataArr);
    }

    public function destroy()
    {
        if (!permissionControl(Constants::dataReset,ps_constant::deletePermission)){
            return redirect()->route('admin.index');
        }
        $dataArr = $this->demoDataDeletionService->destroy();
        return renderView(self::indexPath, $dataArr);

    }
}
