<?php

namespace App\Providers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use League\CommonMark\Environment\Environment;
use Modules\Core\Entities\BackendSetting;
use Modules\Payment\Entities\PaymentInfo;
use Modules\Core\Constants\Constants;
use App\Providers\Braintree_Configuration;
use Config;
use Exception;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        if (App::environment("production")) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // app('debugbar')->enable();
        Paginator::useBootstrap();
        JsonResource::withoutWrapping();

        try {
            if (DB::connection()->getPdo()) {
                $mailSetting = BackendSetting::first();
                if ($mailSetting) {
                    $data = [
                        'driver' => 'smtp',
                        'host' => $mailSetting->smtp_host,
                        'port' => $mailSetting->smtp_port,
                        'encryption' => $mailSetting->smtp_encryption,
                        'username' => $mailSetting->smtp_user,
                        'password' => $mailSetting->smtp_pass,
                        'pretend' => false,
                        'verify_peer' => false,
                        'from' => [
                            'address' => $mailSetting->sender_email,
                            'name' => $mailSetting->sender_name,
                        ]
                    ];
                    Config::set('mail', $data);
                }
            }
        } catch (Exception $e) {
            // echo "Unable to connect";
        }
    }
}
