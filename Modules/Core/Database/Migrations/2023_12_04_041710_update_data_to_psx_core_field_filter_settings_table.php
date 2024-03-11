<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Entities\CoreFieldFilterSetting;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $coreFieldFilterSettings = CoreFieldFilterSetting::where(CoreFieldFilterSetting::fieldName, 'is_show_email')->first();
        $coreFieldFilterSettings->enable = 1;
        $coreFieldFilterSettings->update();

        $coreFieldFilterSettings = CoreFieldFilterSetting::where(CoreFieldFilterSetting::fieldName, 'is_show_phone')->first();
        $coreFieldFilterSettings->enable = 1;
        $coreFieldFilterSettings->update();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('psx_core_field_filter_settings', function (Blueprint $table) {

        });
    }
};
