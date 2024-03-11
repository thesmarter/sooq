<?php

namespace Modules\Core\Transformers\Api\App\V1_0\SystemConfig;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\Setting;

class SystemConfigApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $setting = Setting::where('setting_env', Constants::SYSTEM_CONFIG )->first();
        $selcted_array = json_decode($setting->setting, true);

        return [
            'lat' => isset($this->lat)?(string)$this->lat:'',
            'lng' => isset($this->lng)?(string)$this->lng:'',
            'is_approved_enable' => isset($this->is_approved_enable)?(string)$this->is_approved_enable:'',
            'is_sub_location' => isset($this->is_sub_location)?(string)$this->is_sub_location:'',
            'is_thumb2x_3x_generate' => isset($this->is_thumb2x_3x_generate)?(string)$this->is_thumb2x_3x_generate:'',
            'is_sub_subscription' => isset($this->is_sub_subscription)?(string)$this->is_sub_subscription:'',
            'is_paid_app' => isset($this->is_paid_app)?(string)$this->is_paid_app:'',
            'is_block_user' => isset($this->is_block_user)?(string)$this->is_block_user:'',
            'max_img_upload_of_item' => isset($this->max_img_upload_of_item)?(string)$this->max_img_upload_of_item:'',
            'ad_type' => isset($this->ad_type)?(string)$this->ad_type:'',
            'promo_cell_interval_no' => isset($this->promo_cell_interval_no)?(string)$this->promo_cell_interval_no:'',
            'one_day_per_price' => isset($this->one_day_per_price)?(string)$this->one_day_per_price:'',
            'selected_price_type' => (string) $selcted_array['selected_price_type']['id'],
            'selected_chat_type' => (string) $selcted_array['selected_chat_data']['id'],

        ];
    }
}
