<?php

namespace Modules\Core\Entities;

use App\Models\User;
use Modules\Core\Entities\Item;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'name', 'description', 'email', 'lat', 'lng', 'cod_email', 'cod_enable', 'status', 'payment_status_id', 'is_featured', 'featured_date', 'overall_tax_label', 'overall_tax_value', 'shipping_tax_label', 'shipping_tax_value', 'whatsapp_no', 'refund_policy', 'trems', 'facebook', 'messenger', 'instagram', 'youtube', 'phone_no', 'address', 'touch_count', 'checkout_with_email', 'multi_page_checkout', 'checkout_with_whatsapp', 'overall_rating', 'price_level', 'added_date', 'added_user_id', 'updated_date', 'updated_user_id', 'updated_flag'];

    protected $table = "psx_shops";

    const CREATED_AT = 'added_date';
    const UPDATED_AT = 'updated_date';

    const status = "status";
    const tableName = 'psx_shops';
    const name = "name";

    protected static function newFactory()
    {
        // return \Modules\Core\Database\factories\ShopFactory::new();
    }

    public function item(){
        return $this->hasMany(Item::class);
    }

    public function owner(){
        return $this->belongsTo(User::class, 'added_user_id');
    }

    public function editor(){
        return $this->belongsTo(User::class, 'updated_user_id');
    }
}
