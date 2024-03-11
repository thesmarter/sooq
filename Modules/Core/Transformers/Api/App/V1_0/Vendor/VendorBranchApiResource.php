<?php

namespace Modules\Core\Transformers\Api\App\V1_0\Vendor;

use Illuminate\Http\Resources\Json\JsonResource;

class VendorBranchApiResource extends JsonResource
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
            'name' => isset($this->name)?(string)$this->name:'',
            'email' => isset($this->email)?(string)$this->email:'',
            'phone' => isset($this->phone)?(string)$this->phone:'',
            'address' => isset($this->address)?(string)$this->address:'',
            'description' => isset($this->description)?(string)$this->description:'',
            'added_user_id' => isset($this->added_user_id)?(string)$this->added_user_id:'',
            "added_date_str" => isset($this->added_date)?(string)$this->added_date->diffForHumans():'',
            "is_empty_object" => $this->when(!isset($this->id),1),
        ];
    }
}
