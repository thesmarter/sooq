<?php

namespace Modules\Rating\Transformers\Api\App\V1_0;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Transformers\Api\App\V1_0\User\UserApiResource;

class RatingApiResource extends JsonResource
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
            'title' => isset($this->title)?(string)$this->title:'',
            'description' => isset($this->description)?(string)$this->description:'',
            'rating' => isset($this->rating)?(string)$this->rating:'',
            'from_user_id' => isset($this->from_user_id)?(string)$this->from_user_id:'',
            'to_user_id' => isset($this->to_user_id)?(string)$this->to_user_id:'',
            "transaction_header_id" => (string)$this->transaction_header_id,
            'item_id' => isset($this->item_id)?(string)$this->item_id:'',
            'shop_id' => isset($this->shop_id)?(string)$this->shop_id:'',
            'type' => isset($this->type)?(string)$this->type:'',
            "from_user" => new UserApiResource(isset($this->fromUser) && $this->fromUser ? $this->whenLoaded('fromUser'): User::where(User::id, 0)->get()),
            "to_user" => new UserApiResource(isset($this->toUser) && $this->toUser ? $this->whenLoaded('toUser'): User::where(User::id, 0)->get()),
            "added_date_str" => isset($this->added_date)?(string)$this->added_date->diffForHumans():'',
            "is_empty_object" => $this->when(!isset($this->id),1),
            "added_date" => isset($this->added_date)?(string)$this->added_date:'',

        ];
    }
}
