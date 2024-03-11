<?php

namespace Modules\PushNotificationMessage\Transformers\Backend\Model\PushNotificationMessage;

use Illuminate\Http\Resources\Json\JsonResource;

class PushNotificationMessageWithKeyResource extends JsonResource
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
            'message' => (string) $this->message,
            'description' => (string) $this->description,
            'added_date' => (string) $this->added_date,
            'added_user_id' => (string) $this->added_user_id,
            'added_user@@name' => (string) $this->owner->name,
            'updated_user_id' => (string) $this->updated_user_id,
            'updated_user@@name' => !empty($this->editor)? $this->editor->name:'',
            'updated_flag' => (string) $this->updated_flag,
            'cover'=>$this->cover ? $this->cover : null,
            'authorization' => $this->authorization
        ];
    }
}
