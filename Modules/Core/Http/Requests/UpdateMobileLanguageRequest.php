<?php

namespace Modules\Core\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMobileLanguageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'symbol' => 'required|unique:psx_mobile_languages,symbol,'.$this->route('mobile_language'),
            'name' => 'required',
            'languageCode' => 'required|unique:psx_mobile_languages,language_code,'.$this->route('mobile_language'),
            'countryCode' => 'required|unique:psx_mobile_languages,country_code,'.$this->route('mobile_language'),
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
