<?php

namespace Modules\Core\Transformers\Api\App\V1_0\MobileLanguageString;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Entities\MobileLanguage;

class MobileLanguageStringApiResource extends JsonResource
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
            'id' => isset($this->id) ? (string) $this->id : '',
            'mobile_langauge_id' => isset($this->mobile_langauge_id) ? (string) $this->mobile_langauge_id : '',
            'key' => isset($this->key) ? (string) $this->key : '',
            'value' => isset($this->value) ? (string) $this->value : '',
            'status' => isset($this->status) ? (string) $this->status : '',
            "mobile_language" => new MobileLanguage(isset($this->mobileLanguage) && $this->mobileLanguage ? $this->whenLoaded('location_city') : MobileLanguage::where('id', 0)->get()),
            "added_date_str" => isset($this->added_date) ? (string) $this->added_date->diffForHumans() : '',
            "is_empty_object" => $this->when(!isset($this->id), 1),
        ];
    }
}
