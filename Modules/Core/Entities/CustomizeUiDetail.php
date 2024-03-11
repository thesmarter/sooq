<?php

namespace Modules\Core\Entities;

use App\Models\User;
use Modules\Core\Entities\UiType;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Entities\CustomizeDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Gate;

class CustomizeUiDetail extends Model
{
    use HasFactory;

    protected $fillable = [];

    protected $table = "psx_customize_ui_details";

    const CREATED_AT = 'added_date';
    const UPDATED_AT = 'updated_date';

    const id = "id";
    const tableName = "psx_customize_ui_details";
    const name = "name";
    const coreKeysId = "core_keys_id";

    protected static function newFactory()
    {
        // return \Modules\Core\Database\factories\CustomizeUiFactory::new();
    }

    public function customize_detail(){
        return $this->hasMany(CustomizeDetail::class);
    }

    public function ui_type(){
        return $this->belongsTo(UiType::class);
    }

    public function owner(){
        return $this->belongsTo(User::class, 'added_user_id');
    }

    public function editor(){
        return $this->belongsTo(User::class, 'updated_user_id');
    }

    public function authorizations($abilities = []){
        return collect(array_flip($abilities))->map(function ($index, $ability){
            return Gate::allows($ability, $this);
        });
    }

//    public function toArray()
//    {
//        return parent::toArray() + [
//            'authorizations' => $this->authorizations(['update','delete','create'])
//        ];
//    }

    protected function authorization(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->authorizations(['update','delete','create']),
        );
    }
}
