<?php

namespace Modules\Core\Transformers\Api\App\V1_0\Subcategory;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Entities\CoreImage;
use Modules\Core\Transformers\Api\App\V1_0\CoreImage\CoreImageApiResource;
use Modules\Core\Entities\SubcatSubscribe;

class SubcategoryApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        if(isset($this->id) && !empty($this->id)){

            // is subscribe mb

            $conds_mb[SubcatSubscribe::user_id] = $_GET['login_user_id'];
            $conds_mb[SubcatSubscribe::subcat_id] = $this->id . '_MB';

            $subcat_mb = SubcatSubscribe::where($conds_mb)->count();

            if ($subcat_mb == "1") {
                $subcat_subscribe_mb = 1;

            } else {
                $subcat_subscribe_mb = 0;
            }

            // is subscribe fe

            $conds_fe[SubcatSubscribe::user_id] = $_GET['login_user_id'];
            $conds_fe[SubcatSubscribe::subcat_id] = $this->id . '_FE';

            $subcat_fe = SubcatSubscribe::where($conds_fe)->count();

            if ($subcat_fe == "1") {
                $subcat_subscribe_fe = 1;

            } else {
                $subcat_subscribe_fe = 0;
            }

            // $subcat_subscribe_fe = 0;

        }

        return [
            'id' => isset($this->id)?(string)$this->id:'',
            'name' => isset($this->name)?(string)$this->name:'',
            'category_id' => isset($this->category_id)?(string)$this->category_id:'',
            'ordering' => isset($this->ordering)?(string)$this->ordering:'',
            'status' => isset($this->status)?(string)$this->status:'',
            'added_date' => isset($this->added_date)?(string)$this->added_date:'',
            'added_user_id' => isset($this->added_user_id)?(string)$this->added_user_id:'',
            'updated_date' => isset($this->updated_date)?(string)$this->updated_date:'',
            'updated_user_id' => isset($this->updated_user_id)?(string)$this->updated_user_id:'',
            'updated_flag' => isset($this->updated_flag)?(string)$this->updated_flag:'',
            'is_subscribed_mb' => (string)isset($subcat_subscribe_mb)? (string)$subcat_subscribe_mb:'',
            'is_subscribed_fe' => (string)isset($subcat_subscribe_fe)? (string)$subcat_subscribe_fe:'',
            "added_date_str" => isset($this->added_date)?(string)$this->added_date->diffForHumans():'',
            "default_photo" => new CoreImageApiResource(isset($this->defaultPhoto) && $this->defaultPhoto ? $this->whenLoaded('defaultPhoto'): []),
            "default_icon" => new CoreImageApiResource(isset($this->defaultIcon) && $this->defaultIcon ? $this->whenLoaded('defaultIcon'): []),
            "is_empty_object" => $this->when(!isset($this->id),1),
        ];
    }
}
