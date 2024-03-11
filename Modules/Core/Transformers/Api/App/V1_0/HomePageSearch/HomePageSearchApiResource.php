<?php

namespace Modules\Core\Transformers\Api\App\V1_0\HomePageSearch;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Transformers\Api\App\V1_0\Category\CategoryApiResource;
use Modules\Core\Transformers\Api\App\V1_0\Product\ProductApiResource;
use Modules\Core\Transformers\Api\App\V1_0\User\UserApiResource;

class HomePageSearchApiResource extends JsonResource
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
            "items" => ProductApiResource::collection($this['items']),
            "categories" => CategoryApiResource::collection($this['categories']),
            "users" => UserApiResource::collection($this['users']),
        ];
    }
}
