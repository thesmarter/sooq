<?php

namespace Modules\Core\Transformers\Backend\Model\VendorApplication;

use Illuminate\Http\Resources\Json\JsonResource;

class VendorApplicationWithKeyResource extends JsonResource
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
            'added_user_id' => isset($this->added_user_id)?(string)$this->added_user_id:'',
            "added_date_str" => isset($this->added_date)?(string)$this->added_date->diffForHumans():'',
        ];
    }
}
