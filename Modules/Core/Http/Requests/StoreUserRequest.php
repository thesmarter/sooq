<?php

namespace Modules\Core\Http\Requests;

use Illuminate\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Modules\Core\Entities\CustomizeUi;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\CoreFieldFilterSetting;

class StoreUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|min:3',
            'password' => 'required|confirmed|'. Password::min(8)->mixedCase(),
            'conf_password' => 'required_with:password|same:password|min:8',
            'email' => 'required|unique:users,email',
            "user_cover_photo" => "nullable|sometimes|image",
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'conf_password' => 'confirm password',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {

            $conds = [
                'module_name' => 'User',
                'enable' => 1,
                'mandatory' => 1
            ];
            $custom_headers = CustomizeUi::where($conds)->get();

            $custom_fields = $validator->getData()['custom_fields'] ?? '';

            foreach($custom_headers as $custom_header){
                if(array_key_exists($custom_header->id, $custom_fields) && empty($custom_fields[$custom_header->id])){
                    $replace = [];
                    $replace['attribute'] = strtolower($custom_header->name);
                    $validator->errors()->add($custom_header->id, $replace);
                }else if(!array_key_exists($custom_header->id, $custom_fields)){
                    $replace = [];
                    $replace['attribute'] = strtolower($custom_header->name);
                    $validator->errors()->add($custom_header->id, $replace);
                }
            }

            $conds = [
                'module_name' => Constants::user,
                'enable' => 1,
                'mandatory' => 1,
                'is_core_field' => 1,
            ];
            $core_headers = CoreFieldFilterSetting::where($conds)->get();

            $core_fields = $validator->getData() ?? '';
            
            foreach($core_headers as $core_header){
                if($core_header->field_name == 'user_cover_photo'){
                    if(array_key_exists('user_cover_photo', $core_fields) && empty($core_fields['user_cover_photo'])){
                        $replace = [];
                        $replace['attribute'] = 'photo';
                        $validator->errors()->add('user_cover_photo', __('validation__required', $replace));
                    }
                }
            }
        });
    }

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
