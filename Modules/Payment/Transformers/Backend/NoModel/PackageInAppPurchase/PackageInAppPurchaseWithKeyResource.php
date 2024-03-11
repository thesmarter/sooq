<?php

namespace Modules\Payment\Transformers\Backend\NoModel\PackageInAppPurchase;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\AvailableCurrency;

class PackageInAppPurchaseWithKeyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $currency = '';
        if($this->currency_id){
            $currency = AvailableCurrency::find($this->currency_id)->currency_short_form;
        }
        return [
            'id' => $this->id,
            'in_app_purchase_prd_id' => !empty($this->core_key) ? $this->core_key->name:'',
            'core_keys_id' => $this->core_keys_id,
            'description' => $this->value,
            'count' => $this->count,
            'price' => $this->price,
            'type' => $this->type,
            'status' => $this->status,
            'currency_id' => $currency,
            'added_date' => $this->added_date,
            'added_user_id' => $this->added_user_id,
            'added_user@@name' => isset($this->owner) && !empty($this->owner) ? $this->owner->name : '',
            'updated_date' => $this->updated_date,
            'updated_user_id' => $this->updated_user_id,
            'updated_user@@name' => isset($this->editor) && !empty($this->editor) ? $this->editor->name : '',
            'updated_flag' => $this->updated_flag,
            'authorizations' => authorizationWithoutPolicy(Constants::packageInAppPurchaseModule)
        ];
    }
}
