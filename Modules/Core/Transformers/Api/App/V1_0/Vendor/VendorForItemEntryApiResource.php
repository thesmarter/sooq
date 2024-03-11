<?php

namespace Modules\Core\Transformers\Api\App\V1_0\Vendor;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Transformers\Api\App\V1_0\CoreImage\CoreImageApiResource;

class VendorForItemEntryApiResource extends JsonResource
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
            "logo" => new CoreImageApiResource(isset($this->logo) && $this->logo ? $this->whenLoaded('logo'): []),
        ];
    }
}
