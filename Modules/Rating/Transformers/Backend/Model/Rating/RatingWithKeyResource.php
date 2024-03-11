<?php

namespace Modules\Rating\Transformers\Backend\Model\Rating;

use Illuminate\Http\Resources\Json\JsonResource;

class RatingWithKeyResource extends JsonResource
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
            'title' => (string) $this->title,
            'description' => (string) $this->description,
            'rating' => (string) $this->rating,
            'from_user_id' => (string) $this->from_user_id,
            'from_user@@name' => (string) $this->from_user->name,
            'to_user_id' => (string) $this->to_user_id,
            'to_user@@name' => (string) $this->to_user->name,
            "transaction_header_id" => (string) $this->transaction_header_id,
            'item_id' => (string) $this->item_id,
            'shop_id' => (string) $this->shop_id,
            'type' => (string) $this->type,
            'added_date' => (string) $this->added_date,
            'added_user_id' => (string) $this->added_user_id,
            'added_user@@name' => (string) $this->owner->name,
            'updated_user_id' => (string) $this->updated_user_id,
            'updated_user@@name' => !empty($this->editor) ? $this->editor->name:'',
            'updated_flag' => (string) $this->updated_flag,
        ];
    }
}
