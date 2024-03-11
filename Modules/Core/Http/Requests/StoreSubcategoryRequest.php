<?php

namespace Modules\Core\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\CoreFieldFilterSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;
use API;
use Illuminate\Support\Arr;

class StoreSubcategoryRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|min:3|unique:psx_subcategories,name,'.request()->id,
            'category_id' => 'required',
            "cover" => "nullable|sometimes|image",
            "icon" => "nullable|sometimes|image",
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Sub Category',
            'category_id' => 'Category',
            'cover' => 'Cover Image',
            'icon' => 'Icon Image'
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {

            $conds = [
                'module_name' => Constants::subcategory,
                'enable' => 1,
                'mandatory' => 1,
                'is_core_field' => 1,
            ];
            $core_headers = CoreFieldFilterSetting::where($conds)->get();

            $core_fields = $validator->getData() ?? '';

            // echo json_encode($core_headers);
            // echo json_encode($core_fields);exit;
            
            foreach($core_headers as $core_header){
                if($core_header->field_name == 'sub_cat_photo'){
                    if(array_key_exists('cover', $core_fields) && empty($core_fields['cover'])){
                        $replace = [];
                        $replace['attribute'] = 'photo';
                        $validator->errors()->add('cover', __('validation__required', $replace));
                    }
                }
                if($core_header->field_name == 'sub_cat_icon'){
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

    public function authorize()
    {
        return true;
    }
}
