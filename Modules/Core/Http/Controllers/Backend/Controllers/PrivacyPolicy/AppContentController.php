<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\PrivacyPolicy;

use Inertia\Inertia;
use Illuminate\Routing\Controller;
use Modules\Core\Http\Services\PrivacyPolicyService;
use Modules\Core\Http\Services\ImageService;
use Modules\DataDeletionPolicy\Http\Services\DataDeletionPolicyService;

class AppContentController extends Controller
{

    const parentPath = "app_content/";
    const privacyPath = self::parentPath."privacy/Index";
    const dataDelectionPath = self::parentPath."data_deletion/Index";

    protected $privacyPolicyService;

    public function __construct(DataDeletionPolicyService $dataDeletionService,PrivacyPolicyService $privacyPolicyService)
    {
        $this->privacyPolicyService = $privacyPolicyService;
        $this->dataDeletionService = $dataDeletionService;

    }

    public function privacycontent()
    {
        $dataArr = $this->privacyPolicyService->privacycontent();
        return renderView(self::privacyPath, $dataArr);
    }

    public function datadelectioncontent()
    {
        $dataArr = $this->dataDeletionService->datadelectioncontent();
        return renderView(self::dataDelectionPath, $dataArr);
    }
}
