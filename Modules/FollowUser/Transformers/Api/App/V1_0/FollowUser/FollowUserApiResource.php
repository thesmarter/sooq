<?php

namespace Modules\FollowUser\Transformers\Api\App\V1_0\FollowUser;

use Illuminate\Http\Resources\Json\JsonResource;

class FollowUserApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
