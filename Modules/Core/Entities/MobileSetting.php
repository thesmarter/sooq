<?php

namespace Modules\Core\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Casts\Attribute;
class MobileSetting extends Model
{
    use HasFactory;

    const CREATED_AT = 'added_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = ['id', 'apple_appstore_url', 'ios_appstore_id', 'is_show_item_video', 'google_playstore_url', 'is_show_admob', 'fb_key', 'date_format', 'price_format', 'default_razor_currency', 'is_razor_support_multi_currency', 'is_show_subcategory','is_show_discount', 'show_phone_login', 'show_google_login','show_apple_login', 'show_facebook_login', 'is_use_thumbnail_as_placeholder', 'is_use_google_map', 'item_detail_view_count_for_ads', 'is_show_ads_in_item_detail', 'after_item_count_admob_once', 'blue_mark_size', 'block_item_loading_limit', 'follower_item_loading_limit', 'block_slider_loading_limit', 'featured_item_loading_limit', 'popular_item_loading_limit', 'recent_item_loading_item', 'category_loading_limit', 'discount_item_loading_limit', 'mile', 'video_duration', 'is_show_own_info', 'no_filter_with_location_on_map', 'chat_image_size', 'profile_image_size', 'upload_image_size', 'promote_first_choice_day', 'promote_second_choice_day', 'promote_third_choice_day', 'promote_fourth_choice_day', 'lat', 'lng', 'collection_item_loading_limit', 'shop_loading_limit', 'show_main_menu', 'show_special_collections', 'show_featured_items', 'show_best_choice_slider', 'default_flutter_wave_currency', 'default_order_time', 'trending_item_loading_limit', 'version_no', 'version_force_update', 'version_title', 'version_message', 'version_need_clear_data', 'deli_boy_version_no', 'deli_boy_version_force_update', 'deli_boy_version_title', 'deli_boy_version_message', 'deli_boy_version_need_clear_data', 'added_date', 'added_user_id', 'updated_date', 'updated_user_id', 'updated_flag' ];

    protected $table = "psx_mobile_settings";

    protected static function newFactory()
    {
        // return \Modules\Core\Database\factories\MobileSettingFactory::new();
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
