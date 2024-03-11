<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BuilderAppInfoCache extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'cached_data', 'added_date', 'added_user_id', 'updated_date', 'updated_user_id', 'updated_flag'];

    protected $table = "psx_builder_app_info_caches";

    const CREATED_AT = 'added_date';
    const UPDATED_AT = 'updated_date';

    const tableName = "psx_builder_app_info_caches";

    protected static function newFactory()
    {
        return \Modules\Core\Database\factories\BuilderAppInfoCatcheFactory::new();
    }
}
