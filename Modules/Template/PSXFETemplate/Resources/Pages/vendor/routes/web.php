<?php

use Illuminate\Support\Facades\Route;
use Modules\Template\PSXFETemplate\Http\Controllers\MPCTemplate1Controller;
use Modules\Template\PSXFETemplate\Http\Controllers\AppContentController;
use Modules\Core\Entities\BackendSetting;
use Modules\Template\PSXFETemplate\Http\Controllers\BlogController;

Route::prefix('app_content')->controller(AppContentController::class)->group(function (){
    Route::get('/privacy', 'privacycontent')->name('privacycontent.index');
    Route::get('/datadelection', 'datadelectioncontent')->name('datadelectioncontent.index');
});

Route::prefix('')->controller(MPCTemplate1Controller::class)->group(function (){
    Route::get('/', 'feDashboard')->name('dashboard');
});
Route::resource('/app_content',AppContentController::class);

Route::middleware(["isFeSettingEnable"])->prefix('')->controller(MPCTemplate1Controller::class)->group(function (){

    Route::prefix('')->controller(BlogController::class)->group(function (){
        Route::get('/blog', 'feBlog')->name('fe_blog');
        Route::get('/blog-detail', 'feBlogDetail')->name('fe_blog_detail');
    });

    Route::get('/category', 'feCategory')->name('fe_category.index');
    Route::get('/subcategory', 'feSubCategory')->name('fe_sub_category');
    Route::get('/item-list', 'feItemList')->name('fe_item_list');
    Route::get('/item-entry', 'feItemEntry')->name('fe_item_entry')->middleware(['uploadSetting']);
    Route::get('/fe_item', 'feItemDetail')->name('fe_item_detail');
    Route::get('/profile', 'feProfile')->name('fe_profile');
    Route::get('/other-profile', 'feOtherProfile')->name('fe_other_profile');
    Route::get('/landing', 'feLanding')->name('fe_landing.index');
    // Route::get('/blog', 'feBlog')->name('fe_blog');
    // Route::get('/blog-detail', 'feBlogDetail')->name('fe_blog_detail');
    Route::get('/contact-us', 'feContactUs')->name('fe_contact_us');
    Route::get('/about-us', 'feAboutUs')->name('fe_about_us');
    Route::get('/term-and-conditions', 'feTermsAndConditions')->name('fe_terms_and_conditions');
    Route::get('/faq', 'feFAQ')->name('fe_faq');
    Route::get('/fe-privacy', 'fePrivacy')->name('fe_privacy');
    Route::get('/safety-tips', 'feSafetyTips')->name('fe_safety_tips');
    Route::get('/active-items', 'feActiveItems')->name('fe_active_items');
    Route::get('/pending-items', 'fePendingItems')->name('fe_pending_items');
    Route::get('/disable-items', 'feDisableItems')->name('fe_disable_items');
    Route::get('/favourite-items', 'feFavouriteItems')->name('fe_favourite_items');


    Route::get('/reject-items', 'feRejectItems')->name('fe_reject_items');
    Route::get('/soldout-items', 'feSoldoutItems')->name('fe_soldout_items');
    Route::get('/follower-items', 'feFollowerItems')->name('fe_follower_items');
    Route::get('/paid-items', 'fePaidItems')->name('fe_paid_items');
    Route::get('/chat-list', 'feChatList')->name('fe_chat_list');
    Route::get('/chat', 'feChat')->name('fe_chat');
    Route::get('/notification-list', 'feNotificationList')->name('fe_notification_list');
    Route::get('/profile-edit', 'feProfileEdit')->name('fe_profile_edit');
    Route::get('/notification-detail', 'feNotificationDetail')->name('fe_notification_detail');


    Route::get('/follower-list', 'feFollowerList')->name('fe_follower_list');
    Route::get('/following-list', 'feFollowingList')->name('fe_following_list');
    Route::get('/user-list', 'feUserList')->name('fe_user_list');
    Route::get('/top-rated-seller-list', 'feTopRatedSellerList')->name('fe_top_rated_seller_list');
    Route::get('/package-list', 'fePackageList')->name('fe_package_list');
    Route::get('/offer-list', 'feOfferList')->name('fe_offer_list');
    Route::get('/block-user-list', 'feBlockUserList')->name('fe_block_user_list');
    Route::get('/reported-items', 'feReportedItems')->name('fe_reported_items');
    Route::get('/limit-ads', 'feLimitAds')->name('fe_limit_ads');
    Route::get('/review-list', 'feReviewList')->name('fe_review_list');

    Route::get('/fe-ui-collection', 'feUiCollection')->name('fe_ui_collection');

    Route::middleware(["isVendorSettingOn"])->group(function () {
        Route::get('/vendor-list', 'feVendorList')->name('fe_vendor_list');
        Route::get('/vendor-info', 'feVendorInfo')->name('fe_vendor_info');
        Route::get('/vendor-filter', 'feVendorFilter')->name('fe_vendor_filter');
        Route::get('/vendor-checkout', 'feVendorCheckout')->name('fe_vendor_checkout');
        Route::get('/order-successful', 'feOrderSuccessful')->name('fe_order_successful');
    });
});




