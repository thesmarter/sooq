<?php

namespace Modules\Core\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBackendSettingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'backend_logo' => 'nullable|sometimes|image',
            'backend_login_image' => 'nullable|sometimes|image',
            'fav_icon' => 'nullable|sometimes|image',
            'backend_meta_image' => 'nullable|sometimes|image',
            'backend_water_mask_image' => 'nullable|sometimes|image',
            'water_mask_background'=>'nullable|sometimes|image',
            'is_watermask'=>'required',

        ];
    }

    protected function getValidatorInstance() {
        $validator = parent::getValidatorInstance();
        $validator->sometimes('opacity', 'required|integer|between:0,100', function($input) {
            return $input->is_watermask == 1;
        });
        $validator->sometimes('watermask_angle', 'required|integer|between:-360,360', function($input) {
            return $input->is_watermask == 1;
        });
        $validator->sometimes('padding', 'required|integer|between:0,1000', function($input) {
            return $input->is_watermask == 1;
        });
        $validator->sometimes('watermask_image_size', 'required|integer|between:10,1000', function($input) {
            return $input->is_watermask == 1;
        });
        return $validator;
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
