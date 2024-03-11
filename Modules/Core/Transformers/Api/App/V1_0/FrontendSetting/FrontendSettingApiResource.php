<?php

namespace Modules\Core\Transformers\Api\App\V1_0\FrontendSetting;
use Modules\Core\Entities\BackendSetting;
use Modules\Core\Entities\CoreImage;
use Modules\Core\Transformers\Api\App\V1_0\CoreImage\CoreImageApiResource;

use Illuminate\Http\Resources\Json\JsonResource;

class FrontendSettingApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $available_languages = [
            ['language_code'=> 'en', 'country_code' => 'US', 'name' => 'English'],
            ['language_code'=> 'ar', 'country_code' => 'DZ', 'name' => 'Arabic'],
            ['language_code'=> 'hi', 'country_code' => 'IN', 'name' => 'Hindi'],
            ['language_code'=> 'de', 'country_code' => 'DE', 'name' => 'German'],
            ['language_code'=> 'es', 'country_code' => 'ES', 'name' => 'Spainish'],
            ['language_code'=> 'fr', 'country_code' => 'FR', 'name' => 'French'],
            ['language_code'=> 'id', 'country_code' => 'ID', 'name' => 'Indonesian'],
            ['language_code'=> 'it', 'country_code' => 'IT', 'name' => 'Italian'],
            ['language_code'=> 'ja', 'country_code' => 'JP', 'name' => 'Japanese'],
            ['language_code'=> 'ko', 'country_code' => 'KR', 'name' => 'Korean'],
            ['language_code'=> 'ms', 'country_code' => 'MY', 'name' => 'Malay'],
            ['language_code'=> 'pt', 'country_code' => 'PT', 'name' => 'Portuguese'],
            ['language_code'=> 'ru', 'country_code' => 'RU', 'name' => 'Russian'],
            ['language_code'=> 'th', 'country_code' => 'TH', 'name' => 'Thai'],
            ['language_code'=> 'tr', 'country_code' => 'TR', 'name' => 'Turkish'],
            ['language_code'=> 'zh', 'country_code' => 'CN', 'name' => 'Chinese'],
        ];

        $selected_language = explode(',', trim($this->selected_language));

		foreach($available_languages as $language){
			if(!in_array($language['language_code'], $selected_language)){
				$exclude_language[] = array('language_code' => $language['language_code'], 'country_code' => $language['country_code'], 'name' => $language['name']);
			}
			if($language['language_code'] == $this->default_language){
				$default_language = array('language_code' => $language['language_code'], 'country_code' => $language['country_code'], 'name' => $language['name']);
			}
		}

        $firebaseConfig = new \stdClass();
        $firebaseConfig->apiKey = '000000000000000000000000000000000000000';
        $firebaseConfig->authDomain = 'sooq.firebaseapp.com';
        $firebaseConfig->databaseURL = 'https://sooq.firebaseio.com';
        $firebaseConfig->projectId = 'sooq';
        $firebaseConfig->storageBucket = 'sooq.appspot.com';
        $firebaseConfig->messagingSenderId = '000000000000';
        $firebaseConfig->appId = '1:000000000000:web:0000000000000000000000';
        $firebaseConfig->measurementId = 'G-0000000000';

        $firebaseConfig = json_encode($firebaseConfig);

        $firebaseConfigStr = $this->firebase_config;
        if($this->firebase_config == null || $this->firebase_config == ''){
            $firebaseConfigStr = $firebaseConfig;
        }else{

            $firebaseConfigObj = json_decode($firebaseConfigStr);
            if (!is_object($firebaseConfigObj) || !isset($firebaseConfigObj->apiKey)) {
                $firebaseConfigStr = $firebaseConfig;
            }
        }

        $backendSetting = BackendSetting::select('map_key', 'is_google_map', 'is_open_street_map')->first();

        return [
            'map_key' => (string)$backendSetting->map_key,
            'google_playstore_url' => isset($this->google_playstore_url) ? (string) $this->google_playstore_url : '',
            'app_store_url' => isset($this->app_store_url) ? (string) $this->app_store_url : '',
            'gps_enable' => isset($this->gps_enable) ? (string) $this->gps_enable : '',
            'show_main_menu' => isset($this->show_main_menu) ? (string) $this->show_main_menu : '',
            'show_special_collections' => isset($this->show_special_collections) ? (string) $this->show_special_collections : '',
            'show_featured_items' => isset($this->show_featured_items) ? (string) $this->show_featured_items : '',
            'show_best_choice_slider' => isset($this->show_best_choice_slider) ? (string) $this->show_best_choice_slider : '',
            'fcm_server_key' => isset($this->fcm_server_key) ? (string) $this->fcm_server_key : '',
            'firebase_config' => $firebaseConfigStr,
            'firebase_web_push_key_pair' => isset($this->firebase_web_push_key_pair) ? (string) $this->firebase_web_push_key_pair : '',
            'ad_client' => isset($this->ad_client) ? (string) $this->ad_client : '',
            'ad_slot' => isset($this->ad_slot) ? (string) $this->ad_slot : '',
            'copyright' => isset($this->copyright) ? (string) $this->copyright : '',
            'price_format' => isset($this->price_format) ? (string) $this->price_format : '',
            'banner_src' => isset($this->banner_src) ? (string) $this->banner_src : '',
            'mile' => isset($this->mile) ? (string) $this->mile : '',
            'is_enable_video_setting' => isset($this->is_enable_video_setting) ? (string) $this->is_enable_video_setting : '',
            'show_user_profile' => isset($this->show_user_profile) ? (string) $this->show_user_profile : '',
            'no_filter_with_location_on_map' => isset($this->no_filter_with_location_on_map) ? (string) $this->no_filter_with_location_on_map : '',
            'enable_notification' => isset($this->enable_notification) ? (string) $this->enable_notification : '',
            'google_setting' => isset($this->google_setting) ? (string) $this->google_setting : '',
            'app_store_setting' => isset($this->app_store_setting) ? (string) $this->app_store_setting : '',
            'google_map' => (string)$backendSetting->is_google_map,
            'open_street_map' => (string)$backendSetting->is_open_street_map,
            'default_language' => isset($default_language) ? $default_language : '',
            'exclude_language' => isset($exclude_language) ? $exclude_language : '',
            'promote_first_choice_day' => isset($this->promote_first_choice_day) ? (string) $this->promote_first_choice_day : '',
            'promote_second_choice_day' => isset($this->promote_second_choice_day) ? (string) $this->promote_second_choice_day : '',
            'promote_third_choice_day' => isset($this->promote_third_choice_day) ? (string) $this->promote_third_choice_day : '',
            'promote_fourth_choice_day' => isset($this->promote_fourth_choice_day) ? (string) $this->promote_fourth_choice_day : '',
            'frontend_version_no' => isset($this->frontend_version_no) ? (string) $this->frontend_version_no : '',
            'is_demo_for_payment' => isset($this->is_demo_for_payment) ? (string) $this->is_demo_for_payment : '',
            "frontend_logo" => new CoreImageApiResource(isset($this->frontend_logo[0]) && $this->frontend_logo[0] ? $this->frontend_logo[0]: []),
            "frontend_icon" => new CoreImageApiResource(isset($this->frontend_icon[0]) && $this->frontend_icon[0] ? $this->frontend_icon[0]: []),
            "frontend_banner" => new CoreImageApiResource(isset($this->frontend_banner[0]) && $this->frontend_banner[0] ? $this->frontend_banner[0]: []),
            "app_branding_image" => new CoreImageApiResource(isset($this->app_branding_image[0]) && $this->app_branding_image[0] ? $this->app_branding_image[0]: []),
            "frontend_meta_title" => isset($this->frontend_meta_title) ? $this->frontend_meta_title: '',
            "frontend_meta_description" => isset($this->frontend_meta_description) ? $this->frontend_meta_description : '',
            "frontend_meta_image" => new CoreImageApiResource(isset($this->frontend_meta_image[0]) && $this->frontend_meta_image[0] ? $this->frontend_meta_image[0]: []),
       ];
    }
}
