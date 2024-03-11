<?php

namespace Modules\Core\Transformers\Api\App\V1_0\MobileLanguage;

use Illuminate\Http\Resources\Json\JsonResource;

class MobileLanguageApiResource extends JsonResource
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
            'symbol' => isset($this->symbol)?(string)$this->symbol:'',
            'language_code' => isset($this->language_code)?(string)$this->language_code:'',
            'country_code' => isset($this->country_code)?(string)$this->country_code:'',
            'name' => isset($this->name)?(string)$this->name:'',
            'code' => isset($this->code)?(string)$this->code:'',
            'status' => isset($this->status)?(string)$this->status:'',
            'enable' => isset($this->enable)?(string)$this->enable:'',            
            "added_date_str" => isset($this->added_date)?(string)$this->added_date->diffForHumans():'',
            "is_empty_object" => $this->when(!isset($this->id),1),
        ];
    }
}
