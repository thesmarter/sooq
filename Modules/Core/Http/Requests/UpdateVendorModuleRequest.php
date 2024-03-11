<?php

namespace Modules\Core\Http\Requests;

use App\Rules\HasRouteName;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVendorModuleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|min:3|unique:psx_vendor_modules,title,'.request()->route('vendor_module_registering'),
            'route_name' => ['required', new HasRouteName(), 'unique:psx_vendor_modules,route_name,'.request()->route('vendor_module_registering')],
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
