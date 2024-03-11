<?php

namespace App\Providers;

use App\Models\Team;
use App\Policies\ApiTokenPolicy;
use App\Policies\PsPolicy;
use App\Policies\PsVendorPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

use Modules\Core\Entities\CoreMenu;
use Modules\ComplaintItem\Policies\ComplaintItemPolicy;
use Modules\Core\Policies\CategoryPolicy;
use Modules\Core\Policies\CoreKeyTypePolicy;
use Modules\Core\Policies\ItemPolicy;
use Modules\Core\Policies\RolePolicy;
use Modules\Core\Policies\LanguagePolicy;
use Modules\Core\Policies\LanguageStringPolicy;

use Modules\Core\Policies\ModulePolicy;
use Modules\Core\Policies\SubcategoryPolicy;
use Modules\Payment\Policies\PaymentPolicy;
use Modules\Core\Policies\AvailableCurrencyPolicy;
use Modules\Blog\Policies\BlogPolicy;
use Modules\Core\Policies\PhoneCountryCodePolicy;
use Modules\Core\Policies\MobileSettingPolicy;
use Modules\Core\Policies\CoreMenuGroupPolicy;
use Modules\Core\Policies\SystemConfigPolicy;
use Modules\Core\Policies\CoreSubMenuGroupPolicy;
use Modules\Core\Policies\CoreMenuPolicy;
use Modules\Core\Policies\CurrencyPolicy;
use Modules\Core\Policies\LocationCityPolicy;
use Modules\Core\Policies\LocationTownshipPolicy;
use Modules\Core\Policies\MobileLanguagePolicy;
use Modules\Core\Policies\MobileLanguageStringPolicy;
use Modules\ContactUsMessage\Policies\ContactPolicy;
use Modules\PushNotificationMessage\Policies\PushNotificationMessagePolicy;
use Modules\Core\Policies\TablePolicy;
use Modules\Core\Policies\CustomizeUiDetailPolicy;
use Modules\Core\Policies\CorePrivacyPolicyPolicy;
use Modules\Core\Policies\FeLanguageStringPolicy;
use Modules\Core\Policies\VendorLanguageStringPolicy;
use Modules\Core\Policies\VendorMenuGroupPolicy;
use Modules\Core\Policies\VendorMenuPolicy;
use Modules\Core\Policies\VendorModulePolicy;
use Modules\Core\Policies\VendorRolePolicy;
use Modules\Core\Policies\VendorSubMenuGroupPolicy;
use Modules\Core\Policies\VendorPolicy;
use Modules\Core\Policies\VendorItemPolicy;

use Modules\DataDeletionPolicy\Policies\CoreDataDeletionPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Team::class => [
            'vendor_policy' => PsVendorPolicy::class,
            'ps_policy' => PsPolicy::class,
        ],
        
//        Product::class => ItemPolicy::class,
//        Subcategory::class => SubcategoryPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // product
//        Gate::define('product', function () {
//            return authorization(1);
//        });
        Gate::define('create-item', [ItemPolicy::class, 'create']);
        Gate::define('create-language', [LanguagePolicy::class, 'create']);
        Gate::define('create-languageString', [LanguageStringPolicy::class, 'create']);

        Gate::define('create-module', [ModulePolicy::class, 'create']);

        //sub category
//        Gate::define('sub-category',function (){
//            return authorization(6);
//        });


        Gate::define('create-subCategory', [SubcategoryPolicy::class, 'create']);
        Gate::define('create-category', [CategoryPolicy::class, 'create']);
        Gate::define('create-user', [UserPolicy::class, 'create']);
        Gate::define('create-role', [RolePolicy::class, 'create']);
        Gate::define('create-payment', [PaymentPolicy::class, 'create']);
        Gate::define('create-coreKeyType', [CoreKeyTypePolicy::class, 'create']);
        Gate::define('create-currency', [CurrencyPolicy::class, 'create']);
        // Gate::define('create-mobileSetting', [MobileSettingPolicy::class, 'create']);
        Gate::define('create-complaintItem', [ComplaintItemPolicy::class, 'create']);
        Gate::define('create-apiToken', [ApiTokenPolicy::class, 'create']);

//        Gate::define('delete-product', [ProductPolicy::class, 'delete']);
//        Gate::define('update-product', [ProductPolicy::class, 'update']);

        Gate::define('create-availableCurrency', [AvailableCurrencyPolicy::class, 'create']);
        Gate::define('create-mobileSetting', [MobileSettingPolicy::class, 'create']);
        Gate::define('create-coreMenuGroup', [CoreMenuGroupPolicy::class, 'create']);
        Gate::define('create-systemConfig', [SystemConfigPolicy::class, 'create']);
        Gate::define('create-coreSubMenuGroup', [CoreSubMenuGroupPolicy::class, 'create']);
        Gate::define('create-coreModule', [CoreMenuPolicy::class, 'create']);
        Gate::define('create-privacyModule', [CorePrivacyPolicyPolicy::class, 'create']);
        Gate::define('create-dataDeletionModule', [CoreDataDeletionPolicy::class, 'create']);



        Gate::define('create-currency', [CurrencyPolicy::class, 'create']);
        Gate::define('create-locationCity', [LocationCityPolicy::class, 'create']);
        Gate::define('create-locationTownship', [LocationTownshipPolicy::class, 'create']);
        Gate::define('create-contactUsMessage', [ContactPolicy::class, 'create']);
        Gate::define('create-pushNotificationMessage', [PushNotificationMessagePolicy::class, 'create']);
        Gate::define('create-mobileLanguage', [MobileLanguagePolicy::class, 'create']);
        Gate::define('create-mobileLanguageString', [MobileLanguageStringPolicy::class, 'create']);

        Gate::define('create-blog', [BlogPolicy::class, 'create']);

        Gate::define('create-phoneCountryCode', [PhoneCountryCodePolicy::class, 'create']);




        Gate::define('create-table', [TablePolicy::class, 'create']);
        Gate::define('create-customizeUiDetail', [CustomizeUiDetailPolicy::class, 'create']);

        // for vendor
        Gate::define('create-vendor', [VendorPolicy::class, 'create']);

        Gate::define('create-vendorItem', [VendorItemPolicy::class, 'create']);

        // for frontend language string
        Gate::define('create-feLanguageString',[FeLanguageStringPolicy::class,'create']);
        Gate::define('create-vendorLanguageString',[VendorLanguageStringPolicy::class,'create']);
        Gate::define('create-vendorMenuGroup',[VendorMenuGroupPolicy::class,'create']);
        Gate::define('create-vendorMenu',[VendorMenuPolicy::class,'create']);
        Gate::define('create-vendorModule',[VendorModulePolicy::class,'create']);
        Gate::define('create-vendorRole',[VendorRolePolicy::class,'create']);
        Gate::define('create-vendorSubMenuGroup',[VendorSubMenuGroupPolicy::class,'create']);
    }
}
