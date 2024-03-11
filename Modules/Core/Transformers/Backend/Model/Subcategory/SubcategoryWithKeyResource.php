<?php

namespace Modules\Core\Transformers\Backend\Model\Subcategory;

use Illuminate\Http\Resources\Json\JsonResource;

class SubcategoryWithKeyResource extends JsonResource
{


    public function toArray($request)
    {

        return [
            'id' => (string)$this->id,
            'name' => (string)$this->name,
            'category_id' => (string)$this->category_id,
            'category_id@@name' => isset($this->category)?(string)$this->category->name:'',
            // 'category_id@@name' => (string)$this->category->name,
            'ordering' => (string)$this->ordering,
            // 'updated_user_id@@name' => (string)$this->editor->name,
            'status' => (string)$this->status,
            'added_date' => (string)$this->added_date,
            'added_user_id' => (string)$this->added_user_id,
            'added_user_id@@name' => (string)$this->owner->name,
            'updated_date' => (string)$this->updated_date,
            'updated_user_id' => (string)$this->updated_user_id,
            'updated_flag' => (string)$this->updated_flag,
            'authorization' => $this->authorization
        ];
    }
}
