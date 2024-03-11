<?php

namespace Modules\Core\Transformers\Api\App\V1_0\CoreKey;

use Illuminate\Http\Resources\Json\JsonResource;

class CoreFieldApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        if (str_contains($this->field_name,"@@")) {
            $originFieldName = strstr($this->field_name,"@@",true);
        } else {
            $originFieldName = $this->field_name;
        }
        
        return [
            'id' => isset($this->id)?(string)$this->id:'',
            'field_name' => isset($originFieldName)?(string)$originFieldName:'',
            'label_name' => isset($this->label_name)?(string)$this->label_name:'',
            'placeholder' => isset($this->placeholder)?(string)$this->placeholder:'',
            'data_type' => isset($this->data_type)?(string)$this->data_type:'',
            'is_core_field' => isset($this->is_core_field)?(string)$this->is_core_field:'',
            'is_visible' => isset($this->enable)?(string)$this->enable:'',
            'mandatory' => isset($this->mandatory)?(string)$this->mandatory:'',
            'added_date' => isset($this->added_date)?(string)$this->added_date:'',
            "added_date_str" => isset($this->added_date)?(string)$this->added_date->diffForHumans():'',
            "is_empty_object" => $this->when(!isset($this->id),1),
           
        ];
    }
}
