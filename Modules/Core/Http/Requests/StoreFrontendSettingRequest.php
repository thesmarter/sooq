<?php

namespace Modules\Core\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\CoreFieldFilterSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;

class StoreFrontendSettingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'frontend_logo' => 'nullable|sometimes|image',
            'frontend_icon' => 'nullable|sometimes|image',
            'frontend_banner' => 'nullable|sometimes|image',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {

            $conds = [
                'module_name' => Constants::frontendSetting,
                'enable' => 1,
                'mandatory' => 1,
                'is_core_field' => 1,
            ];
            $core_headers = CoreFieldFilterSetting::where($conds)->get();

            $core_fields = $validator->getData() ?? '';


            foreach($core_headers as $core_header){
                if($core_header->field_name == 'frontend_banner'){
                    if(array_key_exists('frontend_banner', $core_fields) && empty($core_fields['frontend_banner'])){
                        $replace = [];
                        $replace['attribute'] = 'banner';
                        $validator->errors()->add('frontend_banner', __('validation__required', $replace));
                    }
                }
                if($core_header->field_name == 'frontend_logo'){
                    if(array_key_exists('frontend_logo', $core_fields) && empty($core_fields['frontend_logo'])){
                        $replace = [];
                        $replace['attribute'] = 'logo';
                        $validator->errors()->add('frontend_logo', __('validation__required', $replace));
                    }
                }
                if($core_header->field_name == 'frontend_icon'){
                    if(array_key_exists('frontend_icon', $core_fields) && empty($core_fields['frontend_icon'])){
                        $replace = [];
                        $replace['attribute'] = 'icon';
                        $validator->errors()->add('frontend_icon', __('validation__required', $replace));
                    }
                }

                if($core_header->field_name == 'backend_meta_image'){
                    if(array_key_exists('backend_meta_image', $core_fields) && empty($core_fields['backend_meta_image'])){
                        $replace = [];
                        $replace['attribute'] = 'meta_image';
                        $validator->errors()->add('backend_meta_image', __('validation__required', $replace));
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
