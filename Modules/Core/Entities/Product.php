<?php

namespace Modules\Core\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Gate;

class Product extends Model
{
    use HasFactory;

    const CREATED_AT = 'added_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = [];

    protected $table = "psx_products";

    const tableName = "psx_products";
    const id = 'id';
    const title = "title";
    const description = "description";
    const categoryId = "category_id";
    const subCategoryId = "subcategory_id";
    const itemLocationId = "city_id";
    const price = "price";
    const isAvailable = "is_available";
    const itemLocationTownshipId = "township_id";
    const itemCurrencyId = "currency_id";
    const shopId = 'shop_id';
    const userId = "added_user_id";

    protected static function newFactory()
    {
        // return \Modules\Core\Database\factories\ProductFactory::new();
    }


    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function subCategory() {
        return $this->belongsTo(Subcategory::class,'subcategory_id','id');
    }

    public function city() {
        return $this->belongsTo(LocationCity::class);
    }

    public function township() {
        return $this->belongsTo(LocationTownship::class);
    }

    public function currency() {
        return $this->belongsTo(Currency::class);
    }

    public function productRelation() {
        return $this->hasMany(ProductInfo::class);
    }


    public function owner(){
        return $this->belongsTo(User::class, 'added_user_id','id');
    }

    public function cover() {
        return $this->hasOne(CoreImage::class, 'img_parent_id')->where('img_type', 'item');
    }

    public function video() {
        return $this->hasMany(CoreImage::class, 'img_parent_id')->where('img_type', 'item-video');
    }

    public function icon() {
        return $this->hasMany(CoreImage::class, 'img_parent_id')->where('img_type', 'item-video-icon');
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



}
