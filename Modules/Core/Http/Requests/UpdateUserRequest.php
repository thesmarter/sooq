<?php

namespace Modules\Core\Http\Requests;

use Illuminate\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\CustomizeUi;
use Modules\Core\Http\Services\CoreFieldFilterSettingService;

class UpdateUserRequest extends FormRequest
{
    protected $coreFieldFilterSettingService;
    public function __construct(CoreFieldFilterSettingService $coreFieldFilterSettingService)
    {
        $this->coreFieldFilterSettingService = $coreFieldFilterSettingService;
    }

    public function rules()
    {
        $customFieldValidations = [];

        $errors = validateForCustomField(Constants::user, $this->user_relation); //<= custom field that need to make mandatory start
        if(collect($errors)->isNotEmpty()){
            foreach($errors as $key=>$error){
                $customFieldValidationArr = array(
                    'fieldName' => $key,
                    'rules' => 'required',
                );
                array_push($customFieldValidations, $customFieldValidationArr);
            }
        }

        $coreFieldsIds = [];

        $cond['module_name'] = Constants::user;
        $cond['mandatory'] = 1;
        $cond['is_core_field'] = 1;
        $cond['enable'] = 1;

        $coreFields = $this->coreFieldFilterSettingService->getCoreFieldsWithConds($cond);

        foreach ($coreFields as $key => $value){
            if (str_contains($value->field_name,"@@")) {
                $originFieldName = strstr($value->field_name,"@@",true); // from language_id@@name to language_id
            } else {
                $originFieldName = $value->field_name;
            }
            array_push($coreFieldsIds,$originFieldName);

        }

        $validationArr = [];
        $coreFieldvalidations = array(
            array(
                'fieldName' => 'name',
                'rules' => 'required|min:3',
            ),
            array(
                'fieldName' => 'username',
                'rules' => 'required|unique:users,username,'.request()->route('user'),
            ),
            array(
                'fieldName' => 'user_phone',
                'rules' => 'required|unique:users,user_phone,'.request()->route('user'),
            ),
            array(
                'fieldName' => 'password',
                'rules' => 'sometimes|nullable|min:8',
            ),
            array(
                'fieldName' => 'conf_password',
                'rules' => 'sometimes|nullable|required_with:password|same:password|min:8',
            ),
            array(
                'fieldName' => 'email',
                'rules' => 'unique:users,email,' . request()->route('user'),
            ),
            array(
                'fieldName' => 'user_cover_photo',
                'rules' => 'nullable|sometimes|image',
            ),
       );
       $validations = collect($coreFieldvalidations)->merge(collect($customFieldValidations));

       foreach($validations as $validation){
            if(in_array($validation['fieldName'],$coreFieldsIds)){
                $validationArr[$validation['fieldName']] = $validation['rules'];
            } elseif(str_contains($validation['fieldName'], 'ps-')) {
                $validationArr[$validation['fieldName']] = $validation['rules'];
            } else {
                $validationArr[$validation['fieldName']] = 'nullable';
            }
       }
       
        return $validationArr;
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    // public function withValidator(Validator $validator)
    // {
    //     $validator->after(function ($validator) {

    //         $conds = [
    //             'module_name' => 'User',
    //             'enable' => 1,
    //             'mandatory' => 1
    //         ];
    //         $custom_headers = CustomizeUi::where($conds)->get();

    //         $custom_fields = $validator->getData()['custom_fields'] ?? '';

    //         foreach($custom_headers as $custom_header){
    //             if(array_key_exists($custom_header->id, $custom_fields) && empty($custom_fields[$custom_header->id])){
    //                 $validator->errors()->add($custom_header->id, 'The ' . strtolower($custom_header->name) . ' field is required.');
    //             }else if(!array_key_exists($custom_header->id, $custom_fields)){
    //                 $validator->errors()->add($custom_header->id, 'The ' . strtolower($custom_header->name) . ' field is required.');
    //             }
    //         }
    //     });
    // }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
