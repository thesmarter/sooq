<?php

namespace Modules\Core\Http\Controllers\Backend\Controllers\BackendSetting;

use App\Mail\TestMail;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Modules\Core\Constants\Constants;
use Illuminate\Support\Facades\Validator;
use Modules\Core\Http\Services\BackendSettingService;
use Modules\Core\Http\Requests\StoreBackendSettingRequest;
use Modules\Core\Http\Requests\UpdateBackendSettingRequest;
use Modules\Core\Http\Services\CoreFieldFilterSettingService;

class BackendSettingController extends Controller
{
    const parentPath = "backend_setting/";
    const indexPath = self::parentPath."Index";
    const createPath = self::parentPath."Create";
    const editPath = self::parentPath."Edit";
    const indexRoute = "backend_setting.index";

    protected $backendSettingService, $successFlag, $dangerFlag;

    public function __construct(BackendSettingService $backendSettingService,CoreFieldFilterSettingService $coreFieldFilterSettingService)
    {
        $this->backendSettingService = $backendSettingService;
        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;
        $this->code = Constants::backendSetting;
        $this->coreFieldFilterSettingService = $coreFieldFilterSettingService;
    }

    public function index()
    {
        $dataArr = $this->backendSettingService->index(self::indexRoute);

        // check permission
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }else {
            if(empty($dataArr['backend_setting'])){
                return renderView(self::createPath, $dataArr);
            }
            return renderView(self::editPath, $dataArr);
        }


    }

    public function store(StoreBackendSettingRequest $request)
    {

        $backend_setting = $this->backendSettingService->create($request);

        // if have error
        if (isset($backend_setting['error'])){
            $msg = $backend_setting['error'];
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }


        $msg = __('core__be_backendsetting_updated');
        return redirectView(self::indexRoute, $msg);
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        // validation start
        $errors = validateForCustomField($this->code,$request->backend_setting_relation);

        $coreFieldsIds = [];
        $errors = [];

        $cond['module_name'] = $this->code;
        $cond['mandatory'] = 1;
        $cond['is_core_field'] = 1;

        $coreFields = $this->coreFieldFilterSettingService->getCoreFieldsWithConds($cond);

        foreach ($coreFields as $key => $value){
            if (str_contains($value->field_name,"@@")) {
                $originFieldName = strstr($value->field_name,"@@",true);
            } else {
                $originFieldName = $value->field_name;
            }
            array_push($coreFieldsIds,$originFieldName);

        }

        $validationArr = [];

        if(in_array('backend_logo',$coreFieldsIds)){
            $validationArr['backend_logo'] = 'nullable|sometimes|image';
        }

        if(in_array('fav_icon',$coreFieldsIds)){
            $validationArr['fav_icon'] = 'nullable|sometimes|image';
        }

        if(in_array('backend_meta_image',$coreFieldsIds)){
            $validationArr['backend_meta_image'] = 'nullable|sometimes|image';
        }

        $validationArr['backend_login_image'] = 'nullable|sometimes|image';
        $validationArr['backend_water_mask_image'] = 'nullable|sometimes|image';
        $validationArr['water_mask_background'] = 'nullable|sometimes|image';
        $validationArr['is_watermask'] = 'required';

        // change custom attribute if required start
        $attributes['backend_logo'] = "Backend Logo";
        $attributes['fav_icon'] = "Fav Icon";
        $attributes['backend_meta_image'] = "Backend Meta Image";
        $attributes['backend_login_image'] = "Backend Login Image";
        $attributes['backend_water_mask_image'] = "Backend Water Mask Image";
        $attributes['water_mask_background'] = "Water Mask Background";
        $attributes['is_watermask'] = "Is Watermask";
        // change custom attribute if required end

        $validator = Validator::make($request->all(),$validationArr,[], $attributes);

        if ($validator->fails()) {
            return redirect()->route(self::indexRoute)->with('backend_setting_relation_errors',$errors)
                ->withErrors($validator)
                ->withInput();
        } else {

            if (collect($errors)->isNotEmpty()){
                return redirect()->route(self::indexRoute)->with('backend_setting_relation_errors',$errors);
            }
        }

        // validation end

        $backend_setting = $this->backendSettingService->update($id,$request);

        $coreFieldsIds = [];
        $errors = [];

        $cond['module_name'] = $this->code;
        $cond['mandatory'] = 1;
        $cond['is_core_field'] = 1;

        $coreFields = $this->coreFieldFilterSettingService->getCoreFieldsWithConds($cond);


        // $coreFields = CoreFieldFilterSetting::where('module_name',)->where('mandatory',1)->where('is_core_field',1)->get();
        foreach ($coreFields as $key => $value){
            if (str_contains($value->field_name,"@@")) {
                $originFieldName = strstr($value->field_name,"@@",true);
            } else {
                $originFieldName = $value->field_name;
            }
            array_push($coreFieldsIds,$originFieldName);

        }

        $validationArr = [];

        // if have error
        if (isset($backend_setting['error'])){
            $msg = $backend_setting['error'];
            return redirectView(self::indexRoute, $msg, $this->dangerFlag);
        }

        // $msg = 'The backend configuration setting has been updated successfully.';
        $msg = __('core__be_backendsetting_updated');

        return redirect()->back();

        // return redirectView(self::indexRoute, $msg);
    }

    public function checkSmtpConfig(Request $request){
        $request->validate([
           "email" => "required|email"
        ]);
        $mailData = [
            'title' => 'Mail from Smart Team',
            'body' => 'This is for testing email using smtp.'
        ];
        // try {
            // $test=Mail::to($request->email);
            // dd($test);
            Mail::to($request->email)->send(new TestMail($mailData));
            $msg = "Smtp Configuration is finished Successfully";
            return redirectView(null, $msg);
        // } catch (\Throwable $e) {
        //     $msg = $e->getMessage();
        //     return redirectView(null, $msg, "danger");
        // }
    }

    public function languageRefresh(){
        $msg = "Smtp Configuration is finished Successfully";
        generateAllLanguage();
        return redirectView(self::indexRoute, $msg, "langSuccess");
    }

}
