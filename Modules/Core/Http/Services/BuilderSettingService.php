<?php
namespace Modules\Core\Http\Services;

use App\Http\Services\PsService;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Project;
use App\Config\ps_constant;

class BuilderSettingService extends PsService
{
    public function getProject(){
        $project = Project::first();

        return $project;
    }

    public function index() {
        $project = $this->getProject();
        $project['builder_url'] = ps_constant::builderDomain;

        $dataArr = [
            'builder_setting' => $project
        ];

        return $dataArr;
    }

    public function update($request, $id) {
        try{
            DB::beginTransaction();
            $builder_setting = Project::find($id);
            $builder_setting->project_url = $request->project_url;
            $builder_setting->token = $request->token;
            $builder_setting->update();

            $oldBuilderDomain = ps_constant::builderDomain;
            $newBuilderDomain = $request->builder_url;

            $file = '../app/Config/ps_constant.php';
            $code = file_get_contents($file);
            $new_const = str_replace('const builderDomain = "'.$oldBuilderDomain.'";', 'const builderDomain = "'.$newBuilderDomain.'";', $code);
            file_put_contents($file, $new_const);

            DB::commit();
        }catch (\Throwable $e){
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }

        return $builder_setting;
    }
}
