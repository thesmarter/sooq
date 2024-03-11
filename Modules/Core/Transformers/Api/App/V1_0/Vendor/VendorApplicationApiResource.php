<?php

namespace Modules\Core\Transformers\Api\App\V1_0\Vendor;

use Illuminate\Http\Resources\Json\JsonResource;

class VendorApplicationApiResource extends JsonResource
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
            'vendor_id' => isset($this->vendor_id)?(string)$this->vendor_id:'',
            'user_id' => isset($this->user_id)?(string)$this->user_id:'',
            'document' => isset($this->document)?(string)$this->document:'',
            'cover_letter' => isset($this->cover_letter)?(string)$this->cover_letter:'',
            "added_date_str" => isset($this->added_date)?(string)$this->added_date->diffForHumans():'',
            "is_empty_object" => $this->when(!isset($this->id),1),
        ];
    }
}
