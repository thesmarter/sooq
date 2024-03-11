<?php

namespace Modules\ContactUsMessage\Http\Services;

use App\Config\ps_constant;
use App\Http\Services\PsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\ContactUsMessage\Entities\Contact;
use Modules\ContactUsMessage\Exports\ContactUsMessageExport;
use Modules\ContactUsMessage\Transformers\Api\App\V1_0\GetInTouchApiResource;
use Modules\ContactUsMessage\Transformers\Backend\Model\ContactUsMessage\ContactUsMessageWithKeyResource;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\CoreFieldFilterSetting;
use Modules\Core\Entities\CustomizeUi;
use Modules\Core\Entities\ScreenDisplayUiSetting;
use Modules\Core\Http\Services\AboutService;
use Modules\Core\Http\Services\UserAccessApiTokenService;

class ContactService extends PsService
{

    protected $loginUserIdParaApi, $userAccessApiTokenService, $forbiddenStatusCode, $deviceTokenParaApi, $aboutService, $contactIdCol, $contactMsgCsvFileName, $successStatus, $createdStatusCode, $internalServerErrorStatusCode, $show, $hide, $delete, $unDelete, $viewAnyAbility, $createAbility, $editAbility, $deleteAbility, $code, $screenDisplayUiKeyCol, $screenDisplayUiIdCol, $screenDisplayUiIsShowCol, $coreFieldFilterModuleNameCol, $customUiIsDelCol, $yes, $no;


    public function __construct(AboutService $aboutService, UserAccessApiTokenService $userAccessApiTokenService)
    {
        $this->aboutService = $aboutService;
        $this->userAccessApiTokenService = $userAccessApiTokenService;
        $this->loginUserIdParaApi = ps_constant::loginUserIdParaFromApi;
        $this->deviceTokenParaApi = ps_constant::deviceTokenKeyFromApi;

        $this->contactIdCol = Contact::id;
        $this->contactIsReadCol = Contact::isRead;
        $this->contactIsSeenCol = Contact::isSeen;
        $this->contactMsgCsvFileName = "contact_us_message";

        $this->successStatus = Constants::successStatus;
        $this->internalServerErrorStatusCode = Constants::internalServerErrorStatusCode;
        $this->createdStatusCode = Constants::createdStatusCode;
        $this->forbiddenStatusCode = Constants::forbiddenStatusCode;

        $this->show = Constants::show;
        $this->no = Constants::no;
        $this->yes = Constants::yes;
        $this->hide = Constants::hide;
        $this->delete = Constants::delete;
        $this->unDelete = Constants::unDelete;
        $this->successFlag = Constants::success;

        $this->viewAnyAbility = Constants::viewAnyAbility;
        $this->createAbility = Constants::createAbility;
        $this->editAbility = Constants::editAbility;
        $this->deleteAbility = Constants::deleteAbility;
        $this->code = Constants::contact;

        $this->screenDisplayUiKeyCol = ScreenDisplayUiSetting::key;
        $this->screenDisplayUiIdCol = ScreenDisplayUiSetting::id;
        $this->screenDisplayUiIsShowCol = ScreenDisplayUiSetting::isShow;

        $this->coreFieldFilterModuleNameCol = CoreFieldFilterSetting::moduleName;

        $this->customUiIsDelCol = CustomizeUi::isDelete;

        $this->forbiddenStatusCode = Constants::forbiddenStatusCode;

        $this->loginUserIdParaApi = ps_constant::loginUserIdParaFromApi;
        $this->deviceTokenParaApi = ps_constant::deviceTokenKeyFromApi;

        $this->userAccessApiTokenService = $userAccessApiTokenService;
    }

    public function store($request)
    {
        DB::beginTransaction();

        try {
            $contact = new Contact();

            if (isset($request->contact_name) && !empty($request->contact_name))
                $contact->contact_name = $request->contact_name;

            if (isset($request->contact_email) && !empty($request->contact_email))
                $contact->contact_email = $request->contact_email;

            if (isset($request->contact_phone) && !empty($request->contact_phone))
                $contact->contact_phone = $request->contact_phone;

            if (isset($request->contact_message) && !empty($request->contact_message))
                $contact->contact_message = $request->contact_message;

            if (isset($request->login_user_id) && !empty($request->login_user_id))
                $contact->added_user_id = $request->login_user_id;
            else
                $contact->added_user_id = Auth::user()->id;

            $contact->save();

            DB::commit();
            return $contact;
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }

        return $contact;
    }

    public function update($id, $request)
    {
        DB::beginTransaction();

        try {
            $contact = $this->getContact($id);

            if (isset($request->contact_name) && !empty($request->contact_name))
                $contact->contact_name = $request->contact_name;

            if (isset($request->contact_email) && !empty($request->contact_email))
                $contact->contact_email = $request->contact_email;

            if (isset($request->contact_phone) && !empty($request->contact_phone))
                $contact->contact_phone = $request->contact_phone;

            if (isset($request->contact_message) && !empty($request->contact_message))
                $contact->contact_message = $request->contact_message;

            if (isset($request->updated_user_id) && !empty($request->updated_user_id))
                $contact->updated_user_id = $request->updated_user_id;
            else
                $contact->updated_user_id = Auth::user()->id;

            $contact->save();

            $contact->update();

            DB::commit();
            return $contact;
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }


    public function getContacts($relation = null, $conds = null, $limit = null, $offset = null)
    {

        $contacts = Contact::when($relation, function ($q, $relation) {
                        $q->with($relation);
                    })
                    ->when($conds, function ($q, $conds) {
                        $q->where($conds);
                    })
                    ->when($limit, function ($query,$limit){
                        $query->limit($limit);
                    })
                    ->when($offset, function ($query,$offset){
                        $query->offset($offset);
                    })
                    ->orderBy('id', 'desc')
                    ->get();
        return $contacts;
    }

    public function markAllAsRead(){

        $contacts = Contact::where($this->contactIsReadCol, 0)->update([$this->contactIsReadCol => 1]);
//        $contacts = Contact::where($this->contactIsReadCol, 0)->get();

//        foreach ($contacts as $contact){
//            $contact->is_read = 1;
//            $contact->update();
//        }
    }

    public function markAllAsSeen(){

        $contacts = Contact::where($this->contactIsSeenCol, 0)->update([$this->contactIsSeenCol => 1]);

//        foreach ($contacts as $contact){
//            $contact->is_seen = 1;
//            $contact->update();
//        }

    }

    public function countUnseen(){
        return Contact::where($this->contactIsSeenCol, 0)->count();
    }

    public function multiDelete($ids){
        $contacts = Contact::whereIn($this->contactIdCol, $ids)->get();
        $contactIds = $contacts->pluck($this->contactIdCol);
        Contact::destroy($contactIds);
//        foreach ($contacts as $contact){
//            $contact->delete();
//        }
    }

    public function getContact($relation = null, $id = null)
    {

        $contact = Contact::when($relation, function ($q, $relation) {
            $q->with($relation);
        })
            ->when($id, function ($q, $id) {
                $q->where($this->contactIdCol, $id);
            })->first();
        return $contact;
    }

    public function getContactsFormNav(){
        $contactRelation = ['owner', 'editor'];
        $contacts = ContactUsMessageWithKeyResource::collection($this->getContacts($contactRelation,null,5,0));

        $count = $this->countUnseen();

        $this->markAllAsSeen();

        $dataArr = [
            "contacts" => $contacts,
            "unseenCount" => $count
        ];

        return $dataArr;
    }
    public function destroy($id){

        $contact = $this->getContact(null,$id);
        $checkPermission = $this->checkPermission($this->deleteAbility, $contact, "admin.index");

        $contact->delete();

        $msg = 'The Contact has been deleted successfully.';
        $dataArr = [
            'checkPermission' => $checkPermission,
            "msg" => $msg,
            "flag" => $this->successFlag
        ];
        return $dataArr;
    }


    // -----------
    public function index(){
        // check permission
        $checkPermission = $this->checkPermission($this->viewAnyAbility, Contact::class, "admin.index");

        $contactRelation = ['owner', 'editor'];
        $this->markAllAsSeen();
        $contacts = ContactUsMessageWithKeyResource::collection($this->getContacts($contactRelation));

        // taking for column and columnFilterOption
        $columnAndColumnFilter = $this->takingForColumnAndFilterOption();
        $showContactCols = $columnAndColumnFilter['showCoreField'];
        $columnProps = $columnAndColumnFilter['arrForColumnProps'];
        $columnFilterOptionProps = $columnAndColumnFilter['arrForColumnFilterProps'];

        $dataArr = [
            'checkPermission' => $checkPermission,
            'contacts' => $contacts,
            'showContactCols' => $showContactCols,
            'showCoreAndCustomFieldArr' => $columnProps,
            'hideShowFieldForFilterArr' => $columnFilterOptionProps,
        ];
        return $dataArr;
    }

    public function edit($id){
        $contactRelation = ['owner'];
        $contact = $this->getContact($contactRelation, $id);

        $checkPermission = $this->checkPermission($this->editAbility,$contact, "admin.index");

        $contact->is_read = 1;
        $contact->is_seen = 1;
        $contact->update();
        $contact = new ContactUsMessageWithKeyResource($contact);
        $dataArr = [
            'contact' => $contact,
            'checkPermission' => $checkPermission,
        ];
        return $dataArr;
    }

    public function csvExport(){

        $filename = newFileNameForExport($this->contactMsgCsvFileName);
        return (new ContactUsMessageExport)->download($filename, \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function takingForColumnProps($label, $field, $type, $isShowSorting, $ordering){
        $hideShowCoreAndCustomFieldObj = new \stdClass();
        $hideShowCoreAndCustomFieldObj->label = $label;
        $hideShowCoreAndCustomFieldObj->field = $field;
        $hideShowCoreAndCustomFieldObj->type = $type;
        $hideShowCoreAndCustomFieldObj->sort = $isShowSorting;
        $hideShowCoreAndCustomFieldObj->ordering = $ordering;
        if ($type !== "Image" && $type !== "MultiSelect" && $type !== "Action"){
        $hideShowCoreAndCustomFieldObj->action = 'Action';
        }

        return $hideShowCoreAndCustomFieldObj;
    }

    public function takingForColumnAndFilterOption(){

        // for table
        $hideShowCoreFieldForColumnArr = [];
        $hideShowCustomFieldForColumnArr = [];

        $showProductCols = [];

        // for eye
        $hideShowFieldForColumnFilterArr = [];

        // for control
        $controlFieldArr = [];
        $controlFieldObj = $this->takingForColumnProps(__('core__be_action'), "action", "Action", false, 0);
        array_push($controlFieldArr, $controlFieldObj);

        $code = $this->code;
        $hiddenForCoreAndCustomField = $this->hiddenShownForCoreAndCustomField($this->hide, $code);
        $shownForCoreAndCustomField = $this->hiddenShownForCoreAndCustomField($this->show, $code);
        $hideShowForCoreAndCustomFields = $shownForCoreAndCustomField->merge($hiddenForCoreAndCustomField);

        foreach ($hideShowForCoreAndCustomFields as $showFields){
        if ($showFields->coreField !== null) {

            $label = $showFields->coreField->label_name;
                $field = $showFields->coreField->field_name;
                $colName = $showFields->coreField->field_name;
                $type = $showFields->coreField->data_type;
                $isShowSorting = $showFields->coreField->is_show_sorting;
                $ordering = $showFields->coreField->ordering;

                $cols = $colName;
                $id = $showFields->id;
                $hidden = $showFields->is_show ? false : true;
                $moduleName = $showFields->coreField->module_name;
                $keyId = $showFields->coreField->id;

            $coreFieldObjForColumnsProps = $this->takingForColumnProps($label, $field, $type,$isShowSorting, $ordering);
            $coreFieldObjForColumnFilter = $this->takingForColumnFilterProps($id, $label, $field, $hidden, $moduleName, $keyId);

            array_push($hideShowFieldForColumnFilterArr, $coreFieldObjForColumnFilter);
            array_push($hideShowCoreFieldForColumnArr, $coreFieldObjForColumnsProps);
            array_push($showProductCols, $cols);

        }if ($showFields->customizeField !== null) {

            $label = $showFields->customizeField->name;
                $uiHaveAttribute = [$this->dropDownUi, $this->radioUi];
                if (in_array($showFields->customizeField->ui_type_id, $uiHaveAttribute)){
                $field = $showFields->customizeField->core_keys_id."@@name";
                } else {
                $field = $showFields->customizeField->core_keys_id;
                }
                $type = $showFields->customizeField->data_type;
                $isShowSorting = $showFields->customizeField->is_show_sorting;
                $ordering = $showFields->customizeField->ordering;

                $id = $showFields->id;
                $hidden = $showFields->is_show ? false : true;
                $moduleName = $showFields->customizeField->module_name;
                $keyId = $showFields->customizeField->core_keys_id;

            $customFieldObjForColumnsProps = $this->takingForColumnProps($label, $field, $type ,$isShowSorting, $ordering);
            $customFieldObjForColumnFilter = $this->takingForColumnFilterProps($id, $label, $field, $hidden, $moduleName, $keyId);

            array_push($hideShowFieldForColumnFilterArr, $customFieldObjForColumnFilter);
            array_push($hideShowCustomFieldForColumnArr, $customFieldObjForColumnsProps);

        }
        }

        // for columns props
        $showCoreAndCustomFieldArr = array_merge($hideShowCoreFieldForColumnArr, $controlFieldArr, $hideShowCustomFieldForColumnArr);
        $sortedColumnArr = columnOrdering("ordering", $showCoreAndCustomFieldArr);

        // for eye
        $hideShowCoreAndCustomFieldArr = array_merge($hideShowFieldForColumnFilterArr);

        $dataArr = [
        "arrForColumnProps" => $sortedColumnArr,
        "arrForColumnFilterProps" => $hideShowCoreAndCustomFieldArr,
        "showCoreField" => $showProductCols,
        ];
        return $dataArr;

    }

    public function hiddenShownForCoreAndCustomField($isShown, $code){
        $hiddenShownForFields = ScreenDisplayUiSetting::with(['customizeField' => function ($q){
        $q->where($this->customUiIsDelCol, $this->unDelete);
        }, 'coreField'=> function($q){
            $q->where($this->coreFieldFilterModuleNameCol, $this->code);
        }])
        ->where($this->coreFieldFilterModuleNameCol, $code)
        ->where($this->screenDisplayUiIsShowCol, $isShown)
        ->get();
        return $hiddenShownForFields;
    }

    public function takingForColumnFilterProps($id, $label, $field, $hidden, $moduleName, $keyId){
        $hideShowCoreAndCustomFieldObj = new \stdClass();
        $hideShowCoreAndCustomFieldObj->id = $id;
        $hideShowCoreAndCustomFieldObj->label = $label;
        $hideShowCoreAndCustomFieldObj->key = $field;
        $hideShowCoreAndCustomFieldObj->hidden = $hidden;
        $hideShowCoreAndCustomFieldObj->module_name = $moduleName;
        $hideShowCoreAndCustomFieldObj->key_id = $keyId;

        return $hideShowCoreAndCustomFieldObj;
    }

    // for api
    public function contactFromApi($request)
    {
        // check permission start
        // $deviceToken = $request->header($this->deviceTokenParaApi);
        // $userAccessApiToken = $this->userAccessApiTokenService->getUserAccessApiToken( $request->login_user_id, $deviceToken);

        // if (empty($userAccessApiToken)){
        //     $msg = __('core__api_update_no_permission',[],$request->language_symbol);
        //     return ['error' =>  $msg,'status' =>  $this->forbiddenStatusCode ];
        // }
        // check permission en

        $contact = $this->store($request);

        if(isset($contact['error'])){
            return  ['error' => __('contact_us__api_db_error',[],$request->language_symbol) ,'status' =>  $this->internalServerErrorStatusCode ];
        }

        $contactNameStr = __('contact_us__api_name') ;
        $contactEmailStr = __('contact_us__api_email') ;
        $contactPhoneStr = __('contact_us__api_phone') ;
        $contactMessageStr = __('contact_us__api_message') ;
        // start send email to admin
        $msg = $contact->contact_message;

        if(!sendContactMail($contact->contact_name, $contact->contact_email, $contact->contact_phone, $msg,$contactNameStr,$contactEmailStr,$contactPhoneStr,$contactMessageStr)){
            return  ['error' => __('contact__email_not_sent',[],$request->language_symbol) ,'status' =>  $this->internalServerErrorStatusCode ];
        }
        // end send email to to_user_id

        return $contact;
    }

    public function getInTouchForContactFromApi($request){
        /// check permission start
        $deviceToken = $request->header($this->deviceTokenParaApi);
        $userId = $request->login_user_id;

        // user token layer permission start
        $userAccessApiToken = $this->userAccessApiTokenService->getUserAccessApiToken($userId, $deviceToken);
        // user token layer permission end

        /// check permission end

        // if (empty($userAccessApiToken)){
        //     $msg = __('contact__api_no_permission',[],$request->language_symbol);
        //     return responseMsgApi($msg, $this->forbiddenStatusCode);
        // }else{
            $contact = new GetInTouchApiResource($this->aboutService->getAbout());
            return $contact;
        // }
    }
}
