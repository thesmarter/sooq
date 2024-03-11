<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\PrivacyPolicy;

use Inertia\Inertia;
use Illuminate\Routing\Controller;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\CorePrivacyPolicy;
use Modules\Core\Http\Services\PrivacyPolicyService;
use Modules\Core\Http\Requests\StorePrivacyPolicyRequest;
use Modules\Core\Http\Requests\UpdatePrivacyPolicyRequest;
use Illuminate\Http\Request;
use Modules\Core\Http\Services\ImageService;

class PrivacyPolicyController extends Controller
{

    const parentPath = "privacy_policy/";
    const indexPath = self::parentPath."Index";
    const createPath = self::parentPath."Create";
    const editPath = self::parentPath."Edit";
    const indexRoute = "privacy_policy.index";
    const createRoute = "privacy_policy.create";
    const editRoute = "privacy_policy.edit";

    protected $privacyPolicyService, $dangerFlag,$imageService;

    public function __construct(PrivacyPolicyService $privacyPolicyService,ImageService $imageService)
    {
        $this->privacyPolicyService = $privacyPolicyService;
        $this->dangerFlag = Constants::danger;
        $this->imageService = $imageService;
        $this->coverImgType = 'privacy_policy';
    }

    public function index()
    {
        $dataArr = $this->privacyPolicyService->index();
        if(empty($dataArr)){
            return renderView(self::createPath);
        }
        return renderView(self::editPath, $dataArr);
    }

    public function store(StorePrivacyPolicyRequest $request)
    {
        $currency = $this->privacyPolicyService->store($request);

        // if have error
        if ($currency){
            $msg = $currency;
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }

        // $msg = 'The privacy policy has been updated successfully.';
        $msg = __('core__be_updated',['attribute'=>'privacy policy']);
        return redirectView(self::indexRoute, $msg);
    }

    public function update( UpdatePrivacyPolicyRequest $request)
    {
        $currency = $this->privacyPolicyService->update(1, $request);

        // if have error
        if ($currency){
            $msg = $currency;
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }

        // $msg = 'The privacy policy has been updated successfully.';
        $msg = __('core__be_updated',['attribute'=>'privacy policy']);
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
