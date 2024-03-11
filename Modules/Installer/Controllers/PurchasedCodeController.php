<?php

namespace Modules\Installer\Controllers;

use App\Config\ps_constant;
use App\Rules\DomainCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Modules\Core\Entities\Project;
use Modules\Core\Http\Requests\UpdateLicenseRequest;


class PurchasedCodeController
{
    public function purchasedCode()
    {
        $project = Project::first();
        return view('vendor.installer.purchased-code')->with('project', $project);
    }

    public function purchasedCodeStore(UpdateLicenseRequest $request)
    {
        $project = Project::first();
        if (empty($project)) {
            $project = new Project();
            $project->project_name = "default";
            $project->project_code = $request->purchased_code;
            $project->project_url = $request->backend_url;
            $project->added_user_id = 1;
            $project->save();
        } else {
            $project->project_code = $request->purchased_code;
            $project->project_url = $request->backend_url;
            $project->update();
        }

        return redirect()->route('LaravelInstaller::userConfiguration');

    }
}
