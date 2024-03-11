<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Entities\BackendSetting;
use Modules\Core\Entities\MobileSetting;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $backendSettings = BackendSetting::first();
        if($backendSettings){
            $backendSettings->backend_version_no = "1.4.1";
            $backendSettings->update();
        }

        $mobileSetting = MobileSetting::first();
        if($mobileSetting){
            $mobileSetting->version_no = "1.4.1";
            $mobileSetting->update();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('', function (Blueprint $table) {

        });
    }
};
