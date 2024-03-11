<?php

namespace Modules\Core\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\CustomizeUi;
use Modules\Core\Http\Services\CoreFieldFilterSettingService;
use Modules\Core\Http\Services\SystemConfigService;
use stdClass;

class StoreItemRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected $coreFieldFilterSettingService, $systemConfigService;
    public function __construct(CoreFieldFilterSettingService $coreFieldFilterSettingService, SystemConfigService $systemConfigService)
    {
        $this->coreFieldFilterSettingService = $coreFieldFilterSettingService;
        $this->systemConfigService = $systemConfigService;

    }

    public function rules()
    {
        $selcted_array = $this->systemConfigService->getSystemSettingJson();

        $errors = validateForCustomField(Constants::item, $this->product_relation);

        $cond['module_name'] = Constants::item;
        $cond['mandatory'] = 1;
        $cond['is_core_field'] = 1;
        $cond['enable'] = 1;

        $coreFields = $this->coreFieldFilterSettingService->getCoreFieldsWithConds($cond);

        $validations = array(
            array(
                'fieldName' => 'item_image',
                'rules' => 'required',
            ),
            array(
                'fieldName' => 'title',
                'rules' => 'required|min:3|unique:psx_items,title,' . $this->route('item'),
            ),
            array(
                'fieldName' => 'description',
                'rules' => 'required|min:10',
            ),
            array(
                'fieldName' => 'category_id',
                'rules' => 'required|exists:psx_categories,id',
            ),
            array(
                'fieldName' => 'subcategory_id',
                'rules' => 'required|exists:psx_subcategories,id',
            ),
            array(
                'fieldName' => 'location_city_id',
                'rules' => 'required|exists:psx_location_cities,id',
            ),
            array(
                'fieldName' => 'location_township_id',
                'rules' => 'required|exists:psx_location_townships,id',
            ),
            array(
                'fieldName' => 'currency_id',
                'rules' => $selcted_array['selected_price_type']['id'] == "NORMAL_PRICE" ? 'required|exists:psx_currencies,id' : 'nullable',
            ),
            array(
                'fieldName' => 'original_price',
                'rules' => 'required|max:6',
            ),
            array(
                'fieldName' => 'percent',
                'rules' => 'required',
            ),
            array(
                'fieldName' => 'lat',
                'rules' => 'required',
            ),
            array(
                'fieldName' => 'lng',
                'rules' => 'required',
            ),
            array(
                'fieldName' => 'shop_id',
                'rules' => 'required',
            ),
            array(
                'fieldName' => 'search_tag',
                'rules' => 'required',
            ),
            array(
                'fieldName' => 'ordering',
                'rules' => 'required',
            ),
            array(
                'fieldName' => 'is_discount',
                'rules' => 'required',
            ),
            array(
                'fieldName' => 'phone',
                'rules' => 'required',
            ),
            array(
                'fieldName' => 'video_icon',
                'rules' => 'nullable|sometimes|image',
            ),
            array(
                'fieldName' => 'video',
                'rules' => 'nullable|sometimes|mimetypes:video/mp4',
            ),
        );

        $validationArr = handleValidation($errors, $coreFields, $validations);
        return $validationArr;

    }

    public function attributes()
    {
        $customFieldAttributeArr = handleCFAttrForValidation(Constants::item, $this->product_relation);

        $coreFieldAttributeArr = [
            'original_price.max' => "The original price must not be greater than 6 digits.",
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
