<?php

namespace Modules\Core\Transformers\Api\App\V1_0\LocationTownship;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Entities\LocationCity;
use Modules\Core\Transformers\Api\App\V1_0\LocationCity\LocationCityApiResource;

class LocationTownshipApiResource extends JsonResource
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
            'location_city_id' => isset($this->location_city_id)?(string)$this->location_city_id:'',
            'lat' => isset($this->lat)?(string)$this->lat:'',
            'lng' => isset($this->lng)?(string)$this->lng:'',
            'ordering' => isset($this->ordering)?(string)$this->ordering:'',
            'status' => isset($this->status)?(string)$this->status:'',
            'description' => isset($this->description)?(string)$this->description:'',
            'touch_count' => isset($this->touch_count)?(string)$this->touch_count:'',
            'is_featured' => isset($this->is_featured)?(string)$this->is_featured:'',
            'featured_date' => isset($this->featured_date)?(string)$this->featured_date:'',
            "location_city" => new LocationCityApiResource(isset($this->location_city) && $this->location_city ? $this->whenLoaded('location_city'): LocationCity::where('id', 0)->get()),
            "added_date_str" => isset($this->added_date) ? (string) $this->added_date->diffForHumans():'',
            "is_empty_object" => $this->when(!isset($this->id), 1),
        ];
    }
}
