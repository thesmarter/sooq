<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('psx_modules', function (Blueprint $table) {
            $table->after("title", function ($table){
                $table->tinyInteger("status");
                $table->string("route_name");
                $table->integer("menu_id")->default(null);
                $table->integer("sub_menu_id")->default(null);
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('psx_modules', function (Blueprint $table) {
            $table->dropColumn(["menu_id", "sub_menu_id", "status", "route_name"]);
        });
    }
};
