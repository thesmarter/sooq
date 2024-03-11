<?php

namespace Modules\Core\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMobileLanguageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'symbol' => 'required|unique:psx_mobile_languages,symbol',
            'name' => 'required|unique:psx_mobile_languages,name',
            'languageCode' => 'required|unique:psx_mobile_languages,language_code',
            'countryCode' => 'required|unique:psx_mobile_languages,country_code',
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
