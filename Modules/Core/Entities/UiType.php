<?php

namespace Modules\Core\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UiType extends Model
{
    use HasFactory;

    const CREATED_AT = 'added_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = [];

    protected $table = "psx_ui_types";

    protected static function newFactory()
    {
        // return \Modules\Core\Database\factories\UiTypeFactory::new();
    }

    public function customize_header(){
        return $this->hasMany(CustomizeHeader::class);
    }

    public function owner(){
        return $this->belongsTo(User::class, 'added_user_id');
    }

    public function editor(){
        return $this->belongsTo(User::class, 'updated_user_id');
    }
}
