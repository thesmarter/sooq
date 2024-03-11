<?php

namespace Modules\Core\Transformers\Api\App\V1_0\LocationCity;

use Illuminate\Http\Resources\Json\JsonResource;

class LocationCityApiResource extends JsonResource
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
            'lat' => isset($this->lat)?(string)$this->lat:'',
            'lng' => isset($this->lng)?(string)$this->lng:'',
            'ordering' => isset($this->ordering)?(string)$this->ordering:'',
            'status' => isset($this->status)?(string)$this->status:'',
            'description' => isset($this->description)?(string)$this->description:'',
            'touch_count' => isset($this->touch_count)?(string)$this->touch_count:'',
            'is_featured' => isset($this->is_featured)?(string)$this->is_featured:'',
            'featured_date' => isset($this->featured_date)?(string)$this->featured_date:'',
            "cityRelation" => LocationCityInfoApiResource::collection(isset($this->cityRelation) && count($this->cityRelation)>0 ? $this->whenLoaded('cityRelation'): ['xxx']),
            "added_date_str" => isset($this->added_date) ? (string) $this->added_date->diffForHumans():'',
            "is_empty_object" => $this->when(!isset($this->id), 1),
        ];
    }
}
