<?php

namespace Modules\Core\Transformers\Backend\Model\LocationTownship;

use Illuminate\Http\Resources\Json\JsonResource;

class LocationTownshipWithKeyResource extends JsonResource
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
            'id' => (string) $this->id,
            'name' => (string) $this->name,
            'location_city_id' => (string) $this->location_city_id,
            'location_city_id@@name' => $this->location_city->name,
            'lat' => (string) $this->lat,
            'lng' => (string) $this->lng,
            'ordering' => (string) $this->ordering,
            'status' => (string) $this->status,
            'description' => (string) $this->description,
            'touch_count' => (string) $this->touch_count,
            'is_featured' => (string) $this->is_featured,
            'featured_date' => (string) $this->featured_date,
            'added_date' => (string) $this->added_date,
            'added_user_id' => (string) $this->added_user_id,
            'added_user_id@@name' => (string) $this->owner->name,
            'updated_user_id' => (string) $this->updated_user_id,
            'updated_user_id@@name' => !empty($this->editor)? $this->editor->name:'',
            'updated_flag' => (string) $this->updated_flag,
            'authorization' => $this->authorization
        ];
    }
}
