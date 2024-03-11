<?php

namespace Modules\Core\Transformers\Backend\Model\Category;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

class CategoryWithKeyResource extends JsonResource
{

    public function toArray($request)
    {

        return [
            'id' => (string)$this->id,
            'name' => (string)$this->name,
            'ordering' => (string)$this->ordering,
            // 'updated_user_id@@name' => (string)$this->editor->name,
            'status' => (string)$this->status,
            'added_date' => (string)$this->added_date,
            'added_user_id' => (string)$this->added_user_id,
            'added_user_id@@name' => isset($this->owner) && $this->owner ? (string)$this->owner->name : '',
            'updated_date' => (string)$this->updated_date,
            'updated_user_id' => (string)$this->updated_user_id,
            'updated_flag' => (string)$this->updated_flag,
            'authorization' => $this->authorization
        ];
    }
}
