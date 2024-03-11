<?php

namespace Modules\Core\Http\Requests;

use App\Rules\HasRouteName;
use Illuminate\Foundation\Http\FormRequest;

class UpdateModuleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|min:3|unique:psx_modules,title,'.request()->route('module'),
            'route_name' => ['required', new HasRouteName(), 'unique:psx_modules,route_name,'.request()->route('module')],
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
