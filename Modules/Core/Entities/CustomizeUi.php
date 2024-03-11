<?php

namespace Modules\Core\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Core\Entities\CustomizeUiDetail as EntitiesCustomizeUiDetail;
use Modules\Core\Entities\CustomizeUiDetail;
use Modules\Core\Entities\ProductInfo;
use Modules\Core\Entities\ScreenDisplayUiSetting;

class CustomizeUi extends Model
{
    use HasFactory;

    // protected $fillable = [];

    const tableName = "psx_customize_ui";
    const id = "id";
    const name = "name";
    const tableId = "table_id";
    const placeholder = "placeholder";
    const uiTypeId = "ui_type_id";
    const coreKeysId = "core_keys_id";
    const mandatory = "mandatory";
    const enable = "enable";
    const isDelete = "is_delete";
    const moduleName = "module_name";
    const addedDate = "added_date";
    const addedUserId = "added_user_id";
    const updatedDate = "updated_date";
    const updatedUserId = "updated_user_id";
    const updatedFlag = "updated_flag";

    const CREATED_AT = 'added_date';
    const UPDATED_AT = 'updated_date';

    protected $table = 'psx_customize_ui';

    protected static function newFactory()
    {
        // return \Modules\Core\Database\factories\CustomizeUiFactory::new();
    }

    public function hideShowSettingForFields() {
        return $this->hasOne(ScreenDisplayUiSetting::class,"key","core_keys_id");
    }

    public function coreKeysId() {
        return $this->hasOne(ProductInfo::class,"core_keys_id","core_keys_id");
    }

    public function uiTypeId() {
        return $this->belongsTo(UiType::class, "ui_type_id","core_keys_id");
    }

    public function owner(){
        return $this->belongsTo(User::class, 'added_user_id');
    }

    public function editor(){
        return $this->belongsTo(User::class, 'updated_user_id');
    }

//    public function customizeUiDetails() {
//        return $this->hasMany(CustomizeUiDetail::class,"core_keys_id","core_keys_id");
//    }

    public function toArray()
    {

        if ($this->ui_type_id == 'uit00003') {
            $data  = EntitiesCustomizeUiDetail::where("core_keys_id",$this->core_keys_id)->get();
        }else {
            $data  = [];
        }
        return parent::toArray() + [
            "customizeUiDetails" => $data
        ];
    }

}
