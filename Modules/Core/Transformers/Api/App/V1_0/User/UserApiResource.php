<?php

namespace Modules\Core\Transformers\Api\App\V1_0\User;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Modules\BlockUser\Entities\BlockUser;
use Modules\Core\Constants\Constants;
use Modules\Core\Entities\Item;
use Modules\Core\Entities\SystemConfig;
use Modules\Core\Entities\UserInfo;
use Modules\Core\Transformers\Api\App\V1_0\User\UserInfoApiResource;
use Modules\FollowUser\Entities\FollowUser;
use Modules\Rating\Entities\Rating;
use Modules\Core\Entities\BackendSetting;

class UserApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $post_count = __('unlimited');
        $active_item_count = 0;
        $rating_std = '';
        $follower_count = 0;
        $following_count = 0;
        $is_followed = 0;
        $is_blocked = 0;
        $is_verify_blue_mark = 0;
        if(isset($this->id) && !empty($this->id)){
            //For rating details
            $total_rating_count = 0;
            $total_rating_value = 0;

            $five_star_count = 0;
            $five_star_percent = 0;

            $four_star_count = 0;
            $four_star_percent = 0;

            $three_star_count = 0;
            $three_star_percent = 0;

            $two_star_count = 0;
            $two_star_percent = 0;

            $one_star_count = 0;
            $one_star_percent = 0;

            //Rating Total how much ratings for this product
            $conds_rating['to_user_id'] = $this->id;

            $ratings = Rating::where($conds_rating)->get();
            $total_rating_count = (float)empty($ratings)? 0: count($ratings);
            $sum_rating_value = Rating::select(DB::raw('SUM(rating) as total'))->where($conds_rating)->first()->total;


            //Rating Value such as 3.5, 4.3 and etc
            if ($total_rating_count > 0) {
                $total_rating_value = number_format((float) ($sum_rating_value / $total_rating_count), 1, '.', '');
            } else {
                $total_rating_value = 0;
            }

            //For 5 Stars rating
            $conds_five_star_rating['rating'] = 5;
            $conds_five_star_rating['to_user_id'] = $this->id;
            $five_star_ratings = Rating::where($conds_five_star_rating)->get();
            $five_star_count = empty($five_star_ratings)? 0: count($five_star_ratings);
            if ($total_rating_count > 0) {
                $five_star_percent = number_format((float) ((100 / $total_rating_count) * $five_star_count), 1, '.', '');
            } else {
                $five_star_percent = 0;
            }

            //For 4 Stars rating
            $conds_four_star_rating['rating'] = 4;
            $conds_four_star_rating['to_user_id'] = $this->id;
            $four_star_ratings = Rating::where($conds_four_star_rating)->get();
            $four_star_count = empty($four_star_ratings)?0:count($four_star_ratings);
            if ($total_rating_count > 0) {
                $four_star_percent = number_format((float) ((100 / $total_rating_count) * $four_star_count), 1, '.', '');
            } else {
                $four_star_percent = 0;
            }

            //For 3 Stars rating
            $conds_three_star_rating['rating'] = 3;
            $conds_three_star_rating['to_user_id'] = $this->id;
            $three_star_ratings = Rating::where($conds_three_star_rating)->get();
            $three_star_count = empty($three_star_ratings) ? 0 : count($three_star_ratings);

            if ($total_rating_count > 0) {
                $three_star_percent = number_format((float) ((100 / $total_rating_count) * $three_star_count), 1, '.', '');
            } else {
                $three_star_percent = 0;
            }

            //For 2 Stars rating
            $conds_two_star_rating['rating'] = 2;
            $conds_two_star_rating['to_user_id'] = $this->id;
            $two_star_ratings = Rating::where($conds_two_star_rating)->get();
            $two_star_count = empty($two_star_ratings) ? 0 : count($two_star_ratings);

            if ($total_rating_count > 0) {
                $two_star_percent = number_format((float) ((100 / $total_rating_count) * $two_star_count), 1, '.', '');
            } else {
                $two_star_percent = 0;
            }

            //For 1 Stars rating
            $conds_one_star_rating['rating'] = 1;
            $conds_one_star_rating['to_user_id'] = $this->id;
            $one_star_ratings = Rating::where($conds_one_star_rating)->get();
            $one_star_count = empty($one_star_ratings)? 0 : count($one_star_ratings);
            if ($total_rating_count > 0) {
                $one_star_percent = number_format((float) ((100 / $total_rating_count) * $one_star_count), 1, '.', '');
            } else {
                $one_star_percent = 0;
            }

            $rating_std = new \stdClass();
            $rating_std->five_star_count = (string)$five_star_count;
            $rating_std->five_star_percent = (string)$five_star_percent;

            $rating_std->four_star_count = (string)$four_star_count;
            $rating_std->four_star_percent = (string)$four_star_percent;

            $rating_std->three_star_count = (string)$three_star_count;
            $rating_std->three_star_percent = (string)$three_star_percent;

            $rating_std->two_star_count = (string)$two_star_count;
            $rating_std->two_star_percent = (string)$two_star_percent;

            $rating_std->one_star_count = (string)$one_star_count;
            $rating_std->one_star_percent = (string)$one_star_percent;

            $rating_std->total_rating_count = (string)$total_rating_count;
            $rating_std->total_rating_value = (string)$total_rating_value;

            // For remaining post count (come from custom field)
            $remainingPostUser = UserInfo::where(['user_id' => $this->id, 'core_keys_id' => Constants::usrRemainingPost])->first();

            // For active item count
            $active_conds['added_user_id'] = $this->id;
            $active_conds['status'] = Constants::publish;
            $active_item_count = Item::where($active_conds)->count();

            // For post count is limited or unlimited
            $systemConfig = SystemConfig::first();
            $post_count = __('unlimited');
            if($systemConfig){
                if($systemConfig->is_paid_app==1 && $this->role_id == Constants::normalUserRoleId){
                    $post_count = __('limited');
                }
            }

            // For verify blue mark (come from custom field)
            $is_verify_blue_mark = 0;
            $bluemark_conds['core_keys_id'] = Constants::usrIsVerifyBlueMark;
            $bluemark_conds['user_id'] = $this->id;
            $blueMarkUser = UserInfo::where($bluemark_conds)->first();
            if($blueMarkUser){
                $is_verify_blue_mark = $blueMarkUser->value;
            }

            // For follower count
            $follower_count = 0;
            $follower_conds['followed_user_id'] = $this->id;
            $follower_count = FollowUser::where($follower_conds)->count();

            // For following count
            $following_count = 0;
            $following_conds['user_id'] = $this->id;
            $following_count = FollowUser::where($following_conds)->count();

            // For is_followed and is_blocked
            $is_followed = 0;
            $is_blocked = 0;
            if(isset($this->followed_user_id) && !empty($this->followed_user_id) && isset($this->following_user_id) && !empty($this->following_user_id)){
                $followConds['user_id'] = $this->following_user_id;
                $followConds['followed_user_id'] = $this->followed_user_id;
                $followUser = FollowUser::where($followConds)->count();
                if ($followUser > 0) {
                    $is_followed = 1;
                }
                
                $blockConds['from_block_user_id'] = $this->following_user_id;
                $blockConds['to_block_user_id'] = $this->followed_user_id;
                $blockUser = BlockUser::where($blockConds)->count();
                if ($blockUser > 0) {
                    $is_blocked = 1;
                }
            }

            if(isset($request->login_user_id) && !empty($request->login_user_id) && $this->id != $request->login_user_id && $this->login_user_id != 'nologinuser'){
                $followConds['user_id'] = $request->login_user_id;
                $followConds['followed_user_id'] = $this->id;
                $followUser = FollowUser::where($followConds)->count();
                if ($followUser > 0) {
                    $is_followed = 1;
                }

                $blockConds['from_block_user_id'] = $request->login_user_id;
                $blockConds['to_block_user_id'] = $this->id;
                $blockUser = BlockUser::where($blockConds)->count();
                if ($blockUser > 0) {
                    $is_blocked = 1;
                }
            }
        }

        if(isset($this->code) && $this->code != '' && $this->code != null){
            $has_code = '1';
        }else{
            $has_code = '0';
        }
        if(BackendSetting::first()->email_verification_enabled == 1 && isset($this->code) && $this->code != '' && $this->code != null){
            $need_verify = '1';
        }else{
            $need_verify = '0';
        }
        return [
            'id' => isset($this->id)?(string)$this->id:'',
            'user_is_sys_admin' => isset($this->user_is_sys_admin)? (string)$this->user_is_sys_admin:'',
            'email_verified_at' => isset($this->email_verified_at)?(string)$this->email_verified_at:'',
            'facebook_id' => isset($this->facebook_id)?(string)$this->facebook_id:'',
            'google_id' => isset($this->google_id)?(string)$this->google_id:'',
            'phone_id' => isset($this->phone_id)?(string)$this->phone_id:'',
            'apple_id' => isset($this->apple_id)?(string)$this->apple_id:'',
            'name' => isset($this->name)?(string)$this->name:'',
            'username' => isset($this->username)?(string)$this->username:'',
            'email' => isset($this->email)?(string)$this->email:'',
            'user_phone' => isset($this->user_phone)?(string)$this->user_phone:'',
            'user_address' => isset($this->user_address)?(string)$this->user_address:'',
            // 'user_password' => isset($this->user_password)?(string)$this->user_password:'',
            'user_about_me' => isset($this->user_about_me)?(string)$this->user_about_me:'',
            'user_cover_photo' => isset($this->user_cover_photo)?(string)$this->user_cover_photo:'',
            'role_id' => isset($this->role_id)?(string)$this->role_id:'',
            'status' => isset($this->status)?(string)$this->status:'',
            'is_banned' => isset($this->is_banned)?(string)$this->is_banned:'',
            'has_code' => $has_code,
            'need_verify' => $need_verify,
            'hasPassword'=>isset($this->password)?(string)"true":"false",
            'overall_rating' => isset($this->overall_rating)?(string)$this->overall_rating:'',
            'is_show_email' => isset($this->is_show_email)?(string)$this->is_show_email:'',
            'is_show_phone' => isset($this->is_show_phone)?(string)$this->is_show_phone:'',
            'is_shop_admin' => isset($this->is_shop_admin)?(string)$this->is_shop_admin:'',
            'is_city_admin' => isset($this->is_city_admin)?(string)$this->is_city_admin:'',
            'user_lat' => isset($this->user_lat)?(string)$this->user_lat:'',
            'user_lng' => isset($this->user_lng)?(string)$this->user_lng:'',
            'added_date' => isset($this->added_date)?(string)$this->added_date:'',
            'added_date_timestamp' => isset($this->added_date_timestamp)?(string)$this->added_date_timestamp:'',
            'verify_types' => isset($this->verify_types)?(string)$this->verify_types:'',
            "remaining_post" => isset($this->id)?(empty($remainingPostUser)?(string)'0':(string)$remainingPostUser->value):'',
            "post_count" => isset($this->id)?(string)$post_count:'',
            "active_item_count" => isset($this->id)?(string)$active_item_count:'0',
            "rating_count" => isset($this->id)?(string) (Rating::where(['to_user_id' => $this->id])->count()): '',
            "rating_details" => isset($this->id)?$rating_std:'',
            "follower_count" => isset($this->id)?(string)$follower_count:'',
            "following_count" => isset($this->id)?(string)$following_count:'',
            "is_followed" => isset($this->id)?(string)$is_followed:'',
            "is_blocked" => isset($this->id)?(string)$is_blocked:'',
            "is_verify_blue_mark" => isset($this->id)?(string)$is_verify_blue_mark:'',
            "userRelation" => UserInfoApiResource::collection(isset($this->userRelation) && count($this->userRelation) >0 ? $this->whenLoaded('userRelation') : ['xxx']),
            "added_date_str" => isset($this->added_date)?(string)$this->added_date->diffForHumans():'',
            "is_empty_object" => $this->when(!isset($this->id),1),

        ];
    }
}
