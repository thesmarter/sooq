<?php

namespace Modules\Core\Transformers\Api\App\V1_0\PhoneCountryCode;

use Illuminate\Http\Resources\Json\JsonResource;

class PhoneCountryCodeApiResource extends JsonResource
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
            'country_name' => isset($this->country_name)?(string)$this->country_name:'',
            'country_code' => isset($this->country_code)?(string)$this->country_code:'',
            'status' => isset($this->status)?(string)$this->status:'',
            'is_default' => isset($this->is_default)?(string)$this->is_default:'',
            'added_date' => isset($this->added_date)?(string)$this->added_date:'',
            "added_date_str" => isset($this->added_date)?(string)$this->added_date->diffForHumans():'',
            "is_empty_object" => $this->when(!isset($this->id),1), 
        ];
    }
}
