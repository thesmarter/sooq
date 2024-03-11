<?php

namespace Modules\DataDeletionPolicy\Transformers\Api\App\V1_0\DataDeletionPolicy;

use Illuminate\Http\Resources\Json\JsonResource;

class DataDeletionPolicyApiResource extends JsonResource
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
            'content' => isset($this->content)?(string)$this->content:'',
        ];
    }
}
