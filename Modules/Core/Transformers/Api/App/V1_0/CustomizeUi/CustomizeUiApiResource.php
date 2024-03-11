<?php

namespace Modules\Core\Transformers\Api\App\V1_0\CustomizeUi;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Transformers\Api\App\V1_0\CustomizeUiDetail\CustomizeUiDetailApiResource;
use Modules\Core\Transformers\Api\App\V1_0\UiType\UiTypeApiResource;
use Modules\Core\Entities\UiType;

class CustomizeUiApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => isset($this->id)?(string)$this->id:'',
            'name' => isset($this->name)?(string)$this->name:'',
            'placeholder' => isset($this->placeholder)?(string)$this->placeholder:'',
            'core_keys_id' => isset($this->core_keys_id)?(string)$this->core_keys_id:'',
            "ui_type" => new UiTypeApiResource(isset($this->uiTypeId) && $this->uiTypeId ? $this->whenLoaded('uiTypeId'): UiType::where('id', 0)->get()),
            'mandatory' => isset($this->mandatory)?(string)$this->mandatory:'',
            'is_visible' => isset($this->enable)?(string)$this->enable:'',
            'is_delete' => isset($this->is_delete)?(string)$this->is_delete:'',
            'module_name' => isset($this->module_name)?(string)$this->module_name:'',
            'data_type' => isset($this->data_type)?(string)$this->data_type:'',
            // 'table_id' => isset($this->table_id)?(string)$this->table_id:'',
            // 'project_id' => isset($this->project_id)?(string)$this->project_id:'',
            // 'project_name' => isset($this->project_name)?(string)$this->project_name:'',
            // 'base_module_name' => isset($this->base_module_name)?(string)$this->base_module_name:'',
            'is_core_field' => isset($this->is_core_field)?(string)$this->is_core_field:'',
            'added_date' => isset($this->added_date)?(string)$this->added_date:'',
            "added_date_str" => isset($this->added_date)?(string)$this->added_date->diffForHumans():'',
            "is_empty_object" => $this->when(!isset($this->id),1), 
            "customize_ui_details" => CustomizeUiDetailApiResource::collection(isset($this->customizeUiDetails) && count($this->customizeUiDetails)>0 ? $this->whenLoaded('customizeUiDetails'): ['xxx']),
        ];
    }
}
