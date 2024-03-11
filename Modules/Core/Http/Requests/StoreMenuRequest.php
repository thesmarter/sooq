<?php

namespace Modules\Core\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'module_name' => 'required|min:3|unique:psx_core_menus,module_name,',
            'core_sub_menu_group_id' => 'required',
            'module_desc' => 'required|unique:psx_core_menus,module_desc',
            'module_lang_key' => 'required|unique:psx_core_menus,module_lang_key',
            'module_id' => 'required|unique:psx_core_menus,module_id',
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
