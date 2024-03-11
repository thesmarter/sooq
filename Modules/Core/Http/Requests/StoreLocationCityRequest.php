<?php

namespace Modules\Core\Http\Requests;

use Illuminate\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Entities\CustomizeUi;

class StoreLocationCityRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|min:3|unique:psx_location_cities,name,',
            'lat' => 'required|numeric|max:90|min:-90',
            'lng' => 'required|numeric|max:180|min:-180',
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
            'name' => 'city name',
            'lat' => 'latitude',
            'lng' => 'longitude',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {

            $conds = [
                'module_name' => 'City',
                'enable' => 1,
                'mandatory' => 1
            ];
            $custom_headers = CustomizeUi::where($conds)->get();

            $custom_fields = $validator->getData()['custom_fields'] ?? '';

            foreach($custom_headers as $custom_header){
                if(array_key_exists($custom_header->id, $custom_fields) && empty($custom_fields[$custom_header->id])){
                    $validator->errors()->add($custom_header->id, 'The ' . strtolower($custom_header->name) . ' field is required.');
                }else if(!array_key_exists($custom_header->id, $custom_fields)){
                    $validator->errors()->add($custom_header->id, 'The ' . strtolower($custom_header->name) . ' field is required.');
                }
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
