<?php

namespace Modules\Core\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Modules\Core\Entities\Shop;
use Modules\Core\Entities\Touch;
use Modules\Core\Entities\Category;
use Modules\Core\Entities\Vendor;
use Modules\Core\Entities\Currency;
use Modules\Core\Entities\CoreImage;
use Modules\Core\Entities\Favourite;
use Modules\Core\Entities\Subcategory;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Entities\LocationCity;
use Modules\Core\Entities\ItemInfo;
use Modules\Core\Entities\LocationTownship;
use Modules\Core\Entities\TransactionCount;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Gate;
use Modules\Chat\Entities\UserBought;
use App\Policies\PsPolicy;
use App\Policies\PsVendorPolicy;
use Modules\Core\Constants\Constants;
use Illuminate\Support\Facades\Auth;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'title',
        'category_id',
        'subcategory_id',
        'currency_id',
        'location_city_id',
        'location_township_id',
        'shop_id',
        'price',
        'original_price',
        'description',
        'search_tag',
        'dynamic_link',
        'lat',
        'lng', '
        status',
        'ordering',
        'is_available',
        'is_discount',
        'touch_count',
        'favourite_count',
        'overall_rating',
        'is_paid',
        'is_sold_out',
        'vendor_id',
        'added_date',
        'added_user_id',
        'updated_date',
        'updated_user_id',
        'updated_flag'
    ];

    protected $table = "psx_items";

    const CREATED_AT = 'added_date';
    const UPDATED_AT = 'updated_date';

    const tableName = 'psx_items';
    const id = "id";
    const title = "title";
    const categoryId = "category_id";
    const subCategoryId = "subcategory_id";
    const itemCurrencyId = "currency_id";
    const itemLocationId = "location_city_id";
    const itemLocationTownshipId = "location_township_id";
    const shopId = 'shop_id';
    const price = "price";
    const description = "description";
    const searchterm = "search_tag";
    const status = "status";
    const lat = "lat";
    const lng = "lng";
    const isAvailable = 'is_available';
    const isPaid = 'is_paid';
    const isSoldOut = 'is_sold_out';
    const isDiscount = 'is_discount';
    const favouriteCount = 'favourite_count';
    const touchCount = 'item_touch_count';
    const overallRating = 'overall_rating';
    const addedUserId = 'added_user_id';
    const addedDate = 'added_date';
    const userId = "added_user_id";
    const vendorId = 'vendor_id';

    protected static function newFactory()
    {
        return \Modules\Core\Database\factories\ItemFactory::new();
    }

    public function shop(){
        return $this->belongsTo(Shop::class);
    }

    public function city(){
        return $this->belongsTo(LocationCity::class, 'location_city_id');
    }

    public function township(){
        return $this->belongsTo(LocationTownship::class, 'location_township_id');
    }

    public function currency(){
        return $this->belongsTo(Currency::class);
    }

    public function owner(){
        return $this->belongsTo(User::class, 'added_user_id');
    }

    public function buyer(){
        return $this->belongsTo(User::class, 'buyer_user_id');
    }

    public function seller(){
        return $this->belongsTo(User::class, 'seller_user_id');
    }

    public function editor(){
        return $this->belongsTo(User::class, 'updated_user_id');
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function vendor(){
        return $this->belongsTo(Vendor::class,"vendor_id","id")->where('psx_vendors.status', Constants::vendorAcceptStatus)->with('logo');
    }

    public function subcategory(){
        return $this->belongsTo(Subcategory::class,"subcategory_id","id");
    }

    public function item_touch() {
        return $this->hasMany(Touch::class, 'type_id')->where('type_name', 'Item');
    }


    public function category_touch() {
        return $this->hasMany(Touch::class, 'type_id', 'category_id')->where('type_name', 'Category');
    }

    public function subcategory_touch() {
        return $this->hasMany(Touch::class, 'type_id', 'subcategory_id')->where('type_name', 'Subcategory');
    }

    public function cover() {
        return $this->hasMany(CoreImage::class, 'img_parent_id')->where('img_type', 'item')->orderBy('ordering', "asc");
    }

    public function video() {
        return $this->hasMany(CoreImage::class, 'img_parent_id')->where('img_type', 'item-video');
    }

    public function icon() {
        return $this->hasMany(CoreImage::class, 'img_parent_id')->where('img_type', 'item-video-icon');
    }

    public function favourite(){
        return $this->hasMany(Favourite::class);
    }

    public function transaction(){
        return $this->hasMany(TransactionCount::class);
    }

    public function itemRelation()
    {
        return $this->hasMany(ItemInfo::class);
    }

    public function user_boughts() {
        return $this->hasMany(UserBought::class, 'item_id')->with('buyer');
    }

    public function authorizations($module, $abilities = [], $policyClass = null, $params = [])
    {
        $policy = $policyClass ? new $policyClass($module, $this) : null;

        return collect($abilities)->mapWithKeys(function ($ability) use ($policy, $params) {
            return [$ability => $policy ? $policy->{$ability}(...($params[$ability] ?? [])) : false];
        });
    }

    public function vendorAuthorization(): Attribute
    {
        $params = [
            'update' => [Auth::user(), $this],
            'create' => [Auth::user()],
            'delete' => [Auth::user(), $this],
        ];

        return Attribute::make(
            get: fn ($value) => $this->authorizations(Constants::vendorItemModule, array_keys($params), PsVendorPolicy::class, $params),
        );

    }

    public function authorization(): Attribute
    {
        $params = [
            'update' => [Auth::user(), $this],
            'create' => [Auth::user()],
            'delete' => [Auth::user(), $this],
        ];
        return Attribute::make(
            get: fn ($value) => $this->authorizations(Constants::itemModule, array_keys($params), PsPolicy::class, $params),
        );
    }
}
