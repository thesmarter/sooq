<?php

namespace Modules\Core\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CoreFieldFilterSetting extends Model
{
    use HasFactory;

    // protected $fillable = ['id'];

    protected $table = "psx_core_field_filter_settings";

    const id = "id";
    const tableId = "table_id";
    const placeholder = "placeholder";
    const moduleName = "module_name";
    const fieldName = "field_name";
    const labelName = "label_name";
    const isDelete = "is_delete";
    const tableName = "psx_core_field_filter_settings";
    const addedDate = "added_date";
    const addedUserId = "added_user_id";
    const updatedUserId = "updated_user_id";
    const updatedDate = "updated_date";
    const updatedFlag = "updated_flag";


    const CREATED_AT = 'added_date';
    const UPDATED_AT = 'updated_date';

    protected $casts = [
        'enable' => 'integer',
        'is_delete' => 'integer',
    ];

    protected static function newFactory()
    {
        // return \Modules\Core\Database\factories\CoreFieldFilterSettingFactory::new();
    }

    public function screenDisplayUiSetting(){
        return $this->hasOne(ScreenDisplayUiSetting::class,"key","id");
    }

    public function toArray()
    {
        if (str_contains($this->field_name,"@@")) {
            $originFieldName = strstr($this->field_name,"@@",true);

        } else {
            $originFieldName = $this->field_name;
        }
        return parent::toArray() + [
            'original_field_name' => $originFieldName
        ];
    }

    public function owner(){
        return $this->belongsTo(User::class, 'added_user_id');
    }

    public function editor(){
        return $this->belongsTo(User::class, 'updated_user_id');
    }

    // accessor
//    protected function fieldName(): Attribute
//    {
//        return Attribute::make(
//            get: function ($value){
//                if (str_contains($value,"@@")) {
//                    return strstr($value,"@@",true);
//                } else {
//                    return $value;
//                }
//            },
//        );
//    }

}
