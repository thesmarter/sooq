<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\BuilderSetting;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Constants\Constants;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Support\Renderable;
use Modules\Core\Http\Services\BuilderSettingService;

class BuilderSettingController extends Controller
{
    const parentPath = "builder_setting/";
    const indexPath = self::parentPath."Index";
    const createPath = self::parentPath."Create";
    const editPath = self::parentPath."Edit";
    const indexRoute = "builder_setting.index";

    protected $builderSettingService, $dangerFlag;

    public function __construct(BuilderSettingService $builderSettingService){
        $this->builderSettingService = $builderSettingService;
        $this->dangerFlag = Constants::danger;
    }


    public function index()
    {
        $dataArr = $this->builderSettingService->index();
        // if(!empty($dataArr['builder_setting'])){
        //     return renderView(self::createPath, $dataArr);
        // }
        return renderView(self::editPath, $dataArr);
    }

    public function create()
    {
        return view('core::create');
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        return view('core::show');
    }

    public function edit($id)
    {
        return view('core::edit');
    }

    public function update(Request $request, $id)
    {
        $validationArr = [
            'token' => 'required'
        ];
        $attributes = [
            'token' => 'Token'
        ];
        $validator = Validator::make($request->all(),$validationArr,[], $attributes);

        if ($validator->fails()) {
            return redirect()->route(self::indexRoute)
                ->withErrors($validator)
                ->withInput();
        }

        $builder_setting = $this->builderSettingService->update($request, $id);

        // if have error
        if (isset($builder_setting['error'])){
            $msg = $builder_setting['error'];
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }

        $msg = __('core__be_backendsetting_updated');

        return redirect()->back();
    }

    public function destroy($id)
    {
        //
    }
}
