<?php

namespace Modules\ComplaintItem\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class ComplaintItemStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('psx_reported_item_statuses')->insert([
            'title' => 'Open',
            'added_user_id' => '1',
            'added_date'=>Carbon::now(),
        ]);

        DB::table('psx_reported_item_statuses')->insert([
            'title' => 'Close',
            'added_user_id' => '1',
            'added_date'=>Carbon::now(),
        ]);
    }
}
