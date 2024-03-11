<?php

namespace Modules\Blog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\CustomizeUi;
use Modules\Core\Http\Services\CoreFieldFilterSettingService;

class UpdateBlogRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected $coreFieldFilterSettingService;
    public function __construct(CoreFieldFilterSettingService $coreFieldFilterSettingService)
    {
        $this->coreFieldFilterSettingService = $coreFieldFilterSettingService;
    }

    public function rules()
    {
        $module = Constants::blog;
        $customFieldData = $this->blog_relation;

        // prepare for custom field validation
        $errors = validateForCustomField($module, $customFieldData);

        // prepare for core field validation
        $cond = condsForCoreFieldValidation($module);
        $coreFields = $this->coreFieldFilterSettingService->getCoreFieldsWithConds($cond);
        $validations = array(
            array(
                'fieldName' => 'name',
                'rules' => 'required|unique:psx_blogs,name,'.$this->blog,
            ),
            array(
                'fieldName' => 'location_city_id',
                'rules' => 'required',
            ),
            array(
                'fieldName' => 'description',
                'rules' => 'required',
            ),
            array(
                'fieldName' => 'blog_photo',
                'rules' => 'nullable|sometimes|image',
            ),
            array(
                'fieldName' => 'about_phone',
                'rules' => 'nullable',
            ),
            array(
                'fieldName' => 'status',
                'rules' => 'nullable',
            ),
            array(
                'fieldName' => 'added_user_id',
                'rules' => 'nullable',
            ),
        );

        // prepared saturation for core and custom field
        $validationArr = handleValidation($errors, $coreFields, $validations);
        return $validationArr;

    }

    public function attributes()
    {

        $customFieldAttributeArr = handleCFAttrForValidation(Constants::blog, $this->blog_relation);

        $coreFieldAttributeArr = [
            'name' => "Blog Name",
            'location_city_id' => "Location City",
        ];
        $attributeArr = array_merge($coreFieldAttributeArr, $customFieldAttributeArr);

        return $attributeArr;
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
