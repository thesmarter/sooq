<?php

namespace Modules\ComplaintItem\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class ComplaintItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        for ($x = 1; $x <= 10; $x++) {
            DB::table('psx_item_reports')->insert([
                'item_id' => $x,
                'reported_user_id' => $x,
                'text_note' => 'note',
                'reported_item_status_id' => 1,
                'added_user_id' => '1',
                'added_date'=>Carbon::now(),
            ]);
        }

    }
}
