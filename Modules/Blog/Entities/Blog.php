<?php

namespace Modules\Blog\Entities;

use App\Models\User;
use Modules\Core\Entities\Shop;
use Modules\Core\Entities\LocationCity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Core\Entities\CoreImage;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Gate;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'name', 'description', 'location_city_id', 'shop_id', 'status', 'added_date', 'added_user_id', 'updated_date', 'updated_user_id', 'updated_flag'];

    protected $table = "psx_blogs";

    const CREATED_AT = 'added_date';
    const UPDATED_AT = 'updated_date';

    const tableName = "psx_blogs";
    const id = "id";
    const name = "name";
    const description = "description";
    const locationCityId = "location_city_id";
    const status = "status";
    const addedDate = 'added_date';
    const addedUserId= 'added_user_id';

    protected static function newFactory()
    {
        // return \Modules\Blog\Database\factories\BlogFactory::new();
    }

    public function city(){
        return $this->belongsTo(LocationCity::class, 'location_city_id');
    }

    public function shop(){
        return $this->belongsTo(Shop::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'added_user_id');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_user_id');
    }

    public function cover()
    {
        return $this->hasMany(CoreImage::class, 'img_parent_id')->where('img_type', 'blog');
    }

    public function authorizations($abilities = []){
        return collect(array_flip($abilities))->map(function ($index, $ability){
            return Gate::allows($ability, $this);
        });
    }

    public function toArray()
    {
        return parent::toArray() + [
            'authorizations' => $this->authorizations(['update','delete','create'])
        ];
    }

    protected function authorization(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->authorizations(['update','delete','create']),
        );
    }

}
