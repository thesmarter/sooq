<?php

namespace Modules\DataDeletionPolicy\Http\Controllers\Backend\Controllers;

use Inertia\Inertia;
use Illuminate\Routing\Controller;
use Modules\Core\Constants\Constants;
use Modules\DataDeletionPolicy\Entities\CoreDataDeletion;
use Modules\DataDeletionPolicy\Http\Services\DataDeletionPolicyService;
use Modules\DataDeletionPolicy\Http\Requests\StoreDataDeletionPolicyRequest;
use Modules\DataDeletionPolicy\Http\Requests\UpdateDataDeletionPolicyRequest;
use Illuminate\Http\Request;
use Modules\Core\Http\Services\ImageService;

class DataDeletionPolicyController extends Controller
{
    const parentPath = "data_deletion_policy/";
    const indexPath = self::parentPath . "Index";
    const createPath = self::parentPath . "Create";
    const editPath = self::parentPath . "Edit";
    const indexRoute = "data_deletion_policy.index";
    const createRoute = "data_deletion_policy.create";
    const editRoute = "data_deletion_policy.edit";

    protected $dataDeletionService, $successFlag, $dangerFlag,$imageService;

    public function __construct(DataDeletionPolicyService $dataDeletionService,ImageService $imageService)
    {
        $this->dataDeletionService = $dataDeletionService;
        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;
        $this->imageService = $imageService;
        $this->coverImgType = 'data_deletetion_policy';

    }

    public function index()
    {
        $dataArr = $this->dataDeletionService->index();

        if (empty($dataArr['data_deletion_policy'])) {
            return renderView(self::createPath);
        }
        return renderView(self::editPath, $dataArr);
    }


    public function store(StoreDataDeletionPolicyRequest $request)
    {

        $data_deletion_policy = $this->dataDeletionService->create($request);

        // if have error
        if ($data_deletion_policy) {
            $msg = $data_deletion_policy;
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }

        // $msg = 'The frontend configuration setting has been updated successfully.';
        $msg = __('core__be_config_success');
        return redirectView(self::indexRoute, $msg);
    }

    public function update(UpdateDataDeletionPolicyRequest $request,$id)
    {
        $data_deletion_policy = $this->dataDeletionService->update($id, $request);

        // if have error
        if ($data_deletion_policy) {
            $msg = $data_deletion_policy;
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }

        // $msg = 'The frontend configuration setting has been updated successfully.';
        $msg = __('core__be_config_success');
        return redirectView(self::indexRoute, $msg);
    }
    public function ckUpload(Request $request)
    {

        $imgParentId = 0 ;

        // save blog cover photo
        $url=$this->imageService->editorUpdateOrCreateImage($request, "upload", $imgParentId, $this->coverImgType);
        return response()->json([
            'uploaded' => true,
            'url' => $url,
        ]);
    }
}
