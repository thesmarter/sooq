<?php

namespace Modules\Template\PSXFETemplate\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Routing\Controller;
use Modules\Core\Http\Services\PrivacyPolicyService;
use Modules\Core\Http\Services\ImageService;
use Modules\DataDeletionPolicy\Http\Services\DataDeletionPolicyService;

class AppContentController extends Controller
{

    const parentPath = "Pages/vendor/views/";
    const privacyPath = self::parentPath."app_content/privacy/Index";
    const dataDelectionPath = self::parentPath."app_content/data_deletion/Index";

    protected $privacyPolicyService;

    public function __construct(DataDeletionPolicyService $dataDeletionService,PrivacyPolicyService $privacyPolicyService)
    {
        $this->privacyPolicyService = $privacyPolicyService;
        $this->dataDeletionService = $dataDeletionService;

    }

    public function privacycontent()
    {
        // $this->dataDeletionService->addMeta('title', 'This is Privacy page ');
        // $this->dataDeletionService->addMeta('description', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla sagittis libero vel mi convallis, in molestie ante vehicula. Proin pharetra egestas odio vel bibendum. Praesent eu feugiat augue. Mauris euismod egestas libero, ac imperdiet neque ullamcorper sed. Aliquam sed turpis tellus. Vestibulum facilisis pellentesque elit. Nullam in sagittis sapien, ut venenatis magna. Curabitur non tortor iaculis, pulvinar tortor ac, porta neque. Nulla facilisi. Nullam ultrices ligula ut diam fringilla congue. Curabitur sed consectetur ipsum. Vivamus viverra justo nulla, vitae fermentum odio dignissim nec. Vivamus euismod et mi vitae tempor. Sed quis interdum justo.');
        $dataArr = $this->privacyPolicyService->privacycontent();
        return renderView(self::privacyPath, $dataArr);
    }

    public function datadelectioncontent()
    {
        // $this->dataDeletionService->addMeta('title', 'This is DataDelection page of SmartTeam');
        // $this->dataDeletionService->addMeta('description', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla sagittis libero vel mi convallis, in molestie ante vehicula. Proin pharetra egestas odio vel bibendum. Praesent eu feugiat augue. Mauris euismod egestas libero, ac imperdiet neque ullamcorper sed. Aliquam sed turpis tellus. Vestibulum facilisis pellentesque elit. Nullam in sagittis sapien, ut venenatis magna. Curabitur non tortor iaculis, pulvinar tortor ac, porta neque. Nulla facilisi. Nullam ultrices ligula ut diam fringilla congue. Curabitur sed consectetur ipsum. Vivamus viverra justo nulla, vitae fermentum odio dignissim nec. Vivamus euismod et mi vitae tempor. Sed quis interdum justo.');
        $dataArr = $this->dataDeletionService->datadelectioncontent();
        return renderView(self::dataDelectionPath, $dataArr);
    }
}
