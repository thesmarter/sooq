<?php

namespace Modules\Core\Transformers\Api\App\V1_0\Category;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Entities\CoreImage;
use Modules\Core\Transformers\Api\App\V1_0\CoreImage\CoreImageApiResource;

class CategoryApiResource extends JsonResource
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
            'ordering' => isset($this->ordering)?(string)$this->ordering:'',
            'status' => isset($this->status)?(string)$this->status:'',
            'added_date' => isset($this->added_date)?(string)$this->added_date:'',
            "default_photo" => new CoreImageApiResource(isset($this->defaultPhoto) && $this->defaultPhoto ? $this->whenLoaded('defaultPhoto'): []),
            "default_icon" => new CoreImageApiResource(isset($this->defaultIcon) && $this->defaultIcon ? $this->whenLoaded('defaultIcon'): []),
            "added_date_str" => isset($this->added_date)?(string)$this->added_date->diffForHumans():'',
            "is_empty_object" => $this->when(!isset($this->id),1),
            "category_touch_count" => isset($this->category_touch)?(string)$this->category_touch->count():''
        ];
    }
}
