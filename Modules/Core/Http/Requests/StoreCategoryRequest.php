<?php

namespace Modules\Core\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\CoreFieldFilterSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;

class StoreCategoryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $module = Constants::category;
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {

            $conds = [
                'module_name' => Constants::category,
                'enable' => 1,
                'mandatory' => 1,
                'is_core_field' => 1,
            ];
            $core_headers = CoreFieldFilterSetting::where($conds)->get();

            $core_fields = $validator->getData() ?? '';

            // echo json_encode($core_headers);
            // echo json_encode($core_fields);exit;

            foreach($core_headers as $core_header){
                if($core_header->field_name == 'cat_photo'){
                    if(array_key_exists('cover', $core_fields) && empty($core_fields['cover'])){
                        $replace = [];
                        $replace['attribute'] = 'photo';
                        $validator->errors()->add('cover', __('validation__required', $replace));
                    }
                }
                if($core_header->field_name == 'cat_icon'){
                    if(array_key_exists('icon', $core_fields) && empty($core_fields['icon'])){
                        $replace = [];
                        $replace['attribute'] = 'icon';
                        $validator->errors()->add('icon', __('validation__required', $replace));
                    }
                }

                // if(array_key_exists($core_header->field_name, $core_fields) && empty($core_fields[$core_header->field_name])){
                //     $validator->errors()->add($core_header->field_name, 'The ' . strtolower($core_header->field_name) . ' field is required.');
                // }
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
