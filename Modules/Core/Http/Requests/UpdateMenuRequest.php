<?php

namespace Modules\Core\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMenuRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'module_name' => 'required|min:3|unique:psx_core_menus,module_name,'.request()->route('menu'),
            'core_sub_menu_group_id' => 'required',
            'module_desc' => 'required',
            'module_lang_key' => 'required',
            'module_id' => 'required|unique:psx_core_menus,module_id,'.request()->route('menu'),

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
