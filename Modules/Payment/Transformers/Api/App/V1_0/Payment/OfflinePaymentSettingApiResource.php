<?php

namespace Modules\Payment\Transformers\Api\App\V1_0\Payment;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Entities\CoreImage;
use Modules\Core\Transformers\Api\App\V1_0\CoreImage\CoreImageApiResource;
use Modules\Core\Transformers\CoreKeyApiResource;


class OfflinePaymentSettingApiResource extends JsonResource
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
            'shop_id' => isset($this->shop_id)?(string)$this->shop_id:'',
            'title' => isset($this->core_key)?(string)$this->core_key->name:'',
            'description' => isset($this->value)?(string)$this->value:'',
            'added_date' => isset($this->added_date)?(string)$this->added_date:'',
            'added_user_id' => isset($this->added_user_id)?(string)$this->added_user_id:'',
            'updated_date' => isset($this->updated_date)?(string)$this->updated_date:'',
            'updated_user_id' => isset($this->updated_user_id)?(string)$this->updated_user_id:'',
            "default_icon" => new CoreImageApiResource(isset($this->offline_icon) && $this->offline_icon ? $this->whenLoaded('offline_icon'): CoreImage::where('id', 0)->get()),
            "added_date_str" => isset($this->added_date)?(string)$this->added_date->diffForHumans():'',
            "is_empty_object" => $this->when(!isset($this->id),1),
        ];
    }
}
