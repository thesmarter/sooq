<?php

use Inertia\Inertia;
use App\Config\ps_constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use Modules\Core\Constants\Constants;
use Modules\Core\Entities\DomainChange;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\CustomLoginController;
use App\Http\Controllers\PhoneVerifyController;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Modules\Blog\Http\Controllers\Backend\Controllers\BlogController;
use Modules\Core\Http\Controllers\Backend\Controllers\Item\ItemController;
use Modules\Core\Http\Controllers\Backend\Controllers\Role\RoleController;
use Modules\Core\Http\Controllers\Backend\Controllers\Shop\ShopController;
use Modules\Core\Http\Controllers\Backend\Controllers\User\UserController;
use Modules\Package\Http\Controllers\Backend\Controllers\PackageController;
use Modules\Core\Http\Controllers\Backend\Controllers\About\AboutController;
use Modules\Core\Http\Controllers\Backend\Controllers\Color\ColorController;
use Modules\Core\Http\Controllers\Backend\Controllers\Image\ImageController;
use Modules\Core\Http\Controllers\Backend\Controllers\Item\RejectController;
use Modules\Core\Http\Controllers\Backend\Controllers\Table\TableController;
use Modules\Core\Http\Controllers\Backend\Controllers\Item\DisableController;
use Modules\Core\Http\Controllers\Backend\Controllers\Item\PendingController;
use Modules\Core\Http\Controllers\Backend\Controllers\Module\ModuleController;
use Modules\Core\Http\Controllers\Backend\Controllers\Vendor\VendorController;
use Modules\Core\Http\Controllers\Backend\Controllers\Item\ItemReportController;
use Modules\Core\Http\Controllers\Backend\Controllers\License\LicenseController;
use Modules\Core\Http\Controllers\Backend\Controllers\Product\ProductController;
use Modules\Core\Http\Controllers\Backend\Controllers\User\BannedUserController;
use Modules\Package\Http\Controllers\Backend\Controllers\PackageReportController;
use Modules\Core\Http\Controllers\Backend\Controllers\ApiToken\ApiTokenController;
use Modules\Core\Http\Controllers\Backend\Controllers\Category\CategoryController;
use Modules\Core\Http\Controllers\Backend\Controllers\CoreMenu\CoreMenuController;
use Modules\Core\Http\Controllers\Backend\Controllers\Currency\CurrencyController;
use Modules\Core\Http\Controllers\Backend\Controllers\Feedback\FeedbackController;
use Modules\Core\Http\Controllers\Backend\Controllers\Language\LanguageController;
use Modules\Core\Http\Controllers\Backend\Controllers\Shipping\ShippingController;
use Modules\ItemPromotion\Http\Controllers\Backend\Controllers\PaidItemController;
use Modules\Package\Http\Controllers\Backend\Controllers\OfflinePackageController;
use Modules\Payment\Http\Controllers\Backend\Controllers\Payment\PaymentController;
use Modules\BlueMarkUser\Http\Controllers\Backend\Controller\BlueMarkUserController;
use Modules\Core\Http\Controllers\Backend\Controllers\Dashboard\DashboardController;
use Modules\Core\Http\Controllers\Backend\Controllers\MenuGroup\MenuGroupController;
use Modules\Core\Http\Controllers\Backend\Controllers\Vendor\VendorRejectController;
use Modules\Core\Http\Controllers\Backend\Controllers\Vendor\VendorSettingController;
use Modules\Core\Http\Controllers\Backend\Controllers\FeLanguage\FeLanguageController;
use Modules\Core\Http\Controllers\Backend\Controllers\UserReport\UserReportController;
use Modules\Core\Http\Controllers\Backend\Controllers\Vendor\VendorApprovalController;
use Modules\Core\Http\Controllers\Backend\Controllers\VendorMenu\VendorMenuController;
use Modules\Core\Http\Controllers\Backend\Controllers\VendorRole\VendorRoleController;
use Modules\Core\Http\Controllers\Backend\Rests\App\V1_0\Product\ProductApiController;
use Modules\DeeplinkGenerator\Http\Controllers\Backend\Controllers\DeeplinkController;
use Modules\Core\Http\Controllers\Backend\Controllers\Category\CategoryReportController;
use Modules\Core\Http\Controllers\Backend\Controllers\CoreKeyType\CoreKeyTypeController;
use Modules\Core\Http\Controllers\Backend\Controllers\LandingPage\LandingPageController;
use Modules\Core\Http\Controllers\Backend\Controllers\Subcategory\SubcategoryController;
use Modules\Core\Http\Controllers\Backend\Controllers\Transaction\TransactionController;
use Modules\ItemPromotion\Http\Controllers\Backend\Controllers\OfflinePaidItemController;
use Modules\SlowMovingItem\Http\Controllers\Backend\Controllers\SlowMovingItemController;
use Modules\Core\Http\Controllers\Backend\Controllers\LocationCity\LocationCityController;
use Modules\Core\Http\Controllers\Backend\Controllers\SubMenuGroup\SubMenuGroupController;
use Modules\Core\Http\Controllers\Backend\Controllers\SystemConfig\SystemConfigController;
use Modules\Core\Http\Controllers\Backend\Controllers\VendorModule\VendorModuleController;
use Modules\Payment\Http\Controllers\Backend\Controllers\Payment\PaymentCoreKeyController;
use Modules\Core\Http\Controllers\Backend\Controllers\MobileSetting\MobileSettingController;
use Modules\Core\Http\Controllers\Backend\Controllers\PaymentStatus\PaymentStatusController;
use Modules\Core\Http\Controllers\Backend\Controllers\PrivacyPolicy\PrivacyPolicyController;
use Modules\Package\Http\Controllers\Backend\Controllers\VendorSubscriptionReportController;
use Modules\ContactUsMessage\Http\Controllers\Backend\Controllers\ContactUsMessageController;
use Modules\DemoDataDeletion\Http\Controllers\Backend\Controllers\DemoDataDeletionController;
use Modules\Core\Http\Controllers\Backend\Controllers\BackendSetting\BackendSettingController;
use Modules\Core\Http\Controllers\Backend\Controllers\BuilderSetting\BuilderSettingController;
use Modules\Core\Http\Controllers\Backend\Controllers\LanguageString\LanguageStringController;
use Modules\Core\Http\Controllers\Backend\Controllers\MobileLanguage\MobileLanguageController;
use Modules\Core\Http\Controllers\Backend\Controllers\VendorLanguage\VendorLanguageController;
use Modules\SlowMovingItem\Http\Controllers\Backend\Controllers\SlowMovingItemReportController;
use Modules\Core\Http\Controllers\Backend\Controllers\FrontendSetting\FrontendSettingController;
use Modules\Core\Http\Controllers\Backend\Controllers\VendorMenuGroup\VendorMenuGroupController;
use Modules\DataDeletionPolicy\Http\Controllers\Backend\Controllers\DataDeletionPolicyController;
use Modules\Payment\Http\Controllers\Backend\Controllers\PaymentSetting\PaymentSettingController;
use Modules\Core\Http\Controllers\Backend\Controllers\FeLanguageString\FeLanguageStringController;
use Modules\Core\Http\Controllers\Backend\Controllers\LocationTownship\LocationTownshipController;
use Modules\Core\Http\Controllers\Backend\Controllers\PhoneCountryCode\PhoneCountryCodeController;
use Modules\Core\Http\Controllers\Backend\Controllers\AvailableCurrency\AvailableCurrencyController;
use Modules\Core\Http\Controllers\Backend\Controllers\CustomizeUiDetail\CustomizeUiDetailController;


use Modules\Core\Http\Controllers\Backend\Controllers\TransactionStatus\TransactionStatusController;
use Modules\ComplaintItem\Http\Controllers\Backend\Controllers\ComplaintItem\ComplaintItemController;
use Modules\Core\Http\Controllers\Backend\Controllers\CoreAndCustomField\CoreAndCustomFieldController;
use Modules\Core\Http\Controllers\Backend\Controllers\VendorSubMenuGroup\VendorSubMenuGroupController;
use Modules\Core\Http\Controllers\Backend\Controllers\MobileLanguageString\MobileLanguageStringController;
use Modules\Core\Http\Controllers\Backend\Controllers\VendorLanguageString\VendorLanguageStringController;
use Modules\ImageGenerationForThumbnail\Http\Controllers\Backend\Controllers\ThumbnailGeneratorController;
use Modules\PushNotificationMessage\Http\Controllers\Backend\Controllers\PushNotificationMessageController;
use Modules\Payment\Http\Controllers\Backend\Controllers\OfflinePaymentSetting\OfflinePaymentSettingController;
use Modules\Payment\Http\Controllers\Backend\Controllers\PackageInAppPurchaseSetting\PackageInAppPurchaseSettingController;
use Modules\Payment\Http\Controllers\Backend\Controllers\PromotionInAppPurchaseSetting\PromotionInAppPurchaseSettingController;
use Modules\Payment\Http\Controllers\Backend\Controllers\VendorSubscriptionPlanSetting\VendorSubscriptionPlanSettingController;

Route::middleware(['auth:sanctum'])->get('/admin', function () {
    if (Auth::check()) {
        if (Auth::user()->role_id != Constants::normalUserRoleId) {
            return redirect()->route('admin.index');
        } else {
            return back();
        }
    }
})->name('admin.index');

//not use route start
Route::get("admin/ui-collections", function () {
    return Inertia::render('Admin/UiCollections');
})->name('admin.ui.collections');

Route::get("admin/ui-components", function () {
    return Inertia::render('Admin/UiComponents');
})->name('admin.ui.components');


Route::prefix('core')->middleware(['auth:sanctum'])->group(function () {
    Route::resource('/core-category', SubcategoryController::class);

    Route::controller(ProductController::class)->group(function () {

        Route::get('/product/core-field-filter-setting', "filterForCoreField")->name("product.coreFieldFilterSetting");
        Route::post('/product/core-field-filter-setting', "filterForCoreFieldStore")->name("product.coreFieldFilterSetting.store");

        Route::post('/product/screen-display-ui-setting', "screenDisplayUiStore")->name("product.screenDisplayUiSetting.store");

        Route::get('/product/setting', 'customization')->name("product.customization");
        Route::delete('/product/setting/{customizeHeader}', 'customizationDestroy')->name("product.customization.delete");

        Route::get('/product/setting/customizationDetailIndex/{coreKeysId}', 'customizationDetailIndex')->name("product.customizationDetail.index");
        Route::get('/product/setting/customizationDetail/{coreKeysId}/edit', 'customizationDetailCreate')->name("product.customizationDetail.create");
        Route::post('/product/setting/customizationDetail', 'customizationDetailStore')->name("product.customizationDetail.store");
        Route::delete('/product/setting/customizationDetail/{id}', 'customizationDetailDestroy')->name("product.customizationDetail.destroy");
        Route::get('/product/setting/customizationDetailEdit/{id}', 'customizationDetailEdit')->name("product.customizationDetail.edit");
        Route::put('/product/setting/customizationDetailUpdate/{id}', 'customizationDetailUpdate')->name("product.customizationDetail.update");

        Route::prefix("/product/setting/")->name("product.")->group(function () {
            Route::get('addNewField', 'addNewField')->name("addNewField");
            Route::post('addNewField', 'addNewFieldStore')->name("addNewField.store");
            Route::get('addNewField/{id}/Edit', "addNewFieldEdit")->name("addNewField.edit");
            Route::put('addNewField/{id}', "addNewFieldUpdate")->name("addNewField.update");
            Route::put('addNewField/mandatory/{id}', 'addNewFieldMandatory')->name('addNewField.mandatory');

            Route::get('delete-temporary-fields', 'deletedTemFields')->name("deletedTemFields");
            Route::put('undelete/{customizeHeader}', 'undeletedForCustomization')->name("customization.undelete");


            Route::put('enableOrDisable/{id}', 'enableOrDisableField')->name('addNewField.enableOrDisable');
        });

        Route::get('/product/export/configs', 'exportConfigs')->name('product.export.configs');
        Route::post('/product/import/configs', 'importConfigs')->name('product.import.configs');
    });

    Route::resource('/product', ProductController::class);
});

// Route::resource('/core_key_type',CoreKeyTypeController::class);
//not use route end

Route::get('admin/mail_test', function () {
    return view('email/testing');
})->name('mail.testing');

Route::get('/send-emailtest', [EmailController::class, 'index']);

Route::post('admin/send_mail_test', function (Request $request) {
    $to = $request->email;
    $subject = __('mail__testing_subject');
    $to_name = "User";
    $body = __('mail__testing_body');
    $title = __('mail__testing_title');
    $headers = "From: no_reply@itempass.com" . "\r\n";
    if (mail($to, $subject, $body, $headers)) {

        $status = "Success";

        echo $status;
    } else {

        $status = "Fail";

        echo $status;
    }
    // if(sendMail($to, $to_name, $title, $subject, $body)){
    //     return back()->with('success', __('mail__testing_success_mail'));
    // }
    // return back()->with('error', __('mail__testing_fail_mail'));
})->name('send.mail.testing');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);
Route::post('/CreateLogin', [CustomLoginController::class, 'CreateLogin'])->name('CreateLogin');
Route::get('/phoneLogin', [CustomLoginController::class, 'phoneLogin'])->name('Phonelogin');
Route::get('/verifyCode/{email}', [CustomLoginController::class, 'verifyForgotPasswordCode'])->name('verifyCode');
Route::get('/resetPassword/{id}/{code}', [CustomLoginController::class, 'resetPassword'])->name('resetPassword');
Route::post('/verifyEmail', [CustomLoginController::class, 'userVerifyEmail'])->name('verifyEmail');

// Route::get('/getToken', [CustomLoginController::class, 'getToken'])->name('getToken');
// Route::post('/CreateUser', [CustomLoginController::class, 'createUser'])->name('CreateUser');
// Route::get('/existuser', [CustomLoginController::class, 'existUser'])->name('existUser');
// ->name('logout');

Route::get('/verify-phone', [PhoneVerifyController::class, 'VerifyPhone'])->name('verifyPhone');
Route::post('/update-verify', [PhoneVerifyController::class, 'updateVerify'])->name('updateVerify');


Route::middleware(['isInstall'])->group(function () {
    require_once __DIR__ . "/fortify.php";
    // $enableViews = config('fortify.views', true);

    // if ($enableViews) {
    //     Route::get('/login',function(){
    //         return redirect()->route('login');
    //     });

    // }

    // $limiter = config('fortify.limiters.login');
    // $twoFactorLimiter = config('fortify.limiters.two-factor');
    // $verificationLimiter = config('fortify.limiters.verification', '6,1');

    // Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    //     ->middleware(array_filter([
    //         'guest:'.config('fortify.guard'),
    //         $limiter ? 'throttle:'.$limiter : null,
    //     ]));


    // Route::middleware(["auth"])->prefix('app_content')->controller(AppContentController::class)->group(function (){
    //     Route::get('/privacy', 'privacycontent')->name('privacycontent.index');
    //     Route::get('/datadelection', 'datadelectioncontent')->name('datadelectioncontent.index');
    // });
    // Route::resource('/app_content',AppContentController::class);

    // Route::prefix('')->controller(MPCTemplate1Controller::class)->group(function (){
    //     Route::get('/', 'index')->name('showlanding.index');
    // });
    // Route::resource('/',MPCTemplate1Controller::class);
    require base_path('Modules/Template/PSXFETemplate/Resources/Pages/vendor/routes/web.php');
    require base_path('Modules/StoreFront/VendorPanel/Resources/Pages/vendor/routes/web.php');

    Route::middleware(["isVendorSettingOn"])->group(function () {
        Route::prefix('vendor')->controller(VendorController::class)->group(function () {
            Route::get('/setSession', 'setSession')->name('vendor.setSession');
            Route::put('/changeVendor/{vendor_id}', 'changeVendor')->name('vendor.changeVendor');
        });
    });
    // if ($enableViews) {
    //     Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    //         ->middleware(['guest:'.config('fortify.guard')])
    //         ->name('login');
    // }


    // Authentication...
    // if ($enableViews) {
    //     Route::get('admin/login', [AuthenticatedSessionController::class, 'create'])
    //         ->middleware(['guest:'.config('fortify.guard')])
    //         ->name('login');
    // }

    // $limiter = config('fortify.limiters.login');
    // $twoFactorLimiter = config('fortify.limiters.two-factor');
    // $verificationLimiter = config('fortify.limiters.verification', '6,1');

    // Route::post('admin/login', [AuthenticatedSessionController::class, 'store'])
    //     ->middleware(array_filter([
    //         'guest:'.config('fortify.guard'),
    //         $limiter ? 'throttle:'.$limiter : null,
    //     ]));

    // Start Admin Route
    Route::middleware(["auth", "checkUpdater", "checkDashboardPermission"])->prefix('admin')->group(function () {

        Route::controller(DashboardController::class)->group(function () {
            Route::get('/', 'index')->name('admin.index');
            Route::get('/refresh', 'refresh')->name('admin.refresh');
            Route::get('/search', 'search')->name('admin.dashboard.search');
            Route::post('/update-code-and-url', 'updateLicense')->name('admin.dashboard.updateLicense');
            Route::post('/activate-license', 'activateLicense')->name('admin.dashboard.activateLicense');
            Route::post('/setup-auto-sync', 'setupAutoSync')->name('admin.dashboard.setupAutoSync');
            Route::post('/check-connection', 'checkConnection')->name('admin.dashboard.checkConnection');
            Route::post('/activate-with-builder-connection', 'activateLicenseWithBuilderConnection')->name('admin.dashboard.activateLicenseWithBuilderConnection');
            Route::post('/download-and-import', 'downloadAndImportProject')->name('admin.dashboard.downloadAndImport');
        });

        Route::resource('/landing_page', LandingPageController::class);

        Route::middleware(["system"])->group(function () {

            // For Currency
            Route::resource('/currency', CurrencyController::class);
            Route::controller(CurrencyController::class)->group(function () {
                Route::put('/currency/status/{currency}', 'statusChange')->name('currency.statusChange');
                Route::put('/currency/default/{currency}', 'defaultChange')->name('currency.defaultChange');
                Route::put('/screen-display-ui-setting', "screenDisplayUiStore")->name("currency.screenDisplayUiSetting.store");
            });

            // For Location City
            Route::prefix('city')->controller(LocationCityController::class)->group(function () {
                Route::put('/status/{city}', 'statusChange')->name('city.statusChange');
                Route::put('/import/csv', 'importCSV')->name('city.import.csv');
                Route::put('/screen-display-ui-setting', "screenDisplayUiStore")->name("city.screenDisplayUiSetting.store");

                // For Location City Setting
                Route::prefix('setting')->group(function () {

                    // For Location City Custom Field
                    Route::prefix('custom_field')->group(function () {
                        // For custom field header
                        Route::get('/', 'customization')->name("city.customization");
                        Route::get('/create', 'addNewField')->name("city.addNewField");
                        Route::post('/store', 'addNewFieldStore')->name("city.addNewField.store");
                        Route::delete('/{custom_field_header}', 'customizationDestroy')->name("city.customization.delete");
                        Route::put('/optionOrMandatory/{custom_field_header}', 'optionalOrMandatoryChange')->name('city.addNewField.optionalOrMandatory');
                        Route::put('/enableOrDisable/{custom_field_header}', 'enableOrDisableChange')->name('city.addNewField.enableOrDisable');

                        // For custom field detail
                        Route::prefix('{custom_field_header}/detail')->group(function () {
                            Route::get('/', 'customizationDetailIndex')->name("city.customizationDetail.index");
                            Route::get('/create', 'customizationDetailCreate')->name("city.customizationDetail.create");
                            Route::post('/', 'customizationDetailStore')->name("city.customizationDetail.store");
                            Route::get('/{custom_field_detail}/edit', 'customizationDetailEdit')->name("city.customizationDetail.edit");
                            Route::put('/{custom_field_detail}', 'customizationDetailUpdate')->name("city.customizationDetail.update");
                            Route::delete('/{custom_field_detail}', 'customizationDetailDestroy')->name("city.customizationDetail.destroy");
                        });
                    });
                });
            });
            Route::resource('/city', LocationCityController::class);

            // For Location Township
            Route::prefix('township')->controller(LocationTownshipController::class)->group(function () {
                Route::put('/status/{township}', 'statusChange')->name('township.statusChange');
                Route::put('/import/csv', 'importCSV')->name('township.import.csv');
                Route::put('/screen-display-ui-setting', "screenDisplayUiStore")->name("township.screenDisplayUiSetting.store");
            });
            Route::resource('/township', LocationTownshipController::class);

            // For Core Menu Group
            Route::resource('/menu_group', MenuGroupController::class);
            Route::prefix('menu_group')->controller(MenuGroupController::class)->group(function () {
                Route::put('/status/{menu_group}', 'statusChange')->name('menu_group.statusChange');
                Route::post('/screen-display-ui-setting', "screenDisplayUiStore")->name("menu_group.screenDisplayUiSetting.store");
            });

            // For Core Sub Menu Group
            Route::resource('/sub_menu_group', SubMenuGroupController::class);
            Route::prefix('sub_menu_group')->controller(SubMenuGroupController::class)->group(function () {
                Route::put('/status/{sub_menu_group}', 'statusChange')->name('sub_menu_group.statusChange');
                Route::post('/screen-display-ui-setting', "screenDisplayUiStore")->name("sub_menu_group.screenDisplayUiSetting.store");
            });

            // For Module
            Route::resource('/module', ModuleController::class);
            Route::prefix('module')->controller(ModuleController::class)->group(function () {
                Route::put('/status/{module}', 'statusChange')->name('module.statusChange');
                Route::post('/screen-display-ui-setting', "screenDisplayUiStore")->name("module.screenDisplayUiSetting.store");
            });

            // For Core Menu
            Route::resource('/menu', CoreMenuController::class);
            Route::prefix('menu')->controller(CoreMenuController::class)->group(function () {
                Route::put('/status/{module}', 'statusChange')->name('menu.statusChange');
                Route::post('/screen-display-ui-setting', "screenDisplayUiStore")->name("menu.screenDisplayUiSetting.store");
                // Route::post('/default_create_token','defaultTokenCreating')->name('api_token.default_creating_token');
            });


            Route::middleware(["isVendorSettingOn"])->group(function () {

                Route::resource('/vendor', VendorController::class);

                // For Vendor Menu Group
                Route::resource('/vendor_menu_group', VendorMenuGroupController::class);
                Route::prefix('vendor_menu_group')->controller(VendorMenuGroupController::class)->group(function () {
                    Route::put('/status/{vendor_menu_group}', 'statusChange')->name('vendor_menu_group.statusChange');
                    Route::post('/screen-display-ui-setting', "screenDisplayUiStore")->name("vendor_menu_group.screenDisplayUiSetting.store");
                });


                // For Vendor Sub Menu Group
                Route::resource('/vendor_sub_menu_group', VendorSubMenuGroupController::class);
                Route::prefix('vendor_sub_menu_group')->controller(VendorSubMenuGroupController::class)->group(function () {
                    Route::put('/status/{vendor_sub_menu_group}', 'statusChange')->name('vendor_sub_menu_group.statusChange');
                    Route::post('/screen-display-ui-setting', "screenDisplayUiStore")->name("vendor_sub_menu_group.screenDisplayUiSetting.store");
                });

                // For Vendor Module
                Route::resource('/vendor_module_registering', VendorModuleController::class);
                Route::prefix('vendor_module_registering')->controller(VendorModuleController::class)->group(function () {
                    Route::put('/status/{vendor_module_registering}', 'statusChange')->name('vendor_module_registering.statusChange');
                    Route::post('/screen-display-ui-setting', "screenDisplayUiStore")->name("vendor_module_registering.screenDisplayUiSetting.store");
                });


                // For Vendor Menu
                Route::resource('/vendor_menu', VendorMenuController::class);
                Route::prefix('vendor_menu')->controller(VendorMenuController::class)->group(function () {
                    Route::put('/status/{module}', 'statusChange')->name('vendor_menu.statusChange');
                    Route::post('/screen-display-ui-setting', "screenDisplayUiStore")->name("vendor_menu.screenDisplayUiSetting.store");
                    // Route::post('/default_create_token','defaultTokenCreating')->name('api_token.default_creating_token');
                });

            });

            // For Privacy Policy
            Route::prefix('privacy_policy')->controller(PrivacyPolicyController::class)->group(function () {
                Route::get('/', 'index')->name('privacy_policy.index');
                Route::post('/', 'store')->name('privacy_policy.store');
                Route::put('/', 'update')->name('privacy_policy.update');
                Route::post('/ckupload', 'ckUpload')->name('privacy_policy.ckUpload');
            });


            // For Language
            Route::prefix('language')->controller(LanguageController::class)->group(function () {
                Route::put('/status/{language}', 'statusChange')->name('language.statusChange');
                Route::post('/table', 'languageTable')->name('language.languageTable');
                Route::post('/generate', 'generateall')->name('language.generateall');
                Route::put('/changeLanguage/{langSymbol}', 'changeLanguage')->name('language.changeLanguage');
                Route::get('/getLangs', 'getLanguages')->name('language.getLanguages');
                Route::post('/screen-display-ui-setting', "screenDisplayUiStore")->name("language.screenDisplayUiSetting.store");
            });
            Route::resource('/language', LanguageController::class);

            // For language string

            Route::prefix('language/{language}/language_string')->controller(LanguageStringController::class)->group(function () {
                Route::put('/import/csv', 'importCSV')->name('language_string.import.csv');
                Route::get('/export/json', 'exportJson')->name('language_string.export.json');
                Route::get('/export/csv', 'exportCSV')->name('language_string.export.csv');
            });
            Route::prefix('language_string')->controller(LanguageStringController::class)->group(function () {
                Route::post('/getLangString', 'getLanguageString')->name('language_string.getLanguageString');
                Route::post('/screen-display-ui-setting', "screenDisplayUiStore")->name("language_string.screenDisplayUiSetting.store");
                Route::post('/updateLang', 'updateLanguageStrings')->name('language_string.updateLanguageStrings');
                Route::get('/updateAllLang', 'updateAllLanguageStrings')->name('language_string.updateAllLanguageStrings');
                Route::post('/importAllLang', 'importAllLanguageStrings')->name('language_string.import_all_langs');
            });
            Route::resource('language/{language}/language_string', LanguageStringController::class);

            // For Frontend Language
            Route::prefix('fe_language')->controller(FeLanguageController::class)->group(function () {
                Route::put('/status/{fe_language}', 'statusChange')->name('fe_language.statusChange');
                Route::post('/table', 'languageTable')->name('fe_language.languageTable');
                Route::post('/generate', 'generateall')->name('fe_language.generateall');
                Route::get('/getLangs', 'getLanguages')->name('fe_language.getLanguages');
                Route::post('/screen-display-ui-setting', "screenDisplayUiStore")->name("fe_language.screenDisplayUiSetting.store");
            });
            Route::resource('/fe_language', FeLanguageController::class);

            // For Frontend Language String
            Route::prefix('fe_language/{fe_language}/fe_language_string')->controller(FeLanguageStringController::class)->group(function () {
                Route::put('/import/csv', 'importCSV')->name('fe_language_string.import.csv');
                Route::get('/export/json', 'exportJson')->name('fe_language_string.export.json');
                Route::get('/export/csv', 'exportCSV')->name('fe_language_string.export.csv');
            });
            Route::prefix('fe_language_string')->controller(FeLanguageStringController::class)->group(function () {
                Route::post('/getLangString', 'getLanguageString')->name('fe_language_string.getLanguageString');
                Route::post('/screen-display-ui-setting', "screenDisplayUiStore")->name("language_string.screenDisplayUiSetting.store");
                Route::post('/updateLang', 'updateLanguageStrings')->name('fe_language_string.updateLanguageStrings');
                Route::get('/updateAllLang', 'updateAllLanguageStrings')->name('fe_language_string.updateAllLanguageStrings');
                Route::post('/importAllLang', 'importAllLanguageStrings')->name('fe_language_string.import_all_langs');
            });
            Route::resource('fe_language/{fe_language}/fe_language_string', FeLanguageStringController::class);

            Route::middleware(["isVendorSettingOn"])->group(function () {

                // For Item Approval
                Route::prefix('vendor/approval')->group(function () {
                    // For Pending Items
                    Route::controller(VendorApprovalController::class)->group(function () {
                        Route::get('/pending', 'index')->name('pending_vendor.index');
                        Route::get('/{vendor}', 'show')->name('pending_vendor.show');
                        Route::delete('/pending/{vendor}', 'destroy')->name('pending_vendor.destroy');
                        Route::put('/pending/{vendor}', 'statusChange')->name('pending_vendor.statusChange');
                        Route::get('/pending/download_document/{vendor}', 'downloadDocument')->name('pending_vendor.download_document');
                    });
                });
                // Route::resource('/vendor_approval', VendorApprovalController::class);

                // For Item Approval
                Route::prefix('vendor-approval')->group(function () {

                    // For Pending Vendor
                    Route::controller(VendorRejectController::class)->group(function () {
                        Route::get('/reject', 'index')->name('reject_vendor.index');
                        Route::get('/{vendor}', 'show')->name('reject_vendor.show');
                        Route::delete('/reject/{vendor}', 'destroy')->name('reject_vendor.destroy');
                        Route::put('/reject/{vendor}', 'statusChange')->name('reject_vendor.statusChange');
                        Route::get('/reject/download_document/{vendor}', 'downloadDocument')->name('reject_vendor.download_document');
                    });
                });

                // For Vendor Report
                Route::prefix('report')->controller(VendorSubscriptionReportController::class)->group(function () {
                    Route::get('/vendor_subscription_report', 'index')->name('vendor_subscription_report.index');
                    Route::get('/vendor_subscription_report/{vendor}', 'show')->name('vendor_subscription_report.show');
                    Route::get('/vendor_subscription_report/csv/export', 'csvExport')->name('vendor_subscription_report.csv.export');
                });

            });

            Route::prefix('vendor_setting')->controller(VendorSettingController::class)->group(function () {
                Route::get('/languageRefresh', 'languageRefresh')->name('vendor_setting.languageRefresh');
            });
            Route::resource('/vendor_setting', VendorSettingController::class);


            //for vendor language
            Route::prefix('vendor_language')->controller(VendorLanguageController::class)->group(function () {
                Route::put('/status/{vendor_language}', 'statusChange')->name('vendor_language.statusChange');
                Route::post('/table', 'languageTable')->name('vendor_language.languageTable');
                Route::post('/generate', 'generateall')->name('vendor_language.generateall');
                Route::get('/getLangs', 'getLanguages')->name('vendor_language.getLanguages');
                Route::post('/screen-display-ui-setting', "screenDisplayUiStore")->name("vendor_language.screenDisplayUiSetting.store");
            });
            Route::resource('/vendor_language', VendorLanguageController::class);

            // For Vendor Language String
            Route::prefix('vendor_language/{vendor_language}/vendor_language_string')->controller(VendorLanguageStringController::class)->group(function () {
                Route::put('/import/csv', 'importCSV')->name('vendor_language_string.import.csv');
                Route::get('/export/json', 'exportJson')->name('vendor_language_string.export.json');
                Route::get('/export/csv', 'exportCSV')->name('vendor_language_string.export.csv');
            });
            Route::prefix('vendor_language_string')->controller(VendorLanguageStringController::class)->group(function () {
                Route::post('/getLangString', 'getLanguageString')->name('vendor_language_string.getLanguageString');
                Route::post('/screen-display-ui-setting', "screenDisplayUiStore")->name("language_string.screenDisplayUiSetting.store");
                Route::post('/updateLang', 'updateLanguageStrings')->name('vendor_language_string.updateLanguageStrings');
                Route::get('/updateAllLang', 'updateAllLanguageStrings')->name('vendor_language_string.updateAllLanguageStrings');
                Route::post('/importAllLang', 'importAllLanguageStrings')->name('vendor_language_string.import_all_langs');
            });
            Route::resource('vendor_language/{vendor_language}/vendor_language_string', VendorLanguageStringController::class);


            // For Mobile Language
            Route::prefix('mobile_language')->controller(MobileLanguageController::class)->group(function () {
                Route::put('/status/{mobile_language}', 'statusChange')->name('mobile_language.statusChange');
                Route::put('/enable_disable/{mobile_language}', 'enableDisable')->name('mobile_language.enableDisable');
                Route::put('/screen-display-ui-setting', "screenDisplayUiStore")->name("mobile_language.screenDisplayUiSetting.store");
            });
            Route::resource('/mobile_language', MobileLanguageController::class);

            // For mobile language string
            Route::prefix('mobile_language/{mobile_language}/mobile_language_string')->controller(MobileLanguageStringController::class)->group(function () {
                Route::put('/status', 'statusChange')->name('mobile_language_string.statusChange');
                Route::put('/import/csv', 'importCSV')->name('mobile_language_string.import.csv');
                Route::get('/export/json', 'exportJson')->name('mobile_language_string.export.json');
                Route::get('/export/csv', 'exportCSV')->name('mobile_language_string.export.csv');
            });
            Route::prefix('mobile_language_string')->controller(MobileLanguageStringController::class)->group(function () {
                Route::put('/screen-display-ui-setting', "screenDisplayUiStore")->name("mobile_language_string.screenDisplayUiSetting.store");
            });
            Route::resource('mobile_language/{mobile_language}/mobile_language_string', MobileLanguageStringController::class);

            // For Shop
            Route::resource('/shop', ShopController::class);
            Route::prefix('shop')->controller(ShopController::class)->group(function () {
                Route::put('/status/{shop}', 'statusChange')->name('shop.statusChange');

                // For Shop Setting
                Route::prefix('setting')->group(function () {

                    // For Shop Custom Field
                    Route::prefix('custom_field')->group(function () {
                        // For custom field header
                        Route::get('/', 'customization')->name("shop.customization");
                        Route::get('/create', 'addNewField')->name("shop.addNewField");
                        Route::post('/store', 'addNewFieldStore')->name("shop.addNewField.store");
                        Route::delete('/{custom_field_header}', 'customizationDestroy')->name("shop.customization.delete");
                        Route::put('/optionOrMandatory/{custom_field_header}', 'optionalOrMandatoryChange')->name('shop.addNewField.optionalOrMandatory');
                        Route::put('/enableOrDisable/{custom_field_header}', 'enableOrDisableChange')->name('shop.addNewField.enableOrDisable');

                        // For custom field detail
                        Route::prefix('{custom_field_header}/detail')->group(function () {
                            Route::get('/', 'customizationDetailIndex')->name("shop.customizationDetail.index");
                            Route::get('/create', 'customizationDetailCreate')->name("shop.customizationDetail.create");
                            Route::post('/', 'customizationDetailStore')->name("shop.customizationDetail.store");
                            Route::get('/{custom_field_detail}/edit', 'customizationDetailEdit')->name("shop.customizationDetail.edit");
                            Route::put('/{custom_field_detail}', 'customizationDetailUpdate')->name("shop.customizationDetail.update");
                            Route::delete('/{custom_field_detail}', 'customizationDetailDestroy')->name("shop.customizationDetail.destroy");
                        });
                    });
                });
            });

            // For Transaction
            Route::prefix('transaction')->controller(TransactionController::class)->group(function () {
                Route::get('/', 'index')->name('transaction.index');
                Route::get('/{transaction}/edit', 'edit')->name('transaction.edit');
                Route::put('/{transaction}', 'update')->name('transaction.update');
                Route::delete('/{transaction}', 'destroy')->name('transaction.destroy');
                Route::get('/csv/export', 'csvExport')->name('transaction.csv.export');
            });

            // For PaymentStatus
            Route::resource('/payment_status', PaymentStatusController::class);

            // For TransactionStatus
            Route::resource('/transaction_status', TransactionStatusController::class);

            // For Shipping
            Route::resource('/shipping', ShippingController::class);
            Route::prefix('shipping')->controller(ShippingController::class)->group(function () {
                Route::put('/status/{shipping}', 'statusChange')->name('shipping.statusChange');
            });

            // For Privacy Policy
            Route::prefix('privacy_policy')->controller(PrivacyPolicyController::class)->group(function () {
                Route::get('/', 'index')->name('privacy_policy.index');
                Route::post('/', 'store')->name('privacy_policy.store');
                Route::put('/', 'update')->name('privacy_policy.update');
            });

            // For About
            // Route::prefix('about')->controller(AboutController::class)->group(function (){
            //     Route::get('/', 'index')->name('about.index');
            //     Route::post('/', 'store')->name('about.store');
            //     Route::put('/{about}', 'update')->name('about.update');
            // });
            Route::resource('/about', AboutController::class);

            // For Category

            Route::prefix('category')->group(function () {
                Route::controller(CategoryController::class)->group(function () {
                    Route::put('/status/{category}', 'statusChange')->name('category.statusChange');
                    Route::put('/import/csv', 'importCSV')->name('category.import.csv');
                    Route::put('/screen-display-ui-setting', "screenDisplayUiStore")->name("category.screenDisplayUiSetting.store");
                });

                // For Category Report
                Route::prefix('report')->controller(CategoryReportController::class)->group(function () {
                    Route::get('/category_report', 'categoryReportIndex')->name('category_report.index');
                    Route::get('/category_report/{category}', 'categoryReportShow')->name('category_report.show');
                    Route::get('/category_report/csv/export', 'categoryReportCsvExport')->name('category_report.csv.export');
                    Route::put('/screen-display-ui-setting', "screenDisplayUiStore")->name("category_report.screenDisplayUiSetting.store");
                });
            });
            Route::resource('/category', CategoryController::class);

            // For Subategory
            Route::prefix('subcategory')->controller(SubcategoryController::class)->group(function () {
                Route::put('/status/{subcategory}', 'statusChange')->name('subcategory.statusChange');
                Route::put('/import/csv', 'importCSV')->name('subcategory.import.csv');
                Route::put('/screen-display-ui-setting', "screenDisplayUiStore")->name("subcategory.screenDisplayUiSetting.store");
            });
            Route::resource('/subcategory', SubcategoryController::class);

            // For Image
            Route::prefix('image')->controller(ImageController::class)->group(function () {
                Route::delete('/{image}', 'destroy')->name('image.destroy');
                Route::put('/{image}', 'update')->name('image.replace');
                Route::put('/video/{video}', 'updateVideo')->name('video.replace');
            });


            // For User Feedback
            Route::prefix('feedback')->controller(FeedbackController::class)->group(function () {
                Route::get('/favourite', 'favouriteIndex')->name('favourite.index');
            });


            // For Item
            Route::prefix('item')->group(function () {
                Route::controller(ItemController::class)->group(function () {
                    Route::put('/duplicate/{item}', 'duplicateRow')->name('item.duplicate');
                    Route::put('/deeplink/{item}', 'deeplink')->name('item.deeplink');
                    Route::put('/', 'search')->name('item.search');
                    Route::put('/screen-display-ui-setting', "screenDisplayUiStore")->name("item.screenDisplayUiSetting.store");
                    Route::post('/custom-field/image-replace/{id}', "customFieldImageReplace")->name('customField.imageReplace');
                    Route::put('/status/{item}', 'statusChange')->name('item.statusChange');
                    Route::post('/upload-multi', 'uploadMulti');
                    // Route::post('/remove-multi', 'removeMulti')->name('item.removeMulti');

                    // For Item Setting
                    Route::prefix('setting')->group(function () {

                        // For Item Custom Field
                        Route::prefix('custom_field')->group(function () {
                            // For custom field header
                            Route::get('/', 'customization')->name("item.customization");
                            Route::get('/create', 'addNewField')->name("item.addNewField");
                            Route::post('/store', 'addNewFieldStore')->name("item.addNewField.store");
                            Route::delete('/{custom_field_header}', 'customizationDestroy')->name("item.customization.delete");
                            Route::put('/optionOrMandatory/{custom_field_header}', 'optionalOrMandatoryChange')->name('item.addNewField.optionalOrMandatory');
                            Route::put('/enableOrDisable/{custom_field_header}', 'enableOrDisableChange')->name('item.addNewField.enableOrDisable');

                            // For custom field detail
                            Route::prefix('{custom_field_header}/detail')->group(function () {
                                Route::get('/', 'customizationDetailIndex')->name("item.customizationDetail.index");
                                Route::get('/create', 'customizationDetailCreate')->name("item.customizationDetail.create");
                                Route::post('/', 'customizationDetailStore')->name("item.customizationDetail.store");
                                Route::get('/{custom_field_detail}/edit', 'customizationDetailEdit')->name("item.customizationDetail.edit");
                                Route::put('/{custom_field_detail}', 'customizationDetailUpdate')->name("item.customizationDetail.update");
                                Route::delete('/{custom_field_detail}', 'customizationDetailDestroy')->name("item.customizationDetail.destroy");
                            });
                        });

                        // Config file export / import
                        Route::prefix('configs')->group(function () {
                            Route::get('/export', 'exportConfigs')->name('item.configs.export');
                            Route::post('/import', 'importConfigs')->name('item.configs.import');
                        });
                    });
                });

                // For Item Report
                Route::prefix('report')->controller(ItemReportController::class)->group(function () {
                    Route::get('/item_report', 'itemReportIndex')->name('item_report.index');
                    Route::get('/item_report/{item}', 'itemReportShow')->name('item_report.show');
                    Route::get('/item_report/csv/export', 'itemReportCsvExport')->name('item_report.csv.export');

                    Route::get('/successful_deal_count_report', 'successfulDealCountReportIndex')->name('successful_deal_count_report.index');
                    Route::get('/successful_deal_count_report/{item}', 'successfulDealCountReportShow')->name('successful_deal_count_report.show');
                    Route::get('/successful_deal_count_report/csv/export', 'successfulDealCountReportCsvExport')->name('successful_deal_count_report.csv.export');

                    Route::get('/sold_out_item_report', 'soldOutItemReportIndex')->name('sold_out_item_report.index');
                    Route::get('/sold_out_item_report/{item}', 'soldOutItemReportShow')->name('sold_out_item_report.show');
                    Route::get('/sold_out_item_report/csv/export', 'soldOutItemReportCsvExport')->name('sold_out_item_report.csv.export');
                });

                // For Complaint Item Report
                Route::prefix('report/complaint_item_report')->controller(ComplaintItemController::class)->group(function () {
                    Route::get('/', 'index')->name('complaint_item_report.index');
                    Route::get('/{item}', 'show')->name('complaint_item_report.show');
                    Route::delete('/{item}', 'destroy')->name('complaint_item_report.destroy');
                    Route::put('/{item}', 'statusChange')->name('complaint_item_report.statusChange');
                    Route::get('/csv/export', 'csvExport')->name('complaint_item_report.csv.export');
                    Route::put('/screen-display-ui-setting', "screenDisplayUiStore")->name("complaint_item_report.screenDisplayUiSetting.store");
                });

                // For Item Approval
                Route::prefix('approval')->group(function () {
                    // For Pending Items
                    Route::controller(PendingController::class)->group(function () {
                        Route::get('/pending', 'index')->name('pending_item.index');
                        Route::delete('/pending/{item}', 'destroy')->name('pending_item.destroy');
                        Route::get('/pending/{item}/edit', 'edit')->name('pending_item.edit');
                        Route::put('/pending/{item}', 'statusChange')->name('pending_item.statusChange');
                    });

                    // For Disable Items
                    Route::controller(DisableController::class)->group(function () {
                        Route::get('/disable', 'index')->name('disable_item.index');
                        Route::delete('/disable/{item}', 'destroy')->name('disable_item.destroy');
                        Route::get('/disable/{item}/edit', 'edit')->name('disable_item.edit');
                        Route::put('/disable/{item}', 'statusChange')->name('disable_item.statusChange');
                    });

                    // For Reject Items
                    Route::controller(RejectController::class)->group(function () {
                        Route::get('/reject', 'index')->name('reject_item.index');
                        Route::delete('/reject/{item}', 'destroy')->name('reject_item.destroy');
                        Route::get('/reject/{item}', 'edit')->name('reject_item.edit');
                        Route::put('/reject/{item}', 'statusChange')->name('reject_item.statusChange');
                    });
                });
            });
            Route::resource('/item', ItemController::class);

            // Sponsored Items
            Route::prefix('item')->controller(PaidItemController::class)->group(function () {
                Route::get('promote/{item}', 'promote')->name('item.promote');
            });
            Route::prefix('report')->controller(PaidItemController::class)->group(function () {
                Route::get('/paid_item/csv/export', 'paidItemReportCsvExport')->name('paid_item_report.csv.export');
            });
            Route::resource('paid_item', PaidItemController::class);
            Route::resource('offline_paid_item', OfflinePaidItemController::class);

            // For Banned User
            Route::prefix('banned_user')->controller(BannedUserController::class)->group(function () {
                Route::put('/ban/{banned_user}', 'ban')->name('banned_user.ban');
                // Route::get('/{user}', 'edit')->name('banned_user.show');
                Route::post('/screen-display-ui-setting', "screenDisplayUiStore")->name("banned_user.screenDisplayUiSetting.store");
            });
            Route::resource('/banned_user', BannedUserController::class);

            // For User
            Route::prefix('users')->group(function () {

                Route::controller(UserController::class)->group(function () {
                    Route::post('/screen-display-ui-setting', "screenDisplayUiStore")->name("user.screenDisplayUiSetting.store");

                    Route::put('/status/{user}', 'statusChange')->name('user.statusChange');
                    Route::put('/ban/{user}', 'ban')->name('user.ban');

                    // For Profile
                    Route::get('/profile/{user}', 'profileEdit')->name('user.profile.edit');
                    Route::put('/profile/{user}', 'profileUpdate')->name('user.profile.update');
                    Route::delete('/profile/{user}/delete', 'deleteImage')->name('user.image.delete');
                    Route::post('/profile/{user}/replace', 'replaceImage')->name('user.image.replace');
                });

                // For User Report
                Route::prefix('report')->controller(UserReportController::class)->group(function () {
                    Route::get('/buyer_report', 'buyerReportIndex')->name('buyer_report.index');
                    Route::get('/buyer_report/{user}', 'buyerReportShow')->name('buyer_report.show');
                    Route::get('/buyer_report/csv/export', 'buyerReportCsvExport')->name('buyer_report.csv.export');

                    Route::get('/seller_report', 'sellerReportIndex')->name('seller_report.index');
                    Route::get('/seller_report/{user}', 'sellerReportShow')->name('seller_report.show');
                    Route::get('/seller_report/csv/export', 'sellerReportCsvExport')->name('seller_report.csv.export');

                    Route::get('/user_report', 'userReportIndex')->name('user_report.index');
                    Route::get('/user_report/{user}', 'userReportShow')->name('user_report.show');
                    Route::get('/user_report/csv/export', 'userReportCsvExport')->name('user_report.csv.export');

                    Route::get('/daily_active_user_report', 'dailyActiveUserReportIndex')->name('daily_active_user_report.index');
                    Route::get('/daily_active_user_report/{user}', 'dailyActiveUserReportShow')->name('daily_active_user_report.show');
                    Route::get('/daily_active_user_report/csv/export', 'dailyActiveUserReportCsvExport')->name('daily_active_user_report.csv.export');
                });

                // User Role
                Route::resource('/user_role', RoleController::class);
                Route::controller(RoleController::class)->group(function () {
                    Route::put('/role/status/{item}', 'statusChange')->name('user_role.statusChange');
                    Route::put('/role/admin-panel-access/{item}', 'updateAdminPanelAccess')->name('user_role.updateAdminPanelAccess');
                    Route::put('/screen-display-ui-setting', "screenDisplayUiStore")->name("user_role.screenDisplayUiSetting.store");
                });

                // Vendor Role
                Route::middleware(["isVendorSettingOn"])->group(function () {
                    Route::resource('/vendor_role', VendorRoleController::class);
                    Route::controller(VendorRoleController::class)->group(function () {
                        Route::put('/vendor_role/status/{item}', 'statusChange')->name('vendor_role.statusChange');
                        Route::put('/vendor_role/admin-panel-access/{item}', 'updateAdminPanelAccess')->name('vendor_role.updateAdminPanelAccess');
                        Route::put('/vendor_role/screen-display-ui-setting', "screenDisplayUiStore")->name("vendor_role.screenDisplayUiSetting.store");
                    });
                });

            });
            Route::resource('/user', UserController::class);

            // For Backend Setting
            Route::prefix('backend_setting')->controller(BackendSettingController::class)->group(function () {
                Route::get('/', 'index')->name('backend_setting.index');
                Route::post('/', 'store')->name('backend_setting.store');
                Route::put('/{backend_setting}', 'update')->name('backend_setting.update');
                Route::get('/checkSmtpConfig', 'checkSmtpConfig')->name('backend_setting.checkSmtpConfig');
                Route::get('/languageRefresh', 'languageRefresh')->name('backend_setting.languageRefresh');
            });

            // For License
            Route::prefix('license')->controller(LicenseController::class)->group(function () {
                Route::get('/', 'index')->name('license.index');
                Route::post('/', 'store')->name('license.store');
                Route::put('/{license}', 'update')->name('license.update');
            });

            // For Mobile Setting
            // Route::prefix('mobile_setting')->controller(MobileSetting::class)->group(function (){
            //     Route::get('/', 'index')->name('mobile_setting.index');
            //     Route::post('/', 'store')->name('mobile_setting.store');
            //     Route::put('/{mobile_setting}', 'update')->name('mobile_setting.update');
            // });


            // For Frontend Setting
            Route::prefix('frontend_setting')->controller(FrontendSettingController::class)->group(function () {
                Route::get('/', 'index')->name('frontend_setting.index');
                Route::post('/', 'store')->name('frontend_setting.store');
                Route::post('/{frontend_setting}', 'update')->name('frontend_setting.update');
                Route::get('/languageRefresh', 'languageRefresh')->name('frontend_setting.languageRefresh');
                Route::put('/colorGenerate', 'colorGenerate')->name('frontend_setting.colorGenerate');
            });

            // For Mobile Setting
            Route::prefix('mobile_setting')->controller(MobileSettingController::class)->group(function () {
                Route::get('/', 'index')->name('mobile_setting.index');
                Route::post('/', 'store')->name('mobile_setting.store');
                Route::get('/test', 'test')->name('mb.test');
                Route::post('/{mobile_setting}', 'update')->name('mobile_setting.update');
            });

            // For Builder Setting
            Route::prefix('builder_setting')->controller(BuilderSettingController::class)->group(function () {
                Route::get('/', 'index')->name('builder_setting.index');
                Route::post('/', 'store')->name('builder_setting.store');
                Route::post('/{builder_setting}', 'update')->name('builder_setting.update');
            });


            // For System Config
            Route::prefix('system_config')->controller(SystemConfigController::class)->group(function () {
                Route::get('/', 'index')->name('system_config.index');
                Route::post('/', 'store')->name('system_config.store');
                Route::put('/{system_config}', 'update')->name('system_config.update');
            });

            // For Deeplink Generator
            Route::prefix('deeplink_generator')->controller(DeeplinkController::class)->group(function () {
                Route::get('/', 'index')->name('deeplink_generator.index');
                Route::put('/', 'deeplink')->name('deeplink_generator.update');
            });

            // For Contact Us Message
            Route::prefix('contact')->controller(ContactUsMessageController::class)->group(function () {
                Route::get('/{contact}', 'show')->name('contact.show');
                Route::get('/csv/export', 'csvExport')->name('contact.csv.export');
                Route::get('/getContact/title', 'getContactFormTitle')->name('contact.getContactFormTitle');
                Route::put('/allasread', 'markAllAsRead')->name('contact.allasread');
                Route::delete('/multidelete', 'multipleDelete')->name('contact.multiDelete');
            });
            Route::resource('/contact', ContactUsMessageController::class);


            // For Blog
            Route::prefix('blog')->controller(BlogController::class)->group(function () {
                Route::put('/status/{blog}', 'statusChange')->name('blog.statusChange');
                Route::get('/gallery/{blog}', 'gallery')->name('blog.gallery');
                Route::post('/gallery/{blog}', 'galleryUpload')->name('blog.gallery.upload');
                Route::put('/screen-display-ui-setting', "screenDisplayUiStore")->name("blog.screenDisplayUiSetting.store");
            });
            Route::resource('/blog', BlogController::class);

            // For Package
            Route::resource('offline_package', OfflinePackageController::class);
            Route::resource('/package', PackageController::class);

            // For Package Report
            Route::prefix('report')->controller(PackageReportController::class)->group(function () {
                Route::get('/package_report', 'index')->name('package_report.index');
                Route::get('/package_report/{package}', 'show')->name('package_report.show');
                Route::get('/package_report/csv/export', 'csvExport')->name('package_report.csv.export');
            });

            // For Data Deletion Policy
            Route::prefix('data_deletion_policy')->controller(DataDeletionPolicyController::class)->group(function () {
                Route::get('/', 'index')->name('data_deletion_policy.index');
                Route::post('/', 'store')->name('data_deletion_policy.store');
                Route::put('/{data_deletion_policy}', 'update')->name('data_deletion_policy.update');
            });


            // For Core Key Type
            Route::prefix('core_key_type')->controller(CoreKeyTypeController::class)->group(function () {
                Route::get('/', 'index')->name('core_key_type.index');
                Route::get('/create', 'create')->name('core_key_type.create');
                Route::post('/', 'store')->name('core_key_type.store');
            });

            // For Data Deletion Policy
            Route::prefix('data_deletion_policy')->controller(DataDeletionPolicyController::class)->group(function () {
                Route::get('/', 'index')->name('data_deletion_policy.index');
                Route::post('/', 'store')->name('data_deletion_policy.store');
                Route::put('/{data_deletion_policy}', 'update')->name('data_deletion_policy.update');
                Route::post('/ckupload', 'ckUpload')->name('data_deletion_policy.ckUpload');
            });

            // For Payment
            Route::prefix('payment')->controller(PaymentController::class)->group(function () {
                Route::put('/status/{payment}', 'statusChange')->name('payment.statusChange');
                Route::put('/screen-display-ui-setting', "screenDisplayUiStore")->name("payment.screenDisplayUiSetting.store");
            });
            Route::resource('/payment', PaymentController::class);

            // For Payment Core Key
            Route::prefix('payment/{payment}/core_key')->controller(PaymentCoreKeyController::class)->group(function () {
                Route::get('/create', 'create')->name('payment_core_key.create');
                Route::post('/', 'store')->name('payment_core_key.store');
                Route::get('/{core_key}/edit', 'edit')->name('payment_core_key.edit');
                Route::put('/{core_key}', 'update')->name('payment_core_key.update');
            });

            // For BlueMark
            Route::resource('/bluemarkuser', BlueMarkUserController::class);

            // For Promotion In App Purchase Setting
            Route::prefix('promotion_in_app_purchase')->controller(PromotionInAppPurchaseSettingController::class)->group(function () {
                Route::put('/status/{promotion_in_app_purchase}', 'statusChange')->name('promotion_in_app_purchase.statusChange');
            });
            Route::resource('/promotion_in_app_purchase', PromotionInAppPurchaseSettingController::class);

            // For Promotion In App Purchase Setting
            Route::prefix('package_in_app_purchase')->controller(PackageInAppPurchaseSettingController::class)->group(function () {
                Route::put('/status/{package_in_app_purchase}', 'statusChange')->name('package_in_app_purchase.statusChange');
            });
            Route::resource('/package_in_app_purchase', PackageInAppPurchaseSettingController::class);

            // For Vendor Subscription Plan Setting
            Route::prefix('vendor_subscription_plan')->controller(VendorSubscriptionPlanSettingController::class)->group(function () {
                Route::put('/status/{vendor_subscription_plan}', 'statusChange')->name('vendor_subscription_plan.statusChange');
                Route::put('/handle-is-most-popular-plan/{vendor_subscription_plan}', 'handleIsMostPopularPlan')->name('vendor_subscription_plan.handleIsMostPopularPlan');
            });
            Route::resource('/vendor_subscription_plan', VendorSubscriptionPlanSettingController::class);

            // For Offline Payment Setting
            Route::prefix('offline_payment_setting')->controller(OfflinePaymentSettingController::class)->group(function () {
                Route::put('/status/{offline_payment_setting}', 'statusChange')->name('offline_payment_setting.statusChange');
            });
            Route::resource('/offline_payment_setting', OfflinePaymentSettingController::class);

            // For Payment Setting
            Route::prefix('payment_setting')->controller(PaymentSettingController::class)->group(function () {
                Route::get('/', 'index')->name('payment_setting.index');
                Route::post('/{payment_setting}', 'store')->name('payment_setting.store');
            });

            // For Push noti message
            Route::prefix('push_notification_message')->controller(PushNotificationMessageController::class)->group(function () {
                Route::put('/screen-display-ui-setting', "screenDisplayUiStore")->name("push_notification_message.screenDisplayUiSetting.store");
            });
            Route::resource('push_notification_message', PushNotificationMessageController::class);

            // For Thumbnail Generator
            Route::prefix('thumbnail_generator')->controller(ThumbnailGeneratorController::class)->group(function () {
                Route::get('/', 'index')->name('thumbnail_generator.index');
                Route::put('/', 'thumbnail')->name('thumbnail_generator.update');
            });

            // For Image Lists
            Route::prefix('image_lists')->controller(ThumbnailGeneratorController::class)->group(function () {
                Route::get('/', 'imageListIndex')->name('image_lists.index');
                Route::put('/{image}', 'imageListUpdate')->name('image_lists.update');
            });


//            // For Table
//            Route::prefix("table")->name("table.")->controller(TableController::class)->group(function () {
//                Route::get("/replace", "replaceFromBuilder")->name("replace");
//                Route::get("/do_replace", "doReplaceFromBuilder")->name("do_replace");
//                Route::post("/import", "import")->name("import.zip");
//                Route::post("/override", "override")->name("field.override");
//
//                Route::post("/handle-project-not-same", "handleProjectNotSame")->name("field.handleProjectNotSame");
//            });

            Route::prefix('table')->name("table.")->group(function () {
                Route::get('/replace', [TableController::class, 'replaceFromBuilder'])->name("replace");
                Route::get('/do_replace', [TableController::class, 'doReplaceFromBuilder'])->name("do_replace");
                Route::post('/import', [TableController::class, 'import'])->name("import.zip");
                Route::post('/override', [TableController::class, 'override'])->name("field.override");
                Route::post('/handle-project-not-same', [TableController::class, 'handleProjectNotSame'])->name("field.handleProjectNotSame");
            });
            Route::resource("table", TableController::class)->only(['index']);

            // For Field ( Core And Custom )
            Route::prefix('tables.fields')->group(function () {
                Route::controller(CoreAndCustomFieldController::class)->group(function () {
                    Route::post('/is-show-sorting', 'handleIsShowSorting')->name('tables.fields.isShowSorting');
                    Route::post('/enable', 'enableChange')->name('tables.fields.enableChange');
                    Route::post('/mandatory', 'mandatoryChange')->name('tables.fields.mandatoryChange');
                    Route::post('/eyeStstus', 'eyeStatusChange')->name('tables.fields.eyeStatusChange');
                    Route::put('/deleteField', 'deleteField')->name('tables.fields.deleteField');
                    Route::put('/handle-ordering', 'handleOrdering')->name('tables.fields.handleOrdering');
                    Route::put('/screen-display-ui-setting', "screenDisplayUiStore")->name("tables.fields.screenDisplayUiSetting.store");
                });
            });
            Route::resource("tables.fields", CoreAndCustomFieldController::class);

            // For Customize Ui Detail
            Route::prefix('tables.fields.attributes')->controller(CustomizeUiDetailController::class)->group(function () {
                Route::put('/screen-display-ui-setting', "screenDisplayUiStore")->name("attribute.screenDisplayUiSetting.store");
            });
            Route::resource("tables.fields.attributes", CustomizeUiDetailController::class, ['names' => "attribute"]);

            // For Slow Moving Item
            // Route::prefix('slow_moving_item')->controller(SlowMovingItemController::class)->group(function () {
            //     Route::get('/', 'index')->name('slow_moving_item.index');
            //     Route::get('/{item}', 'edit')->name('slow_moving_item.edit');
            //     Route::delete('/{item}', 'destroy')->name('slow_moving_item.destroy');
            // });

            // For Slow Moving Item Report
            Route::prefix('slow_moving_item_report')->controller(SlowMovingItemReportController::class)->group(function () {
                Route::get('/', 'index')->name('slow_moving_item_report.index');
                Route::get('/{item}', 'show')->name('slow_moving_item_report.show');
                Route::get('/csv/export', 'csvExport')->name('slow_moving_item_report.csv.export');
            });


            // For Demo Data Deletion
            Route::prefix('demo_data_deletion')->controller(DemoDataDeletionController::class)->group(function () {
                Route::get('/', 'index')->name('demo_data_deletion.index');
                Route::put('/', 'destroy')->name('demo_data_deletion.destroy');
            });

            // For Slow Moving Item
            Route::resource("slow_moving_item", SlowMovingItemController::class);


            // For Available Currency
            Route::prefix('available_currency')->controller(AvailableCurrencyController::class)->group(function () {
                Route::put('/status/{available_currency}', 'statusChange')->name('available_currency.statusChange');
                Route::put('/available_currency/default/{available_currency}', 'defaultChange')->name('available_currency.defaultChange');
                Route::put('/screen-display-ui-setting', "screenDisplayUiStore")->name("available_currency.screenDisplayUiSetting.store");
            });
            Route::resource('/available_currency', AvailableCurrencyController::class);


            // For Phone Country Code
            Route::prefix('phone_country_code')->controller(PhoneCountryCodeController::class)->group(function () {
                Route::put('/status/{phone_country_code}', 'statusChange')->name('phone_country_code.statusChange');
                Route::put('/phone_country_code/default/{phone_country_code}', 'defaultChange')->name('phone_country_code.defaultChange');
                Route::put('/screen-display-ui-setting', "screenDisplayUiStore")->name("phone_country_code.screenDisplayUiSetting.store");
            });
            Route::resource('/phone_country_code', PhoneCountryCodeController::class);
        });

        // For API Token
        Route::prefix('api_token')->controller(ApiTokenController::class)->group(function () {
            Route::put('/screen-display-ui-setting', "screenDisplayUiStore")->name("api_token.screenDisplayUiSetting.store");
            Route::post('/default_create_token', 'defaultTokenCreating')->name('api_token.default_creating_token');
        });
        Route::resource('/api_token', ApiTokenController::class);

        // mobile color
        Route::resource('/color', ColorController::class);
    });

    Route::prefix('item')->group(function () {
        Route::controller(ProductApiController::class)->group(function () {

            Route::post('/upload-multi-images', 'uploadMulti');
        });
    });
    // End Admin Route

});

Route::get('/generate-npm', function () {

    $pathArr = findFindWithHashKey(public_path(ps_constant::appJSFilePath));
    if (count($pathArr) !== 0) {
        $path_to_file = $pathArr[0];
    }

    $pathArr2 = findFindWithHashKey(public_path(ps_constant::PsApiServiceJSFilePath));
    if (count($pathArr2) !== 0) {
        $path_to_file2 = $pathArr2[0];
    }

    $pathArr6 = findFindWithHashKey(public_path(ps_constant::psApiServiceJSFilePath));
    if (count($pathArr6) !== 0) {
        $path_to_file6 = $pathArr6[0];
    }


    // app.js
    if (config('app.dir') === '') {
        // dd(ps_constant::searchDomain.ps_constant::searchSubFolder.'/');
        findAndReplaceForBuildFolder($path_to_file, ps_constant::searchDomain . ps_constant::searchSubFolder . '/', config("app.base_domain"));
        findAndReplaceForBuildFolder($path_to_file, ps_constant::searchSubFolder, config("app.dir"));
        findAndReplaceForBuildFolder($path_to_file, ps_constant::searchDomain, config("app.base_domain"));
    } else {
        findAndReplaceForBuildFolder($path_to_file, ps_constant::searchDomain, config("app.base_domain"));
        findAndReplaceForBuildFolder($path_to_file, ps_constant::searchSubFolder, config("app.dir"));
    }

    // findAndReplaceForBuildFolder($path_to_file, ps_constant::searchSubFolder, config("app.dir"));


    //psApiService2.js
    if (config('app.dir') === '') {
        findAndReplaceForBuildFolder($path_to_file2, '/' . ps_constant::searchSubFolder, config("app.dir"));
    } else {
        findAndReplaceForBuildFolder($path_to_file2, ps_constant::searchSubFolder, config("app.dir"));
    }


    //psApiService.js

    findAndReplaceForBuildFolder($path_to_file6, ps_constant::searchSubFolder, config("app.dir"));


    dd("complete");
});


Route::prefix('item')->group(function () {
    Route::controller(ItemController::class)->group(function () {
        Route::post('/remove-multi', 'removeMulti')->name('item.removeMulti');
    });
});


Route::get('phpmyinfo', function () {
    phpinfo();
})->name('phpmyinfo');


Route::get('/repair-vue', function () {
    repairVue();
    return view('cache-clear');
});

Route::get("/repair-domain", function () {
    $domainChange = DomainChange::first();
    $domain = config("app.base_domain");
    $subFolder = config("app.dir");
    if (empty($domainChange)) {
        $newDomainChange = new DomainChange();
        $newDomainChange->domain_name = $domain;
        $newDomainChange->sub_folder = $subFolder;
        $newDomainChange->save();
    } else {
        $domainChange->domain_name = $domain;
        $domainChange->sub_folder = $subFolder;
        $domainChange->update();
    }
    return redirect()->route("dashboard");
});
