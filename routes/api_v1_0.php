<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Modules\Core\Entities\VendorApplication;
use App\Http\Controllers\CustomLoginController;
use Modules\Blog\Http\Controllers\Backend\Rests\App\V1_0\BlogApiController;
use Modules\Rating\Http\Controllers\Backend\Rests\App\V1_0\RatingApiController;
use Modules\Chat\Http\Controllers\Backend\Rests\App\V1_0\Chat\ChatApiController;
use Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\User\UserApiController;
use Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\About\AboutApiController;
use Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\Color\ColorApiController;
use Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\Image\ImageApiController;
use Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\Table\TableApiController;
use Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\Touch\TouchApiController;
use Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\Vendor\VendorApiController;
use Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\AppInfo\AppInfoApiController;
use Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\Product\ProductApiController;
use Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\Project\ProjectApiController;
use Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\Category\CategoryApiController;
use Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\Currency\CurrencyApiController;
use Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\Favourite\FavouriteApiController;
use Modules\ComplaintItem\Http\Controllers\Backend\Rests\App\V1_0\ComplaintItemApiController;
use Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\Subcategory\SubcategoryApiController;
use Modules\BlockUser\Http\Controllers\Backend\Rests\App\V1_0\BlockUser\BlockUserApiController;
use Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\LocationCity\LocationCityApiController;
use Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\SearchHistory\SearchHistoryApiController;
use Modules\FollowUser\Http\Controllers\Backend\Rests\App\V1_0\FollowUser\FollowUserApiController;
use Modules\ContactUsMessage\Http\Controllers\Backend\Rests\App\V1_0\ContactUsMessageApiController;
use Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\MobileLanguage\MobileLanguageApiController;
use Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\SubCatSubscribe\SubCatSubscribeApiController;
use Modules\ItemPromotion\Http\Controllers\Backend\Rests\App\V1_0\ItemPromotion\PaidItemApiController;
use Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\LocationTownship\LocationTownshipApiController;
use Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\PhoneCountryCode\PhoneCountryCodeApiController;
use Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\VendorApplication\VendorApplicationApiController;
use Modules\Package\Http\Controllers\Backend\Rests\App\V1_0\Package\PackageBoughtTransactionApiController;
use Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\CoreAndCustomField\CoreAndCustomFieldApiController;

use Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\MobileLanguageString\MobileLanguageStringApiController;
use Modules\PushNotificationMessage\Http\Controllers\Backend\Rests\App\V1_0\PushNotificationMessageApiController;
use Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\PushNotificationToken\PushNotificationTokenApiController;
use Modules\Payment\Http\Controllers\Backend\Rests\App\V1_0\OfflinePaymentSetting\OfflinePaymentSettingApiController;
use Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\PushNotificationReadUser\PushNotificationReadUserApiController;
use Modules\Payment\Http\Controllers\Backend\Rests\App\V1_0\PackageInAppPurchaseSetting\PackageInAppPurchaseSettingApiController;
use Modules\Payment\Http\Controllers\Backend\Rests\App\V1_0\VendorSubscriptionPlanSetting\VendorSubscriptionPlanSettingApiController;
use Modules\Package\Http\Controllers\Backend\Rests\App\V1_0\VendorSubscriptionPlan\VendorSubscriptionPlanBoughtTransactionApiController;
use Modules\StoreFront\VendorPanel\Http\Controllers\Backend\Rests\App\V1_0\Order\OrderApiController;
use Modules\StoreFront\VendorPanel\Http\Controllers\Backend\Rests\App\V1_0\ShippingAndBilling\ShippingAndBillingApiController;
use Modules\StoreFront\VendorPanel\Http\Controllers\Backend\Rests\App\V1_0\VendorInfo\VendorInfoApiController;
use Modules\StoreFront\VendorPanel\Http\Controllers\Backend\Rests\App\V1_0\VendorItem\VendorItemBoughtTransactionApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// this api include token and policy layer testing
Route::middleware(['auth:sanctum', 'isUserAndDeliboyMobileToken', 'checkAuthUserForApi'])->name("api.")->group(function () {
    // Route::apiResource('/sub-category', SubcategoryApiController::class, array("as" => "api"));
    Route::get('/token', [SubcategoryApiController::class, "token"]);
    // this apiResource route will support for SubcategoryApiController => index, store, show, update, destroy - KZL test
});

// with laravel jebstream token
Route::middleware(['auth:sanctum', 'isUserMobileToken', 'checkAuthUserForApi'])->name("api.")->group(function () {

    // Route::prefix('table')->controller(TableApiController::class)->group(function () {
    //     Route::get('for_connection_check', 'forConnectionCheck');
    //     Route::post('take_builder_api_key', 'takeBuilderApiKey');
    // });
    // Route::apiResource('table', TableApiController::class, array("as" => "api"));

    // ProductApiController
    Route::prefix('product')->controller(ProductApiController::class)->group(function () {
        Route::get("/create", 'create');
        Route::get("/get_product", 'getProductById');
        Route::get("/get_related", 'getRelatedTrending');
        Route::post("search", "search");
        Route::post("/delete_item", "deleteItem");
        Route::post("/sold_out_item_detail", 'soldOutFromItemDetail');
        Route::post("/item_status_change", "statusChangeFromApi");
        Route::post("/all_search", 'allSearch');

        // Custom Field
        Route::get("/customize-header/{customize_header_key}/customize-details", "customizeDetails");
        Route::get("/ui-types/for-customize-details", "customizeHeadersForCustomizeDetails");

        // Item Image, Icon and Video
        Route::post('/cover/upload', "coverUpload");
        Route::post('/icon/upload', "iconUpload");
        Route::post('/video/upload', "videoUpload");
        Route::post("/delete_image", 'destroyImage');
        Route::post("/delete_video_and_icon", 'destroyVideo');
        Route::post("reorder_images", "reorderImages");
        Route::get("/gallery_list", 'getGalleryList');

    });
    Route::apiResource('product', ProductApiController::class, array("as" => "api"));








    // category api
    Route::prefix('category')->controller(CategoryApiController::class)->group(function () {
        Route::post("/search", 'search');
    });

    // location city api
    Route::prefix('location-city')->controller(LocationCityApiController::class)->group(function () {
        Route::post("/search", 'search');
    });

    // location township api
    Route::prefix('location-township')->controller(LocationTownshipApiController::class)->group(function () {
        Route::post("/search", 'search');
    });

    // rating api
    Route::prefix('rating')->controller(RatingApiController::class)->group(function () {
        Route::post("/", 'rating');
        Route::post("/search", 'search');
    });

    // blog api
    Route::prefix('blog')->controller(BlogApiController::class)->group(function () {
        Route::post("/search", 'search');
        Route::get("/detail", "detail");
    });

    // contact us message api
    Route::prefix('contact')->controller(ContactUsMessageApiController::class)->group(function () {
        Route::post("/", 'contact');
        Route::get("/get_in_touch", "getInTouchForContact");
    });

    // currency api controller
    Route::apiResource('/currency', CurrencyApiController::class, array("as" => "api"));

    // image api controller
    Route::apiResource('image', ImageApiController::class, array("as" => "api"));

    // block api controller
    Route::prefix('block')->controller(BlockUserApiController::class)->group(function () {
        Route::post("/block_user", 'blockUser');
        Route::post("/unblock_user", 'unblockUser');
        Route::get("/get_blocked_user_by_loginuser", 'getBlockedUser');
    });
    Route::apiResource("/block", BlockUserApiController::class, array("as" => "api"));



    // paid item api
    Route::prefix('paid_item')->controller(PaidItemApiController::class)->group(function () {
        Route::post("/destroy", 'destroy');
        Route::get("/get_purchased_history", 'getPurchasedHistory');
    });
    Route::apiResource("/paid_item", PaidItemApiController::class, array("as" => "api"));

    // package api
    Route::prefix('package_bought')->controller(PackageBoughtTransactionApiController::class)->group(function () {
        Route::post("/search", 'search');
        Route::post("/destroy", 'destroy');
    });
    Route::apiResource("/package_bought", PackageBoughtTransactionApiController::class, array("as" => "api"));

    // vendor subscription plan bought api
    Route::apiResource("/vendor_subscription_bought", VendorSubscriptionPlanBoughtTransactionApiController::class, array("as" => "api"));

    // Paypal api
    Route::prefix('paypal')->controller(PaidItemApiController::class)->group(function () {
        Route::get("/token", 'token');
    });

    // offline Payment api
    Route::prefix('offline_payment')->controller(OfflinePaymentSettingApiController::class)->group(function () {
        Route::get("/", 'index');
    });

    // complaint item api
    Route::apiResource("/complaint_item", ComplaintItemApiController::class, array("as" => "api"));

    // Package IAP api
    Route::prefix('package_in_app_purchase')->controller(PackageInAppPurchaseSettingApiController::class)->group(function () {
        Route::get("/", 'index');
        Route::get("/package_purchased_count", 'packagePurchasedCount');
    });

    // Vendor Subscription Plan api
    Route::prefix('vendor_subscription_plan')->controller(VendorSubscriptionPlanSettingApiController::class)->group(function () {
        Route::get("/", 'index');
    });

    // push noti token api
    Route::prefix('push_noti_token')->controller(PushNotificationTokenApiController::class)->group(function () {
        Route::post("/register_noti", 'registerNoti');
        Route::post("/unregister_noti", 'unregisterNoti');
        Route::post("/test_status_code", 'testStatusCode');
    });

    // push noti read user
    Route::prefix('push_noti_read_user')->controller(PushNotificationReadUserApiController::class)->group(function () {
        Route::post("/is_read", 'isReadNoti');
        Route::post("/is_unread", 'isUnreadNoti');
        Route::post("/destroy", 'destroy');
    });

    // push noti message
    Route::prefix('push_noti_message')->controller(PushNotificationMessageApiController::class)->group(function () {
        Route::post("/all_notis", 'allNotis');
        Route::get("/get_detail", 'getNotiDetail');
    });

    // subcategory api
    Route::prefix('sub_category')->controller(SubcategoryApiController::class)->group(function () {
        Route::post("/search", 'search');
    });

    // subcategory subscribe api
    Route::prefix('subcat_subscribe')->controller(SubCatSubscribeApiController::class)->group(function () {
        Route::post("/subcategory_subscribe", 'subCategorySubscribe');
        Route::get("/is_user_subscribed", 'isUserSubscribed');
    });

    // item touch api
    Route::prefix('touch')->controller(TouchApiController::class)->group(function () {
        Route::post("/item_touch", 'addItemTouchCount');
    });

    // item favourite api
    Route::prefix('favourite')->controller(FavouriteApiController::class)->group(function () {
        Route::post("/item_favourite", 'favouriteItem');
        Route::get("/get_favourite", 'getAllFavouriteItem');
    });

    // search history api
    Route::prefix('search_history')->controller(SearchHistoryApiController::class)->group(function () {
        Route::post("/search", 'search');
        Route::post("/destroy", 'destroy');

        Route::get("/search_category_histories", "searchCategoryHistory");
        Route::post("/destroy_category_histories", "destroyCategoryHistory");

        Route::get("/search_item_histories", "searchItemHistory");
        Route::post("/destroy_item_histories", "destroyItemHistory");

        Route::get("/search_subCat_histories", "searchSubCatHistory");
        Route::post("/destroy_subCat_histories", "destroySubCatHistory");

    });

    //vendor
    Route::prefix('vendor_application')->controller(VendorApplicationApiController::class)->group(function() {
        Route::post('/submit', 'submitApplication');
    });

    Route::prefix('vendor')->controller(VendorApiController::class)->group(function() {
        Route::get('get_vendors', 'getVendors');
        Route::get('get_vendor', 'getVendorById');
        Route::post('get_vendor_branches', 'getVendorBranches');
        Route::post("search", "search");
    });

    // vendor single item checkout
    Route::prefix('vendor')->controller(ShippingAndBillingApiController::class)->group(function() {
        Route::post('order_and_billing_info/submit', 'storeShippingAndBillingInfo');
    });

    Route::prefix('vendor')->controller(VendorItemBoughtTransactionApiController::class)->group(function() {
        Route::post('item_bought', 'store');
        Route::get('paypal/token', 'vendorPaypalNonceGenegrate');
    });

    Route::prefix('vendor')->controller(OrderApiController::class)->group(function() {
        Route::get('get_order_summary', 'getOrderSummary');
    });

    Route::prefix('vendor')->controller(VendorInfoApiController::class)->group(function() {
        Route::get('info', 'getVendorInfo');
    });



});

// with laravel jetstream token
Route::middleware(['auth:sanctum', 'isBuilderToken', 'checkAuthUserForApi'])->name('api.')->group(function(){
    Route::prefix('table')->controller(TableApiController::class)->group(function () {
        Route::get('for_connection_check', 'forConnectionCheck');
        Route::post('take_builder_api_key', 'takeBuilderApiKey');
        Route::post('auto_sync','tableAutoSync');
    });
    Route::apiResource('table', TableApiController::class, array("as" => "api"));

    // for project
    Route::prefix('project')->controller(ProjectApiController::class)->group(function(){
        Route::post('update_project_api_key','updateProjectApiKey');
    });

    // for fields
    Route::prefix('fields')->controller(CoreAndCustomFieldApiController::class)->group(function(){
        Route::post('sync_fields','syncFields');
    });

});

// about us api
Route::prefix('about')->controller(AboutApiController::class)->group(function () {
    Route::get("/", 'index');
});

 // follow api controller
 Route::prefix('follow')->controller(FollowUserApiController::class)->group(function () {
    Route::post("/follow_user", 'followUser');
    Route::get("/get_follower_by_loginuser", 'getFollower');
    Route::post("/search_follow_user", 'searchFollower');
    Route::post("/item_from_follower", 'itemListFromFollower');
});
Route::apiResource("/follow", FollowUserApiController::class, array("as" => "api"));

// MobileLanguageApiController
Route::prefix('mobile_language')->controller(MobileLanguageApiController::class)->group(function () {
    Route::post("/search", 'search');
    Route::get("/langs", 'langs');
});
Route::apiResource("/mobile_language", MobileLanguageApiController::class, array("as" => "api"));

// MobileLanguageStringApiController
Route::apiResource("/mobile_language/{mobile_language}/mobile_language_string", MobileLanguageStringApiController::class, array("as" => "api"));

// chat api
Route::prefix('chat')->controller(ChatApiController::class)->group(function () {
    Route::post("/", 'store');
    Route::post("/update_price", 'updatePrice');
    Route::post("/get_chat_history", 'show');
    Route::post("/chat_image_upload", "chatImageUpload");
    Route::post("/reset_count", "resetCount");
    Route::post("/unread_count", "unreadCount");
    Route::post("/get_offer_list", "getOfferList");
    Route::post("/is_user_bought", "isUserBought");
    Route::post("/update_accept", "updateAccept");
    Route::post("/get_buyer_seller_list", "getBuyerSellerList");
    Route::post("/item_sold_out", "itemSoldOut");

    Route::post("/chat_image_delete", "chatImageDelete");
});

Route::post('/existuser', [CustomLoginController::class, 'existUser'])->name('existUser');

Route::post('/CreateUser', [CustomLoginController::class, 'createUser'])->name('CreateUser');

Route::post('/set_username_password', [UserApiController::class, 'setUsernamePassword'])->name('setUsernamePassword');

Route::prefix('user')->controller(UserApiController::class)->group(function () {
    Route::get("/create", 'create');
    Route::post("/login", 'login');
    Route::post("/logout", 'logout');
    Route::post("/register", 'register');
    // Route::post("/user_register", 'userRegister');
    Route::post("/google_register", 'googleRegister');
    Route::post("/phone_register", 'phoneRegister');
    Route::post("/apple_register", 'appleRegister');
    Route::post("/facebook_register", 'facebookRegister');
    Route::post("/user_image_upload", 'userImageUpload');
    Route::post("/verify_code", 'verifyCode');
    Route::post("/request_code", 'requestCode');
    Route::post("/reset_password", 'resetPassword');
    Route::post("/forgot_password", 'forgotPassword');
    Route::post("/forgot_password_verify", 'forgotPasswordVerify');
    Route::post("/update_password", 'userPasswordUpdate');
    Route::post("/update_forgot_password", 'userForgotPasswordUpdate');
    Route::post("/profile_update", 'userProfileUpdate');
    Route::post("/search", 'search');
    Route::post("/delete_user", 'deleteUser');
    Route::post("/verify_blue_mark", 'verifyBlueMark');
    Route::get("/get_detail", 'userDetail');
    Route::get("/top_rated_seller", "getTopRatedSeller");
});

// app info api
Route::prefix('app_info')->controller(AppInfoApiController::class)->group(function () {
    Route::post("/", 'appInfo');
});

// phone_country_code api controller
Route::prefix('phone_country_code')->controller(PhoneCountryCodeApiController::class)->group(function () {
    Route::post("/search", 'search');
});
Route::apiResource('/phone_country_code', PhoneCountryCodeApiController::class, array("as" => "api"));

// mobile color api controller
Route::prefix('color')->controller(ColorApiController::class)->group(function () {
    Route::get("/", 'index');
});
