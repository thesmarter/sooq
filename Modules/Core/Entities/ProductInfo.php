<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Core\Entities\CustomizeUi;
use Modules\Core\Entities\CustomizeUiDetail;
use Modules\UiType\Entities\UiType;

class ProductInfo extends Model
{
    use HasFactory;

    const productId = "product_id";
    const coreKeysId = "core_keys_id";
    const value = "value";
    const uiTypeId = "ui_type_id";


    protected $fillable = [];

    protected $table = "psx_product_infos";

    protected static function newFactory()
    {
        // return \Modules\Core\Database\factories\ProductInfoFactory::new();
    }

    public function uiType() {
        return $this->belongsTo(UiType::class,"ui_type_id","core_keys_id");
    }

    public function customizeUi() {
        return $this->belongsTo(CustomizeUi::class,"core_keys_id","core_keys_id");
    }

    public function toArray()
    {
        $data = null;
        if (!empty($this->value)){
            if ($this->ui_type_id == 'uit00001') {
                $data  = CustomizeUiDetail::where("id",$this->value)->first();
            } else if ($this->ui_type_id == 'uit00003'){
                $data  = CustomizeUiDetail::where("id",$this->value)->first();
            }
        }
        return parent::toArray() + [
            "customizeUiDetail" => $data
        ];
    }


}
