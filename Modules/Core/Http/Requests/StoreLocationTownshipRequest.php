<?php

namespace Modules\Core\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLocationTownshipRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|min:3|unique:psx_location_townships,name,',
            'lat' => 'required|numeric|max:90|min:-90',
            'lng' => 'required|numeric|max:180|min:-180',
            'location_city_id' => 'required',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => 'township name',
            'lat' => 'latitude',
            'lng' => 'longitude',
            'location_city_id' => 'location city'
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
