<?php

namespace Modules\PushNotificationMessage\Http\Controllers\Backend\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Constants\Constants;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Support\Renderable;
use Modules\Core\Http\Services\CoreFieldFilterSettingService;
use Modules\PushNotificationMessage\Http\Services\PushNotificationMessageService;
use Modules\PushNotificationMessage\Http\Requests\StorePushNotificationMessageRequest;

class PushNotificationMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */

    const parentPath = "push_notification_message/";
    const indexPath = self::parentPath."Index";
    const createPath = self::parentPath."Create";
    const editPath = self::parentPath."Edit";
    const indexRoute = "push_notification_message.index";
    const createRoute = "push_notification_message.create";
    const editRoute = "push_notification_message.edit";


    protected $pushNotificationMessageService, $successFlag, $dangerFlag, $code, $coreFieldFilterSettingService;

    public function __construct(PushNotificationMessageService $pushNotificationMessageService, CoreFieldFilterSettingService $coreFieldFilterSettingService)
    {
        $this->pushNotificationMessageService = $pushNotificationMessageService;
        $this->coreFieldFilterSettingService = $coreFieldFilterSettingService;
        $this->successFlag = Constants::success;
        $this->dangerFlag = Constants::danger;
        $this->code = Constants::pushNotificationMessage;
    }


    public function index(Request $request)
    {
        $dataArr = $this->pushNotificationMessageService->index(self::indexRoute,$request);
        // return $dataArr;
        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }

        return renderView(self::indexPath, $dataArr);
    }
    public function screenDisplayUiStore(Request $request) {



        $parameter = ['page' => $request->current_page];

        $this->pushNotificationMessageService->makeColumnHideShown($request);

        // $msg = 'ScreenDisplay UiSetting is updated.';
        $msg = __('core__be_screen_display_ui_updated');
        // return redirectView(self::indexRoute, $msg,null,$parameter);
        return redirect()->back();
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $dataArr = $this->pushNotificationMessageService->create(self::indexRoute);

        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }

        return renderView(self::createPath, $dataArr);

    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        // validation start
        $errors = validateForCustomField($this->code,$request->push_noti_msg_relation);

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

        if(in_array('message',$coreFieldsIds)){
            $validationArr['message'] = 'required';
        }

        $validationArr['description'] = 'required';
        $validationArr['cover'] = 'nullable|sometimes|image';

        $validator = Validator::make($request->all(),$validationArr);

        if ($validator->fails()) {
            return redirect()->route(self::createRoute)->with('push_noti_msg_relation_errors',$errors)
                ->withErrors($validator)
                ->withInput();
        } else {

            if (collect($errors)->isNotEmpty()){
                return redirect()->route(self::createRoute)->with('push_noti_msg_relation_errors',$errors);
            }
        }

        // validation end

        $noti_message = $this->pushNotificationMessageService->store($request);

        // if have error
        if (isset($noti_message['error'])){
            $msg = $noti_message['error'];
            return redirectView(self::createRoute, $msg, $this->dangerFlag);
        }

        return redirectView(self::indexRoute, $noti_message);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $dataArr = $this->pushNotificationMessageService->edit($id, self::indexRoute);

        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }

        return renderView(self::editPath, $dataArr);

    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $dataArr = $this->pushNotificationMessageService->destroy($id);

        $checkPermission = $dataArr["checkPermission"];
        if (!empty($checkPermission)){
            return $checkPermission;
        }

        // $msg = 'The '.$dataArr['title']." row has been deleted successfully.";
        $msg = __('core__be_delete_success',['attribute'=>$dataArr["title"]]);

        return redirectView(self::indexRoute, $msg);
    }
}
