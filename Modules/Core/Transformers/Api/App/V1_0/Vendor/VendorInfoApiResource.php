<?php

namespace Modules\Core\Transformers\Api\App\V1_0\Vendor;

use Modules\Core\Constants\Constants;
use Modules\Core\Entities\VendorInfo;
use Modules\Core\Entities\CustomizeUiDetail;
use Illuminate\Http\Resources\Json\JsonResource;

class vendorInfoApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $dropDownUi = Constants::dropDownUi;
        $textUi = Constants::textUi;
        $radioUi = Constants::radioUi;
        $checkBoxUi = Constants::checkBoxUi;
        $dateTimeUi = Constants::dateTimeUi;
        $textAreaUi = Constants::textAreaUi;
        $numberUi = Constants::numberUi;
        $multiSelectUi = Constants::multiSelectUi;
        $imageUi = Constants::imageUi;
        $timeOnlyUi = Constants::timeOnlyUi;
        $dateOnlyUi = Constants::dateOnlyUi;

        $data = []; $changedObj = [];
        $empty = [
            "id" => (string) "",
            "value" => (string) "",
        ];

        $uiTypeForAttrs = [ $dropDownUi, $radioUi];
        $otherUiTypes = [$textUi, $checkBoxUi, $dateTimeUi, $textAreaUi, $numberUi, $imageUi, $timeOnlyUi, $dateOnlyUi];
        if (!empty($this->value)){
            if (in_array($this->ui_type_id, $uiTypeForAttrs)) {
                $obj  = CustomizeUiDetail::where(CustomizeUiDetail::id, $this->value)->first();
                if($obj){
                    $changedObj = [
                        "id" => (string) $obj->id,
                        "value" => (string) $obj->name,
                    ];
                    $data = array($changedObj);
                }
            } elseif (in_array($this->ui_type_id, $otherUiTypes)) {
                $obj  = VendorInfo::where(VendorInfo::id, $this->id)->first();
                if($obj){
                    $changedObj = [
                        "id" => (string) $obj->id,
                        "value" => (string) $obj->value,
                    ];
                    $data = array($changedObj);
                }
            } elseif ($this->ui_type_id == $multiSelectUi) {
                $selectedIds = explode(',', $this->value);
                $objs  = CustomizeUiDetail::whereIn(CustomizeUiDetail::id, $selectedIds)->get();
                $changedObj = [];
                foreach ($objs as $obj){
                    $data = [
                        "id" => (string)$obj->id,
                        "value" => (string)$obj->name
                    ];
                    array_push($changedObj, $data);
                }

                $data = $changedObj;
            }
        }

        if (empty($data)) {
            $data = array($empty);
        }

        return [
            'id' => isset($this->id)?(string)$this->id:'',
            'vendor_id' => isset($this->vendor_id)?(string)$this->vendor_id:'',
            'core_keys_id' => isset($this->core_keys_id)?(string)$this->core_keys_id:'',
            'core_key_name' => isset($this->customizeUi)?(string)__($this->customizeUi->name, [], $request->language_symbol):'',
            'isVisible' => isset($this->customizeUi)?(string)$this->customizeUi->enable:'',
            'value' => isset($this->value)?(string)$this->value:'',
            'ui_type_id' => isset($this->ui_type_id)?(string)$this->ui_type_id:'',
            "added_date_str" => isset($this->added_date)?(string)$this->added_date->diffForHumans():'',
            "selectedValue" => $data
        ];
    }
}
