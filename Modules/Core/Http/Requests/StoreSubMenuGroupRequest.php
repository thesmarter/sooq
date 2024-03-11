<?php

namespace Modules\Core\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubMenuGroupRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'sub_menu_name' => 'required|min:3|unique:psx_core_sub_menu_groups,sub_menu_name,',
            'core_menu_group_id' => 'required',
            'icon_id'=>'required',
            'ordering'=>'integer'
        ];
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
