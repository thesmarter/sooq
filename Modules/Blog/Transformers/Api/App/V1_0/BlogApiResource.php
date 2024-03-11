<?php

namespace Modules\Blog\Transformers\Api\App\V1_0;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Entities\LocationCity;
use Modules\Core\Transformers\Api\App\V1_0\LocationCity\LocationCityApiResource;
use Modules\Core\Transformers\Api\App\V1_0\CoreImage\CoreImageApiResource;
use Modules\Core\Entities\CoreImage;

class BlogApiResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => isset($this->id)?(string)$this->id:'',
            'name' => isset($this->name)?(string)$this->name:'',
            'description' => isset($this->description)?(string)$this->description:'',
            'location_city_id' => isset($this->location_city_id)?(string)$this->location_city_id:'',
            'shop_id' => isset($this->shop_id)?(string)$this->shop_id:'',
            'status' => isset($this->status)?(string)$this->status:'',
            "city" => new LocationCityApiResource(isset($this->city) && $this->city ? $this->whenLoaded('city'): LocationCity::where(LocationCity::id, 0)->get()),
            "default_photo" => new CoreImageApiResource(isset($this->cover[0]) && $this->cover[0] ? $this->cover[0]: CoreImage::where(CoreImage::id, 0)->get()),
            "added_date_str" => isset($this->added_date)?(string)$this->added_date->diffForHumans():'',
            'added_date' => isset($this->added_date)?(string)$this->added_date:'',
            "is_empty_object" => $this->when(!isset($this->id),1),
        ];
    }
}
