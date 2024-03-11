<?php

namespace Modules\Core\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Gate;
use Modules\Core\Entities\Item;
use Modules\Core\Entities\Touch;
use Modules\Core\Entities\CoreImage;
use Modules\Core\Entities\Subcategory;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Entities\TransactionCount;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{

    use HasFactory;

    protected $fillable = ['id', 'name', 'ordering', 'status', 'added_date', 'added_user_id', 'updated_date', 'updated_user_id', 'updated_flag'];

    protected $table = "psx_categories";

    const CREATED_AT = 'added_date';
    const UPDATED_AT = 'updated_date';

    const tableName = "psx_categories";
    const name = "name";
    const id = "id";
    const status = "status";
    const addedDate = 'added_date';


    protected static function newFactory()
    {
        // return \Modules\Core\Database\factories\CategoryFactory::new();
    }

    public function subcategory(){
        return $this->hasMany(Subcategory::class);
    }

    public function item(){
        return $this->hasMany(Item::class);
    }

    public function category_touch() {
        return $this->hasMany(Touch::class, 'type_id', 'id')->where('type_name', 'Category');
    }

    public function defaultPhoto()
    {
        return $this->hasOne(CoreImage::class, 'img_parent_id', 'id')->where('img_type', 'category-cover');
    }

    public function defaultIcon()
    {
        return $this->hasOne(CoreImage::class, 'img_parent_id', 'id')->where('img_type', 'category-icon');
    }

    public function icon() {
        return $this->hasOne(CoreImage::class, 'img_parent_id')
                    ->where('img_type', 'category-icon');
    }

    public function cover() {
        return $this->hasOne(CoreImage::class, 'img_parent_id')
                    ->where('img_type', 'category-cover');
    }

    public function transaction(){
        return $this->hasMany(TransactionCount::class);
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
//                'authorizations' => $this->authorizations(['update','delete','create'])
//            ];
//    }

    protected function authorization(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->authorizations(['update','delete','create']),
        );
    }


}
